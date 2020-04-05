<?php
function autoriser_gererresultats_dist($faire, $type, $id, $qui, $opt){
	return in_array($qui['statut'], array('0minirezo'));
}

function autoriser_modifierstatut_dist($faire, $type, $id, $qui, $opt){
	return in_array($qui['statut'], array('0minirezo'));
}

function autoriser_auteur_gererresultats_dist($faire, $type, $id, $qui, $opt){
	return (in_array($qui['statut'], array('0minirezo')) or ($qui['id_auteur']==$id));
}

?>