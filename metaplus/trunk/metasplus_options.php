<?php
/**
 * Options du plugin Métas+
 *
 * @plugin     Métas+
 * @copyright  2018
 * @author     Tetue, Erational, Tcharlss
 * @licence    GNU/GPL
 * @package    SPIP\Metas+\Options
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

	// Identifier la page actuelle
	$page = metasplus_identifier_page($contexte, $page_erreur);

	// Vérifier que tous les protocoles ne sont pas désactivés en config
	// (dans ce cas là, autant désactiver le plugin, mais on ne sait jamais)
	include_spip('inc/config');
	$config = lire_config('metasplus');
	$config_ok = (!$config or count($config) < 3);

	// Les pages à exclure éventuelles
	$pages_exclues = (defined('_METASPLUS_PAGES_EXCLUES') and _METASPLUS_PAGES_EXCLUES) ? explode(',', _METASPLUS_PAGES_EXCLUES) : array();
	$page_ok = (!in_array($page, $pages_exclues) and !$page_erreur);

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
		}

		// Si le squelette n'est pas vide : go go go
		if ($contenu = recuperer_fond($fond, $contexte)) {
			echo $contenu;
		}

	}
}

/**
 * Identifier la page
 * 
 * On retourne 2 choses par référence :
 * - un contexte (objet et id_objet si c'est la page d'un objet)
 * - s'il s'agit d'une page d'erreur
 * 
 * Il n'est pas recommandé d'utiliser $GLOBALS['contexte], donc on utilise la fonction qui décode l'URL et retourne les bonnes infos :
 * [0]            => page (le fond)
 * [1][id_patate] => id si page d'un objet
 * [1][erreur]    => erreur éventuelle (404)
 *
 * @return string la page
 */
function metasplus_identifier_page (&$contexte, &$page_erreur) {

	$contexte = array();
	$decoder_url = charger_fonction('decoder_url', 'urls');
	$url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; // [FIXME] The client can set HTTP_HOST and REQUEST_URI to any arbitrary value it wants.
	$decodage = $decoder_url($url);
	$page = $decodage[0];
	$page_erreur = isset($decodage[1]['erreur']) ? true : false;

	// 1) Page retrouvée et pas en erreur
	if ($page
		and !$page_erreur
	) {
		include_spip('base/objets');
		$id_table_objet = id_table_objet($page);
		$id_objet = isset($decodage[1][$id_table_objet]) ? $decodage[1][$id_table_objet] : null;
		if ($id_objet) {
			$contexte['objet'] = $page;
			$contexte['id_objet'] = $id_objet;
			$contexte[$id_table_objet] = $id_objet; // ça peut servir
		}

	// 2) Sinon page lambda avec 'page' en query string
	} elseif (!$page) {
		$page = _request('page');
	}

	return $page;
}