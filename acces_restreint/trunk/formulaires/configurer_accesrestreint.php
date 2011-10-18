<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_configurer_accesrestreint_charger_dist(){
	$valeurs = array(
		'creer_htaccess' => $GLOBALS['meta']["creer_htaccess"]?$GLOBALS['meta']["creer_htaccess"]:'non',
		'creer_htpasswd' => $GLOBALS['meta']["creer_htpasswd"]?$GLOBALS['meta']["creer_htpasswd"]:'non',
	);

	return $valeurs;
}

function formulaires_configurer_accesrestreint_traiter_dist(){

	$champs = array('creer_htaccess','creer_htpasswd');

	foreach($champs as $c)
		ecrire_meta($c,_request($c)=='oui'?'oui':'non');

	return array('message_ok'=>_T('config_info_enregistree'),'editable'=>true);
}