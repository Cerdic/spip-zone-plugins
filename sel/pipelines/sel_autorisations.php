<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
// fonction pour le pipeline, n'a rien a effectuer

function sel_autoriser(){}


function autoriser_auteur_modifier($faire, $type, $id, $qui, $opt){
/*
	$auth = array();
	$auth[0] = 1; // pour autoriser l'id_auteur 1 à tout modifier
	if (($id=='new') || ($id=='oui')) $lid=0; else $lid = $id;
	$res_auteurs = sql_select('id_auteur','spip_auteurs','id_auteur='.$lid);
	while($r = sql_fetch($res_auteurs)) {
		$auth[] = $r['id_auteur'];
	}
	return in_array($qui['id_auteur'], $auth);
	*/
	return true;
}

?>