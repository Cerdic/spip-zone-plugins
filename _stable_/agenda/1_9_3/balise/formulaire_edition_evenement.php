<?php
/**
 * Plugin Agenda pour Spip 2.0
 * Licence GPL
 * 
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('inc/editer_evenement'); 

// Pas besoin de contexte de compilation
global $balise_FORMULAIRE_EDITION_EVENEMENT_collecte;
$balise_FORMULAIRE_EDITION_EVENEMENT_collecte = array('id_evenement','id_article');

function balise_FORMULAIRE_EDITION_EVENEMENT ($p) {
	return calculer_balise_dynamique($p,'FORMULAIRE_EDITION_EVENEMENT', array('id_evenement', 'id_article'));
}

function balise_FORMULAIRE_EDITION_EVENEMENT_stat($args, $filtres) {
	return $args;
}
 
?>