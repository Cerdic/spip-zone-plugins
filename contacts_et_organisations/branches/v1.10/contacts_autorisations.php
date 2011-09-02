<?php
/**
 * Plugin Comptes & Contacts pour Spip 2.0
 * Licence GPL (c) 2009 - 2010 - Ateliers CYM
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour le pipeline, n'a rien a effectuer
function contacts_autoriser(){}

function autoriser_contacts_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

?>
