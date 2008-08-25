<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_BUREAU',(_DIR_PLUGINS.end($p)));

if (!defined("_ECRIRE_INC_VERSION")) return;

// Les préférences de l'utilisateur sont stockés dans les extras de la table auteur
if (empty($GLOBALS['champs_extra']['auteurs']))
	$GLOBALS['champs_extra']['auteurs'] = Array();

// Declaration des pipelines
$GLOBALS['spip_pipeline']['BUREAU_menus']='';
$GLOBALS['spip_pipeline']['BUREAU_infos']='';
$GLOBALS['spip_pipeline']['BUREAU_fenetres']='';
?>
