<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour le pipeline, n'a rien a effectuer
function boutique_autoriser(){}

// declarations d'autorisations
function autoriser_boutique_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('voir', 'boutiques', $id, $qui, $opt);
}

function autoriser_boutique_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

function autoriser_boutique_voir_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('modifier', 'boutique', $id, $qui, $opt);
}

function autoriser_boutique_modifier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

?>
