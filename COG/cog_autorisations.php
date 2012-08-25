<?php



if (!defined("_ECRIRE_INC_VERSION")) return;

function cog_autoriser(){
	return true;
}
// declarations d'autorisations
function autoriser_cog_bouton_dist($faire, $type, $id, $qui, $opt) {
return autoriser('voir', 'cog', $id, $qui, $opt);
}

function autoriser_cog_voir_dist($faire, $type, $id, $qui, $opt) {
return in_array($qui['statut'], array('0minirezo', '1comite'));
}

function autoriser_cog_modifier_dist($faire, $type, $id, $qui, $opt) {
return in_array($qui['statut'], array('0minirezo'));
}


?>
