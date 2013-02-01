<?php
/**
 * Plugin Diogene
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info)
 *
 * © 2010-2011 - Distribue sous licence GNU/GPL
 * 
 * Fonctions PHP du squelette selecteur_langue.html
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Suppression d'une langue dans l'array de langue
 * Cas des traductions existantes
 *
 * @param array $array L'array des langues à disposition
 * @param string $val La langue à enlever
 */
function langue_unset($array,$val){
	if(in_array($val,$array)){
		$key = array_search($val, $array);
		unset($array[$key]);
	}
	return $array;
}

?>