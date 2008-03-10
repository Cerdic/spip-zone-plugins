<?php
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_OPENPUBLISHING',(_DIR_PLUGINS.end($p)));

if (!defined("_ECRIRE_INC_VERSION")) return;

// si les extra de la table que l'on veut etendre sont vide faut creer le tableau
if (empty($GLOBALS['champs_extra']['articles']))
	$GLOBALS['champs_extra']['articles'] = Array();

$GLOBALS['champs_extra']['articles']['OP_pseudo'] = "ligne|propre|pseudonyme du r&eacute;dacteur";
$GLOBALS['champs_extra']['articles']['OP_mail'] = "ligne|propre|mail du r&eacute;dacteur";

?>