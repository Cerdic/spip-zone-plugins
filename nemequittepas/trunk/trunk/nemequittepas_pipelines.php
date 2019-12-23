<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

/*
 * Insertion dans le pipeline insert_head_prive (SPIP)
 * Ajoute des barres de porte-plume sur les champs configurés
 * 
 * @param string $flux : le contexte du pipeline
 * @return string $flux : le contexte modifié
 */

function nemequittepas_insert_head_prive($flux){
	$js = find_in_path('js/jquery.are-you-sure.js');
	$flux .= "<script type='text/javascript' src='$js'></script>\n";
	$js = find_in_path('js/nemequittepas.js');
	$flux .= "<script type='text/javascript' src='$js'></script>\n";
	return $flux;
}
