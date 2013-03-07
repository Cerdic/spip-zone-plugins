<?php
/**
 * Définit les autorisations du plugin Coordonnees
 *
 * @plugin     Coordonnees
 * @copyright  2013
 * @author     Marcimat / Ateliers CYM
 * @licence    GNU/GPL
 * @package    SPIP\Coordonnees\Autorisations
 */


if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function coordonnees_autoriser(){}


// --------------
// Objet Adresses

// creer
function autoriser_adresse_creer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite')); 
}

// voir les fiches completes
function autoriser_adresse_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// modifier
function autoriser_adresse_modifier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

// supprimer
function autoriser_adresse_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

// associer (lier / delier)
function autoriser_associeradresses_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}


// --------------
// Objet numeros

// creer
function autoriser_numero_creer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite')); 
}

// voir les fiches completes
function autoriser_numero_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// modifier
function autoriser_numero_modifier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

// supprimer
function autoriser_numero_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

// associer (lier / delier)
function autoriser_associernumeros_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}


// ------------
// Objet emails

// creer
function autoriser_email_creer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite')); 
}

// voir les fiches completes
function autoriser_email_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

// modifier
function autoriser_email_modifier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

// supprimer
function autoriser_email_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}


// associer (lier / delier)
function autoriser_associeremails_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}



?>
