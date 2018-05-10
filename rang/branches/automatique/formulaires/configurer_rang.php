<?php

/**
 * Gestion CVT du formulaire de configuration de RANG
 *
 * @plugin     Rang
 * @copyright  2016
 * @author     Peetdu
 * @licence    GNU/GPL
 * @package    SPIP\Rang\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/config');
include_spip('inc/rang_api');

/**
 * Chargement du formulaire de configuration des rangs
 *
 * @return array
 *     Environnement du formulaire
 **/
function formulaires_configurer_rang_charger_dist() {
	$objets = lire_config('rang/rang_objets');
	if ($objets) {
		$valeurs['rang_objets'] = explode(',', $objets);
	}
	$valeurs['rang_max'] = lire_config('rang/rang_max');
	
	return $valeurs;
}

/**
 * Traitement du formulaire de configuration des rangs
 *
 * @return array
 *     Retours du traitement
 **/
function formulaires_configurer_rang_traiter_dist() {
	$res = array('editable' => true);
	$objets = array();
	$err = null;

	// création / mise à jour des métas
	if (!is_null(_request('rang_objets'))) {
		$objets = array_filter(_request('rang_objets'));
		ecrire_config('rang/rang_objets', is_array($objets) ? implode(',', $objets) : '');
	}
	ecrire_config('rang/rang_max', _request('rang_max'));

	// créer les champs dans les tables
	rang_creer_champs($objets);

	$res['message_ok'] = _T('config_info_enregistree');

	return $res;
}


