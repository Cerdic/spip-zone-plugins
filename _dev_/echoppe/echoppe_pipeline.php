<?php


if (!defined("_ECRIRE_INC_VERSION")) return;
	
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_ECHOPPE',(_DIR_PLUGINS.end($p)));


function echoppe_ajouter_boutons($flux){
	$flux['naviguer']->sousmenu['echoppe']= new Bouton("../"._DIR_PLUGIN_ECHOPPE."/images/logo.png",_T('echoppe:gerer_echoppe'));
	return $flux;	
}

?>