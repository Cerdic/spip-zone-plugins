<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_echoppe_ajouter_panier(){
	
	include_spip('inc/session');
	include_spip('inc/session');
	
	$contexte = array();
	$contexte['id_produit'] = _request('id_produit');
	$contexte['id_panier'] = _request('id_panier');
	$contexte['quantite'] = _request('quantite');
	$contexte['token_client'] = session_get('echoppe_token_client');
	$contexte['token_panier'] = session_get('echoppe_token_panier');
	$contexte['statut_panier'] = session_get('echoppe_statut_panier');
	$contexte['redirect'] = _request('redirect');
	$contexte['achat_rapide'] = _request('achat_rapide');
	
	if ($contexte['achat_rapide'] == "oui"){
		$contexte['redirect'] = $contexte['redirect'];
	}else{
		$_page = lire_config('echoppe/squelette_panier','echoppe_panier');
		$contexte['redirect'] = generer_url_public($_page);
	}
	
	
	$sql_test_existance_produit = "SELECT * FROM spip_echoppe_paniers WHERE id_produit = '".$contexte['id_produit']."' AND token_panier = '".$contexte['token_panier']."';";
	$res_test_existance_produit = spip_query($sql_test_existance_produit);
	$le_produit_existant = spip_fetch_array($res_test_existance_produit);
	if (spip_num_rows($res_test_existance_produit) <= 0){
		$sql_insert_produit_panier = "INSERT INTO spip_echoppe_paniers VALUES ('', '".$contexte['id_client']."', '".$contexte['id_produit']."', '".$contexte['quantite']."', '".$contexte['configuration']."', '".$contexte['token_client']."', '".$contexte['token_panier']."', '".$contexte['statut']."', '".$contexte['date_maj']."');";
		$res_insert_produit_panier = spip_query($sql_insert_produit_panier);
	}else{
		
		if ($contexte['achat_rapide'] == "oui"){
			$contexte['quantite'] = $le_produit_existant['quantite'] + 1;
		}
		
		$sql_maj_produit_panier = "UPDATE spip_echoppe_paniers SET quantite = '".$contexte['quantite']."', configuration = '".$contexte['configuration']."', statut = '".$contexte['statut_panier']."' WHERE id_panier = '".$contexte['id_panier']."' AND token_panier = '".$contexte['token_panier']."'; ";
		//var_dump($sql_maj_produit_panier);
		$res_maj_produit_panier = spip_query($sql_maj_produit_panier);
	}
	
	
	redirige_par_entete($contexte['redirect']);
	
}

?>
