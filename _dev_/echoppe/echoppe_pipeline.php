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

function echoppe_insert_head($flux){
	$flux .= '<!-- Echoppe --> <script src="'._DIR_PLUGIN_ECHOPPE.'jquery.nyroModal-1.2.2.pack.js'.'" type="text/javascript"></script><script src="'._DIR_PLUGIN_ECHOPPE.'echoppe.js'.'" type="text/javascript"></script><link rel="stylesheet" href="'._DIR_PLUGIN_ECHOPPE.'css/echoppe.css'.'" type="text/css" media="all" /><link rel="stylesheet" href="'._DIR_PLUGIN_ECHOPPE.'css/nyroModal.css'.'" type="text/css" media="all" />';
	
	return $flux;	
}

function echoppe_I2_cfg_form($flux){
    //$flux .= recuperer_fond('fonds/inscription2_echopppe');
	
	return $flux;	
}

?>
