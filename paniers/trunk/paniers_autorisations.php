<?php
/**
 * Plugin Paniers pour Spip 3.0
 * Licence GPL 
 * Auteur Cyril Marion - Ateliers CYM
 * 
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour le pipeline, n'a rien a effectuer
function paniers_autoriser(){}

// declarations d'autorisations
function autoriser_paniers_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('voir', 'paniers', $id, $qui, $opt);
}

function autoriser_paniers_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

?>
