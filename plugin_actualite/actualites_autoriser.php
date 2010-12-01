<?php

/* pour que le pipeline ne rale pas ! */
function actualites_autoriser(){}


// Autoriser a modifier l'actualite $id
// oui, si admin ou redac 
function autoriser_actualite_modifier($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

?>
