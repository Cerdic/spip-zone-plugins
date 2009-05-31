<?php

function action_echoppe_ajouter_option(){
	include_spip('inc/utils');
	$contexte = Array();
	$contexte['id_produit'] = _request('id_produit');
	$contexte['id_categorie'] = _request('id_categorie');
	$contexte['new'] = _request('new');
	$contexte['lang_produit'] = _request('lang_produit');
	$contexte['titre'] = _request('titre');
	
	$sql_insert_option = "INSERT INTO spip_echoppe_options VALUES ('','".$contexte['id_produit']."','".$contexte['id_categorie']."');";
	$res_insert_option =spip_query($sql_insert_option);
	$contexte['id_option'] = spip_insert_id();
	$sql_insert_description = "INSERT INTO spip_echoppe_options_descriptions VALUES('".$contexte['id_option']."', '".$contexte['lang_produit']."', '".$contexte['titre']."');";
	$res_insert_description = spip_query($sql_insert_description);
	$contexte['redirect'] = generer_url_ecrire("echoppe_produit","id_produit=".$contexte['id_produit'], "&");
	redirige_par_entete($contexte['redirect']);
}

?>
