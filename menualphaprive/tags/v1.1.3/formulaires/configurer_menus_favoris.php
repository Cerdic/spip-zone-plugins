<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/bandeau');
include_spip('prive/squelettes/inclure/barre-nav_fonctions');


/**
 * Chargement du formulaire de menus favoris d'un auteur dans l'espace privé
 *
 * @return array
 *     Environnement du formulaire
 **/
function formulaires_configurer_menus_favoris_charger_dist() {
	// travailler sur des meta fraiches
	include_spip('inc/meta');
	lire_metas();
	$valeurs = array();
	$valeurs['menus_favoris'] = obtenir_menus_favoris();
	return $valeurs;
}

/**
 * Traitements du formulaire de menus favoris d'un auteur dans l'espace privé
 *
 * @return array
 *     Retours des traitements
 **/
function formulaires_configurer_menus_favoris_traiter_dist() {

	$menus_favoris = _request('menus_favoris');

	// si le menu change, on recharge toute la page.
	if ($menus_favoris != obtenir_menus_favoris()) {
		refuser_traiter_formulaire_ajax();

		$GLOBALS['visiteur_session']['prefs']['menus_favoris'] = $menus_favoris;

		if (intval($GLOBALS['visiteur_session']['id_auteur'])) {
			include_spip('action/editer_auteur');
			$c = array('prefs' => serialize($GLOBALS['visiteur_session']['prefs']));
			auteur_modifier($GLOBALS['visiteur_session']['id_auteur'], $c);

		}
	}

	return array('message_ok' => _T('config_info_enregistree'), 'editable' => true);
}
