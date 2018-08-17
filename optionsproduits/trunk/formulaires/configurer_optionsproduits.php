<?php

/**
 * Gestion CVT du formulaire de configuration de OPTIONSPRODUITS
 *
 * @plugin     Optionsproduits
 * @copyright  2018
 * @author     nicod_
 * @licence    GNU/GPL
 * @package    SPIP\Optionsproduits\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/config');

/**
 * Chargement du formulaire de configuration
 *
 * @return array
 *     Environnement du formulaire
 **/
function formulaires_configurer_optionsproduits_charger_dist() {

	$valeurs = array(
		'objets'     => explode(',', lire_config('optionsproduits/objets')),
		'editer_ttc' => lire_config('optionsproduits/editer_ttc'),
	);

	return $valeurs;
}

/**
 * Traitement du formulaire de configuration
 *
 * @return array
 *     Retours du traitement
 **/
function formulaires_configurer_optionsproduits_traiter_dist() {
	if (!is_null(_request('objets'))) {
		$objets = array_filter(_request('objets'));
		ecrire_config('optionsproduits/objets', is_array($objets) ? implode(',', $objets) : '');
	}
	ecrire_config('optionsproduits/editer_ttc', _request('editer_ttc'));

	$res = array(
		'message_ok' => _T('config_info_enregistree'),
		'editable'   => true,
	);

	return $res;
}


