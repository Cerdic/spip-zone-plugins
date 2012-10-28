<?php
/**
 * Plugin À vos souhaits
 * (c) 2012 RastaPopoulos
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

// declaration vide pour ce pipeline.
function souhaits_autoriser(){}


// -----------------
// Objet souhaits


// bouton de menu
function autoriser_souhaits_menu_dist($faire, $type, $id, $qui, $opts){
	return true;
} 

// bouton d'outils rapides
function autoriser_souhaitcreer_menu_dist($faire, $type, $id, $qui, $opts){
	return autoriser('creer', 'souhait', '', $qui, $opts);
} 

// creer
function autoriser_souhait_creer_dist($faire, $type, $id, $qui, $opt) {
	return (in_array($qui['statut'], array('0minirezo', '1comite')) AND sql_countsel('spip_rubriques')>0); 
}

// voir les fiches completes
function autoriser_souhait_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// modifier
function autoriser_souhait_modifier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

// supprimer
function autoriser_souhait_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

// creer dans une rubrique
function autoriser_rubrique_creersouhaitdans_dist($faire, $type, $id, $qui, $opt) {
	return ($id AND autoriser('voir','rubrique', $id) AND autoriser('creer','souhait', $id));
}



?>