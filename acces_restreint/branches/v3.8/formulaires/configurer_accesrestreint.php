<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_configurer_accesrestreint_charger_dist(){
	$valeurs = array(
		'accesrestreint_proteger_documents' => isset($GLOBALS['meta']["accesrestreint_proteger_documents"])?$GLOBALS['meta']["accesrestreint_proteger_documents"]:'non',
		'creer_htpasswd' => $GLOBALS['meta']["creer_htpasswd"]?$GLOBALS['meta']["creer_htpasswd"]:'non',
	);

	return $valeurs;
}

function formulaires_configurer_accesrestreint_traiter_dist(){

	$champs = array('accesrestreint_proteger_documents','creer_htpasswd');
	$current = $GLOBALS['meta']["accesrestreint_proteger_documents"];

	foreach($champs as $c)
		ecrire_meta($c,_request($c)=='oui'?'oui':'non');

	// generer/supprimer les fichiers htaccess qui vont bien
	include_spip("inc/accesrestreint_documents");
	accesrestreint_gerer_htaccess($GLOBALS['meta']["accesrestreint_proteger_documents"]=="oui");

	// si le reglage du htaccess a change, purger le cache
	if ($GLOBALS['meta']["accesrestreint_proteger_documents"]!==$current) {
		$purger = charger_fonction("purger","action");
		$purger("cache");
	}

	return array('message_ok'=>_T('config_info_enregistree'),'editable'=>true);
}