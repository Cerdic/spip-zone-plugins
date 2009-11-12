<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour le pipeline, n'a rien a effectuer
function bannieres_autoriser(){}

// declarations d'autorisations
function autoriser_bannieres_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('voir', 'bannieres', $id, $qui, $opt);
}

function autoriser_bannieres_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

function autoriser_banniere_voir_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('modifier', 'banniere', $id, $qui, $opt);
}

function autoriser_banniere_modifier_dist($faire, $type, $id, $qui, $opt) {
	// autorisations a modifier si necessaire
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

?>
