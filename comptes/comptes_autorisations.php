<?php
/**
 * Plugin Comptes & Contacts pour Spip 2.0
 * Licence GPL (c) 2009 - 2010 - Ateliers CYM
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour le pipeline, n'a rien a effectuer
function comptes_autoriser(){}

// declarations d'autorisations
function autoriser_comptes_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('voir', 'comptes', $id, $qui, $opt);
}

function autoriser_comptes_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

?>