<?php

function action_echoppe_ajouter_option(){
	$contexte = Array();
	$contexte['id_produit'] = _request('id_produit');
	$contexte['id_categorie'] = _request('id_categorie');
	$contexte['new'] = _request('new');
	
	
	$sql_insert_option = "INSERT INTO spip_echoppe_options VALUES ('','".$contexte['id_produit']."','".$contexte['id_categorie']."');";
	
}

?>
