<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

// Constantes pour connexion à Sphinx
defined('SPHINX_SERVER_HOST')   || define('SPHINX_SERVER_HOST', '127.0.0.1');
defined('SPHINX_SERVER_PORT')   || define('SPHINX_SERVER_PORT', 9306);
defined('SPHINX_DEFAULT_INDEX') || define('SPHINX_DEFAULT_INDEX', 'spip');

// Charge les classes possibles de l'indexer
require_once _DIR_PLUGIN_INDEXER . 'lib/Composer/Autoload/ClassLoader.php';

$loader = new \Composer\Autoload\ClassLoader();

// register classes with namespaces
$loader->add('Indexer', _DIR_PLUGIN_INDEXER . 'lib');
$loader->add('Sphinx',  _DIR_PLUGIN_INDEXER . 'lib');
$loader->addPsr4('Spip\\Indexer\\Sources\\',  _DIR_PLUGIN_INDEXER . 'Sources');


$loader->register();

/**
 * Renvoyer les sources de données disponibles dans le site
 * 
 * Un pipeline "indexer_sources" est appelée avec la liste par défaut, permettant de retirer ou d'ajouter des sources.
 *
 * @pipeline_appel insexer_sources
 * @return Sources Retourne un objet Sources listant les sources enregistrées avec la méthode register()
 */
function indexer_sources(){
	static $sources = null;
	
	if (is_null($sources)){
		$sources = new Indexer\Sources\Sources();
		$sources->register('articles', new Spip\Indexer\Sources\Articles());
		$sources = pipeline('indexer_sources', $sources);
	}
	
	return $sources;
}
