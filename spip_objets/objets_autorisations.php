<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour le pipeline, n'a rien a effectuer
function objets_autoriser(){}

// declarations d'autorisations
function autoriser_objets_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('voir', $type, $id, $qui, $opt);
}

function autoriser_objets_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

function autoriser_objets_voir_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('modifier', $type, $id, $qui, $opt);
}

function autoriser_objets_modifier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite')); //les admins et rédacteurs peuvent modifier une actu
}
