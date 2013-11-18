<?php
/**
 * Plugin Simple trad
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * © 2013 - Distribue sous licence GNU/GPL
 * 
 * Fonctions PHP du squelette selecteur_langue_simple.html
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