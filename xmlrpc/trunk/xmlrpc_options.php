<?php
/**
 * Plugin xmlrpc
 * 
 * Auteurs : kent1 (http://www.kent1.info)
 * © 2011 - GNU/GPL v3
 * 
 * Fichier d'options du plugin
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Déclaration des pipelines créés par le plugin
 */
$GLOBALS['spip_pipeline']['xmlrpc_apis'] = '';
$GLOBALS['spip_pipeline']['xmlrpc_methodes'] = '';
$GLOBALS['spip_pipeline']['xmlrpc_serveur_class'] = '';
$GLOBALS['spip_pipeline']['xmlrpc_pre_methode'] = '';
$GLOBALS['spip_pipeline']['xmlrpc_post_methode'] = '';

define('_DIR_IXR','lib/ixr/');

/**
 * Fonction de base client xmlrpc
 *
 * @return unknown_type
 */
function xmlrpc(){
	$args = func_get_args();
	spip_log($args,'xmpp');
	include_spip('inc/xmlrpc');
	return call_user_func_array('inc_xmlrpc_dist', $args);
}
?>