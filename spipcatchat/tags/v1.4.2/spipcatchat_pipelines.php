<?php
/**
 * Plugin SpipCatChat pour Spip 3.0.* 3.1.* 
 * Licence GPL (c) 2014 - 2015 Codden Claude
 * 
 * Fichier des utilisations des pipelines du plugin
 * 
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
 
// Insertion du jQuery pour l'autocomplétion de SpipCatChat
function spipcatchat_jqueryui_plugins($scripts){ 	
	$scripts[] = "jquery.ui.autocomplete";
    return $scripts;
}

// Insertion dans le header prive javascript et CSS
function spipcatchat_header_prive($flux){
	$flux .="\n".'<link rel="stylesheet" href="'.find_in_path('prive/themes/spip/spipcatchat_prive.css').'" />';
	return $flux;	
}

// Insertion des css public de SpipCatChat
function spipcatchat_insert_head_css($flux){
	$flux .="\n".'<link rel="stylesheet" href="'.find_in_path('css/spipcatchat.css').'" />';
	$flux .="\n".'<link rel="stylesheet" href="'.find_in_path('css/spipcatchat_formulaire.css').'" />';
	return $flux;
}

// autorisation du bouton Spipcatchat dans l'espace privé
function autoriser_spipcatchat_menu_dist($faire, $type, $id, $qui, $opt){
    return true;
}
?>