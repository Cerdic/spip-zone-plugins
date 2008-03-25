<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_echoppe_ajouter_panier(){
	
	$contexte = array();
	$contexte['id_produit'] = _request('id_produit');
	$contexte['nombre'] = _request('nombre');
	$contexte['token_client'] = session_get('token_client');
	$contexte['token_panier'] = session_get('token_panier');
	$contexte['statut_panier'] = session_get('statut_panier');
	
	
	$sql_test_existance_produit = "SELECT * FROM spip_echoppe_paniers WHERE id_produit = '".$contexte['id_produit']."';"
	$res_test_existance_produit = spip_query($sql_test_existance_produit);
	if (spip_num_rows($res_test_existance_produit) > 0){
		$sql_insert_produit_panier = "";
	}else{
		
	}
}

?>
