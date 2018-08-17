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
 * Traitement du formulaire de configuration des optionsproduits
 *
 * @return array
 *     Retours du traitement
 **/
function formulaires_configurer_optionsproduits_traiter_dist() {
	$res    = array('editable' => true);
	$objets = array();
	// création / mise à jour des métas
	if (!is_null(_request('objets'))) {
		$objets_request = array_filter(_request('objets'));
		foreach ($objets_request as $objet) {
			$objets[] = table_objet($objet);
		}
		ecrire_config('optionsproduits/objets', is_array($objets) ? $objets : '');
	}
	ecrire_config('optionsproduits/editer_ttc', _request('editer_ttc'));

	$res['message_ok'] = _T('config_info_enregistree');

	return $res;
}


