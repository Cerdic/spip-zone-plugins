<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function codespostaux_autoriser(){
	return true;
}
// declarations d'autorisations
function autoriser_codespostaux_bouton_dist($faire, $type, $id, $qui, $opt) {
return autoriser('voir', 'codes_postaux', $id, $qui, $opt);
}

function autoriser_codespostaux_voir_dist($faire, $type, $id, $qui, $opt) {
return in_array($qui['statut'], array('0minirezo', '1comite'));
}

function autoriser_codespostaux_modifier_dist($faire, $type, $id, $qui, $opt) {
return in_array($qui['statut'], array('0minirezo'));
}

?>
