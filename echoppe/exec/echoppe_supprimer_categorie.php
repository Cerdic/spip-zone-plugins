<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_echoppe_supprimer_categorie(){
	
	include_spip('inc/headers');
	
	$contexte = Array();
	
	$contexte['id_categorie'] = _request('id_categorie');
	
	$id_parent = sql_fetsel(array('id_parent'),array('spip_echoppe_categories'),array("id_categorie=".sql_quote($contexte['id_categorie'])));
	
	$delete_categorie = sql_updateq('spip_echoppe_categories',array("statut"=>"poubelle"),array("id_categorie=".$contexte['id_categorie']));
	
	$redirect = generer_url_ecrire("echoppe_categorie","id_categorie=".$id_parent['id_parent'],"&");
	
	redirige_par_entete($redirect);
}

?>
