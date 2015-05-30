<?php
/**
 * Fichier d'options du plugin
 * 
 * @plugin FACD pour SPIP
 * @author b_b
 * @author kent1 (http://www.kent1.info - kent1@arscenic.info)
 * @license GPL
 */
 
if (!defined('_ECRIRE_INC_VERSION')) return;

if(!defined('_DIR_LIB_FLOT'))
	define('_DIR_LIB_FLOT',_DIR_RACINE.'lib/flot');

/**
 * Forcer le fait de pouvoir ajouter des documents sur les documents
 */
if(isset($GLOBALS['meta']['documents_objets']) && !preg_match('/spip_documents/',$GLOBALS['meta']['documents_objets']))
	$GLOBALS['meta']['documents_objets'] = $GLOBALS['meta']['documents_objets'].',spip_documents';

?>