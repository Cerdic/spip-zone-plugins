<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

// Constantes pour connexion à Sphinx
defined('SPHINX_SERVER_HOST')   || define('SPHINX_SERVER_HOST', '127.0.0.1');
defined('SPHINX_SERVER_PORT')   || define('SPHINX_SERVER_PORT', 9306);
defined('SPHINX_DEFAULT_INDEX') || define('SPHINX_DEFAULT_INDEX', 'spip');

// Charge les classes possibles de l'indexer
if (!class_exists('Composer\\Autoload\\ClassLoader')) {
	require_once _DIR_PLUGIN_INDEXER . 'lib/Composer/Autoload/ClassLoader.php';
}

$loader = new \Composer\Autoload\ClassLoader();

// register classes with namespaces
$loader->add('Indexer', _DIR_PLUGIN_INDEXER . 'lib');
$loader->add('Sphinx',  _DIR_PLUGIN_INDEXER . 'lib');
$loader->addPsr4('Spip\\Indexer\\Sources\\',  _DIR_PLUGIN_INDEXER . 'Sources');

$loader->register();

/**
 * Renvoyer un indexeur configuré avec un (et peut-être un jour plusieurs) lieu de stockage des choses à indexer
 *
 * Par défaut renvoie l'indexeur avec le stockage Sphinx intégré en interne et les paramètres des define()
 * 
 * @pipeline_appel indexer_indexer
 * @return Retourne un objet Indexer, ayant été configuré avec la méthode registerStorage()
 */
function indexer_indexer(){
	static $indexer = null;
	
	if (is_null($indexer)){
		// On crée un indexeur
		$indexer = new Indexer\Indexer();
	
		// On tente de le configurer avec Sphinx et les define()
		try {
			$indexer->registerStorage(
				new Indexer\Storage\Sphinx(
					new Sphinx\SphinxQL\SphinxQL(SPHINX_SERVER_HOST, SPHINX_SERVER_PORT),
					SPHINX_DEFAULT_INDEX
				)
			);
		} catch( \Exception $e ) {
			if (!$message = $e->getMessage()) {
				$message = _L('Erreur inconnue');
			}
			die("<p class='erreur'>$message</p>");
		}
		
		// On le fait passer dans un pipeline
		$indexer = pipeline('indexer_indexer', $indexer);
	}
	
	return $indexer;
}

/**
 * Renvoyer les sources de données disponibles dans le site
 * 
 * Un pipeline "indexer_sources" est appelée avec la liste par défaut, permettant de retirer ou d'ajouter des sources.
 *
 * @pipeline_appel indexer_sources
 * @return Sources Retourne un objet Sources listant les sources enregistrées avec la méthode register()
 */
function indexer_sources(){
	static $sources = null;
	
	if (is_null($sources)){
		include_spip('base/objets');
		include_spip('inc/config');
		
		// On crée la liste des sources
		$sources = new Indexer\Sources\Sources();
		
		// On ajoute chaque objet configuré aux sources à indexer
		// Par défaut on enregistre les articles s'il n'y a rien
		foreach (lire_config('indexer/sources_objets', array('spip_articles')) as $table) {
			if ($table) {
				$sources->register(
					table_objet($table),
					new Spip\Indexer\Sources\SpipDocuments(objet_type($table))
				);
			}
		}
		
		// Toute la hiérarchie des rubriques
		$sources->register('hierarchie_rubriques', new Spip\Indexer\Sources\HierarchieRubriques());
		
		// On passe les sources dans un pipeline
		$sources = pipeline('indexer_sources', $sources);
	}
	
	return $sources;
}

/**
 * Indexer une partie des documents d'une source.
 * 
 * Cette fonction est utilisable facilement pour programmer un job.
 *
 * @param string $alias_de_sources Alias donné à la source enregistrée dans les Sources du site
 * @param mixed $start Début des documents à indexer
 * @param mixed $end Fin des documents à indexer
 * @return void
 */
function indexer_job_indexer_source($alias_de_sources, $start, $end){
	// On va chercher l'indexeur du SPIP
	$indexer = indexer_indexer();
	// On va chercher les sources du SPIP
	$sources = indexer_sources();
	// On récupère celle demandée en paramètre
	$source = $sources->getSource($alias_de_sources);
	// On va chercher les documents à indexer
	$documents = $source->getDocuments($start, $end);
	// Et on le remplace (ou ajoute) dans l'indexation
	$res = $indexer->replaceDocuments($documents);

	// en cas d'erreur, on se reprogramme pour une autre fois
	if (!$res) {
		job_queue_add(
			'indexer_job_indexer_source',
			"Replan indexation $alias_de_sources $start $end",
			array($alias_de_sources, $start, $end, uniqid()),
			'inc/indexer',
			false, // duplication possible car ce job-ci n'est pas encore nettoyé
			time() + 15*60, // attendre 15 minutes
			-10 // priorite basse
		);
	}
}




function indexer_statistiques_indexes_depuis($date_reference = null) {
	if (!$date_reference)
		$date_reference = intval(lire_meta('indexer_derniere_reindexation'));

	$query = "SELECT COUNT(*) AS c,
	properties.objet AS objet,
	(date_indexation > $date_reference) AS recent,
	properties.source AS source
	FROM " . SPHINX_DEFAULT_INDEX . "
	GROUP BY recent, properties.objet, properties.source";

	$sphinx = new Sphinx\SphinxQL\SphinxQL(SPHINX_SERVER_HOST, SPHINX_SERVER_PORT);

	$all = $sphinx->allfetsel($query);

	if (!is_array($all)
	OR !is_array($all['query'])
	OR !is_array($all['query']['docs'])) {
		echo "<p class=error>" . _L('Erreur Sphinx')."</p>";
	} else {
		$liste = [];
		foreach ($all['query']['docs'] as $l) {
			$liste[$l['source']][table_objet($l['objet'])][$l['recent']] = intval($l['c']);
		}
		return $liste;
	}
}



function indexer_tout_reindexer_async($bloc = 100) {

	foreach(indexer_lister_blocs_indexation($bloc) as $alias => $ids) {
		foreach ($ids as $id) {
			$start = $id;
			$end = $id + $bloc-1;
			job_queue_add(
				'indexer_job_indexer_source',
				'Indexer '.$alias.' de '.$start.' à '.$end,
				array($alias, $start, $end),
				'inc/indexer',
				true // pas de duplication
			);
		}
	}

	ecrire_meta('indexer_derniere_reindexation', time());
}

function indexer_lister_blocs_indexation($bloc = 100) {

	// On récupère toutes les sources compatibles avec l'indexation
	$sources = indexer_sources();

	$liste = [];

	foreach ($sources as $alias => $source) {
		// Si une méthode pour définir explicitement existe, on l'utilise
		if (method_exists($source, 'getObjet')){
			$objet_source = $source->getObjet();
		}
		// Sinon on cherche avec l'alias donné à la source
		else {
			$objet_source = objet_type(strtolower($alias));
		}

		// alias = mots, objet_source = mot
		// alias = hierarchie_rubriques, objet_source = rubrique
		$liste[$alias] = [];

		$parts = new \ArrayIterator($source->getParts($bloc));
		
		while ($parts->valid()) {
			$part = $parts->key();
			$liste[$alias][] = $part*$bloc;
			$parts->next();
		}
	}

	return array_filter($liste);
}
