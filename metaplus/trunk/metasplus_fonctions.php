<?php
/**
 * Fonctions utiles au plugin Métas+
 *
 * @plugin     Métas+
 * @copyright  2018
 * @author     Tetue, Erational, Tcharlss
 * @licence    GNU/GPL
 * @package    SPIP\Metas+\Pipelines
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Générer le contenu ajouté dans le <head> public
 *
 * On prend le contenu d'un squelette situé dans inclure/metasplus/
 * Il peut s'agir de la variante spécifique à la page en cours si elle existe, sinon à défaut du squelette générique (dist.html).
 * Certaines pages peuvent être exclues en créant une variante vide, ou en les listant dans la constante _METASPLUS_PAGES_EXCLUES (liste de pages séparées par des virgules).
 *
 * @return void
 */
function metasplus_generer_head() {

	// Récupérer le contexte pour connaître la page et l'objet
	$contexte = $GLOBALS['contexte'];
	$page = (isset($contexte['type-page']) ? $contexte['type-page'] : (isset($contexte['page']) ? $contexte['page'] : 'sommaire'));
	// Si c'est un objet éditorial, il y a id_patate dans le contexte
	include_spip('base/objets');
	$id_table_objet = id_table_objet($page);
	if (isset($contexte[$id_table_objet])) {
		$objet    = $page;
		$id_objet = intval($contexte[$id_table_objet]);
	}

	// Les protocoles à ne pas insérer éventuellement
	// On n'a pas de façon programmatique pour lister les protocoles possibles, donc on met leur nombre total en dur (3)
	include_spip('inc/config');
	$config = lire_config('metasplus');
	$config_ok = (!$config or count($config) < 3);

	// Les pages à exclure éventuelles
	$pages_exclues = (defined('_METASPLUS_PAGES_EXCLUES') and _METASPLUS_PAGES_EXCLUES) ? explode(',', _METASPLUS_PAGES_EXCLUES) : array();
	$page_ok = !in_array($page, $pages_exclues);

	// Go go go
	if ($config_ok // au moins un protocole est activé
		and $page_ok // la page n'est pas exclue
	) {

		// Trouver le squelette à utiliser
		$fond_defaut   = 'inclure/metasplus/dist';
		$fond_variante = 'inclure/metasplus/' . $page;
		if (find_in_path($fond_variante.'.html')) {
			$fond = $fond_variante;
		} elseif (find_in_path($fond_defaut.'.html')) {
			$fond = $fond_defaut;
			$contexte['objet'] = $objet;
			$contexte['id_objet'] = $id_objet;
		}

		// Si le squelette n'est pas vide : go go go
		if ($contenu = recuperer_fond($fond, $contexte)) {
			echo $contenu;
		}

	}

}