<?php

/*
 * Plugin Titre de logo
 *
 * Distribue sous licence GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('titre_logo_administrations');


function formulaires_configurer_titre_logo_traiter_dist(){

	include_spip('inc/cvt_configurer');
	$trace = cvtconf_formulaires_configurer_enregistre('configurer_titre_logo', array());
	$res = array('message_ok' => _T('config_info_enregistree') . $trace, 'editable' => true);

	titre_logo_check_upgrade();

	return $res;
}