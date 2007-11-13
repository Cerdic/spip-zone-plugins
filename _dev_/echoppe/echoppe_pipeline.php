<?php


if (!defined("_ECRIRE_INC_VERSION")) return;
	
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_ECHOPPE',(_DIR_PLUGINS.end($p)));


function echoppe_ajouter_boutons($flux){
	$flux['naviguer']->sousmenu['echoppe']= new Bouton("../"._DIR_PLUGIN_ECHOPPE."/images/echoppe_blk_24.png",_T('echoppe:gerer_echoppe'));
	return $flux;	
}
function echoppe_header_prive($flux){
	$flux .= '<link rel="stylesheet" href="'._DIR_PLUGIN_ECHOPPE.'css/echoppe.css'.'" type="text/css" media="all" />';
	return $flux;	
}

?>
