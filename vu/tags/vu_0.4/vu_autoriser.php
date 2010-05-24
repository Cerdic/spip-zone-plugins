<?php

/* pour que le pipeline ne rale pas ! */
function vu_autoriser(){}


// Autoriser a modifier l'annonce $id
// oui, si admin ou redac 
function autoriser_annonce_modifier($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

// Autoriser a modifier l'evenement $id
// oui, si admin ou redac 
function autoriser_evenement_modifier($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

// Autoriser a modifier la publication $id
// oui, si admin ou redac 
function autoriser_publication_modifier($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}



?>
