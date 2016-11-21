<?php

/**
 * Pipeline pour shortcut_url
 *
 * @plugin     shortcut_url
 * @copyright  2015
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\shortcut_url\pipelines
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Autorisation à créer une URL raccourcie
 *
 * Les administrateurs, les rédacteurs, les ips autorisées depuis la configuration
 *
 * @param string $faire
 * @param string $type
 * @param int $id
 * @param array $qui
 * @param array $opt
 * @return bool
 */
function autoriser_shortcuturl_creer_dist($faire, $type, $id, $qui, $opt) {
	include_spip('inc/config');
	$ips = array_map('trim', explode(',', lire_config('shortcut_url/serveurs_api')));
	return in_array($qui['statut'], array('0minirezo', '1comite')) or in_array($GLOBALS['ip'], $ips);
}
/**
 * Autorisation du menu d'entrée dans l'admin pour spip 3.1
 *
 * On supprime l'accès à tous les menus non utiles
 *
 * @param string $faire
 * @param string $type
 * @param int $id
 * @param array $qui
 * @param array $opt
 * @return bool
 */
function autoriser_menugrandeentree($faire, $type, $id, $qui, $opt) {
	if (!in_array($type, array(
		'menuaccueil',
		'menuedition',
		'menupublication',
		'menuadministration',
		'menuconfiguration',
		'menushortcuturl'))) {
		return false;
	}

	return true;
}

/**
 * Autorisation du menu d'entrée dans l'admin pour spip 3.0
 *
 * @param string $faire
 * @param string $type
 * @param int $id
 * @param array $qui
 * @param array $opt
 * @return bool
 */
function autoriser_revisions_menu($faire, $type, $id, $qui, $opt) {
	return true;
}
function autoriser_mediabox_menu($faire, $type, $id, $qui, $opt) {
	return false;
}
function autoriser_visiteurs_menu($faire, $type, $id, $qui, $opt) {
	return false;
}
function autoriser_suiviedito_menu($faire, $type, $id, $qui, $opt) {
	return false;
}
function autoriser_synchro_menu($faire, $type, $id, $qui, $opt) {
	return false;
}
function autoriser_articles_menu($faire, $type, $id, $qui, $opt) {
	return false;
}
function autoriser_rubriques_menu($faire, $type, $id, $qui, $opt) {
	return false;
}
function autoriser_rubrique_creer($faire, $type, $id, $qui, $opt) {
	return false;
}
function autoriser_documents_menu($faire, $type, $id, $qui, $opt) {
	return false;
}
function autoriser_sites_menu($faire, $type, $id, $qui, $opt) {
	return false;
}
function autoriser_mots_menu($faire, $type, $id, $qui, $opt) {
	return false;
}
function autoriser_breves_menu($faire, $type, $id, $qui, $opt) {
	return false;
}
function autoriser_voirrevisions($faire, $type, $id, $qui, $opt) {
	return true;
}

/**
 * Autoriser shortcut dans le menu
 *
 * @param string $faire
 * @param string $type
 * @param int $id
 * @param array $qui
 * @param array $opt
 * @return bool
 */
function autoriser_menushortcuturl_menu($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('1comite', '0minirezo')) && count($qui['restreint']) == 0;
}

/**
 * Ajouter un bouton stats
 *
 * @param array $boutons_admin
 * @return array
 */
function shortcut_url_ajouter_menus($boutons_admin) {
	include_spip('inc/autoriser');
	if (autoriser('menu', '_menu_shortcut_url')) {
		$pages = array('shortcut_url_logs', 'shortcut_url_logs_export');
		foreach ($pages as $page) {
			$boutons_admin['menu_shortcut_url']->sousmenu[] = new Bouton(
				find_in_theme('images/shortcut_url-16.png'),
				'shortcut_url:' . $page,
				$page
			);
		}
	} else {
		unset($boutons_admin['menu_shortcut_url']);
	}

	return $boutons_admin;
}

/**
 * Affiche les URL publié par un auteur dans sa fiche
 *
 * @param array $flux Le contexte du pipeline
 * @return array
 */
function shortcut_url_affiche_milieu($flux) {
	if (trouver_objet_exec($flux['args']['exec'] == 'auteur') && $flux['args']['id_auteur']) {
		$id_auteur = $flux['args']['id_auteur'];
		$texte = recuperer_fond('prive/objets/editer/shortcut_url_auteurs', array('id_auteur' => $id_auteur));
		if ($p = strpos($flux['data'], '<!--affiche_milieu-->')) {
			$flux['data'] .= $texte;
		} else {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		}
	}

	return $flux;
}

/**
 * Ajoute les css pour shortcut_url chargées dans le privé
 *
 * @param string $flux Contenu du head HTML concernant les CSS
 * @return string      Contenu du head HTML concernant les CSS modifié
 */
function shortcut_url_header_prive_css($flux) {
	$css = find_in_path('css/shortcut_url.css');
	$flux .= "<link rel='stylesheet' type='text/css' media='all' href='" . direction_css($css) . "' />\n";

	return $flux;
}

/**
 * Insertion des plugins d3js supplémentaires dans le head
 *
 * @param $flux
 * @return mixed
 */
function shortcut_url_d3js_plugins($plugins) {
	if (test_espace_prive()) {
		$plugins[] = 'topojson';
	}
	return $plugins;
}
