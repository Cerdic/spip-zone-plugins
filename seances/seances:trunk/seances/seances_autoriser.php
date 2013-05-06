<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour le pipeline, rien a effectuer
function seances_autoriser(){}

// autorisations
function autoriser_seances_endroits_bouton($faire, $type, $id, $qui, $opt) {
	return autoriser('voir', 'seances_endroits', $id, $qui, $opt);
	/* return in_array($qui['statut'], array('0minirezo', '1comite')); */
	// return true;
}

// pour la remise à zéro
function autoriser_seances_administrer($faire, $type, $id, $qui, $opt) {
	return ($qui['statut']=='0minirezo');
}

// définir et modifier les endroits
function autoriser_seances_endroits_voir($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

function autoriser_seances_endroit_voir($faire, $type, $id, $qui, $opt) {
	return autoriser('modifier', 'seances_endroit', $id, $qui, $opt);
}


function autoriser_seances_endroit_modifier($faire, $type, $id, $qui, $opt) {
	return ($qui['statut']=='0minirezo');
}

// créer modifier supprimer les séances
function autoriser_seance_modifier($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

?>