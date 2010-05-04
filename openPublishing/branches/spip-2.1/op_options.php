<?php
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_OPENPUBLISHING',(_DIR_PLUGINS.end($p)));

if (!defined("_ECRIRE_INC_VERSION")) return;

if (version_compare($GLOBALS['spip_version_code'], '1.9300', '<'))
	include_spip('inc/compat_op');

// si les extra de la table que l'on veut etendre sont vide faut creer le tableau
if (empty($GLOBALS['champs_extra']['articles']))
	$GLOBALS['champs_extra']['articles'] = Array();

// si les extra de la table que l'on veut etendre sont vide faut creer le tableau
if (empty($GLOBALS['champs_extra']['breves']))
	$GLOBALS['champs_extra']['breves'] = Array();

$GLOBALS['champs_extra']['articles']['OP_pseudo'] = "ligne|propre|pseudonyme du r&eacute;dacteur";
$GLOBALS['champs_extra']['articles']['OP_mail'] = "ligne|propre|mail du r&eacute;dacteur";
$GLOBALS['champs_extra']['breves']['OP_pseudo'] = "ligne|propre|pseudonyme du r&eacute;dacteur";
$GLOBALS['champs_extra']['breves']['OP_mail'] = "ligne|propre|mail du r&eacute;dacteur";

// Declaration des pipelines
$GLOBALS['spip_pipeline']['OP_squelette']='';
$GLOBALS['spip_pipeline']['OP_environnement']='';
$GLOBALS['spip_pipeline']['OP_action']='';
$GLOBALS['spip_pipeline']['OP_pre_validation']='';
$GLOBALS['spip_pipeline']['OP_validation']='';
$GLOBALS['spip_pipeline']['OP_abandon']='';

?>
