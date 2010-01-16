<?php
/**
 * Plugin Transactions pour Spip 2.0
 * Licence GPL (c) 2010 - Ateliers CYM
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour le pipeline, n'a rien a effectuer
function transactions_autoriser(){}

// declarations d'autorisations
function autoriser_transactions_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('voir', 'transactions', $id, $qui, $opt);
}

function autoriser_transactions_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

?>