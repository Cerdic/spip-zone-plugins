<?php


if (!defined("_ECRIRE_INC_VERSION")) return;
	
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_ECHOPPE',(_DIR_PLUGINS.end($p)));


function echoppe_ajouter_boutons($flux){
	$flux['naviguer']->sousmenu['echoppe']= new Bouton("../"._DIR_PLUGIN_ECHOPPE."/images/echoppe_blk_24.png",_T('echoppe:gerer_echoppe'));
	return $flux;	
}
function echoppe_header_prive($flux){
	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/echoppe.css').'" type="text/css" media="all" /> <!-- CSS Echoppe --> ';
	return $flux;	
}

function echoppe_insert_head($flux){
	return $flux;	
}

function echoppe_I2_cfg_form($flux){
    //$flux .= recuperer_fond('fonds/inscription2_echopppe');
	
	return $flux;	
}

function echoppe_taches_generales_cron($taches_generales){
	$jours = lire_config('echoppe/duree_de_vie_paniers_temp', 2);
	$taches_generales['echoppe'] = 60*60*24*$jours; // par exemple toutes les 10 minutes, ne pas descendre en dessous de 30 secondes !
	return $taches_generales;
}

?>
