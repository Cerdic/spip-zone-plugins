<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


// fonction pour le pipeline, n'a rien a effectuer
function mesabonnes_autoriser(){}

// declarations d'autorisations
function autoriser_mesabonnes_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('voir', 'mesabonnes', $id, $qui, $opt);
}
	
function autoriser_mesabonnes_voir_dist($faire, $type, $id, $qui, $opt) {
	return (in_array($qui['statut'],array('0minirezo')));// pour l'instant uniquement les admins
}

