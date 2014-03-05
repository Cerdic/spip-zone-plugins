<?php
/*
 * Plugin Alertes
 * Distribué sous licence GPL
 *
 * Fonctions
 */
if (!defined('_ECRIRE_INC_VERSION')) return;

/*** Fonction transformant une liste de valeurs séparées par des virgules en array ***/
function to_array($texte){
	$texte = preg_replace('/\s/', '', trim($texte));
	$array = explode(",",$texte);
	return $array;
}
?>