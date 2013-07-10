<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour le pipeline, n'a rien a effectuer
function relances_autoriser(){}

// declarations d'autorisations
function autoriser_relances_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('voir', 'relances', $id, $qui, $opt);
}

function autoriser_relances_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

function autoriser_relance_voir_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('modifier', 'relance', $id, $qui, $opt);
}

function autoriser_relance_modifier_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

?>
