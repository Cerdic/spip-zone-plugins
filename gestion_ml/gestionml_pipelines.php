<?php
/**
 * Gestion de Mailling Lists Via l'API SOAP chez OVH
 * Licence GPL (c) 2010 Cloarec  Yffic
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline insert_head_prive
 * Ajoute css et javascript dans le <head> privé
 *
 * @param string $flux Le contenu du head privé
 */
function gestionml_insert_head_prive($flux){

	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/gestionml.css').'" type="text/css" media="all" />';
	return $flux;
}


?>