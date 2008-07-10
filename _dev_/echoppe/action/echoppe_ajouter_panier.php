<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_echoppe_ajouter_panier(){
	
	include_spip('inc/session');
	include_spip('inc/session');
	
	$contexte = array();
	$contexte['id_produit'] = _request('id_produit');
	$contexte['id_auteur'] = session_get('id_auteur');
	$contexte['quantite'] = _request('quantite');
	$contexte['token_client'] = session_get('echoppe_token_client');
	$contexte['token_panier'] = session_get('echoppe_token_panier');
	$contexte['statut_panier'] = session_get('echoppe_statut_panier');
	$contexte['redirect'] = _request('redirect');
	$contexte['achat_rapide'] = _request('achat_rapide');
	$contexte['date_maj'] = date("Y-m-d h:i:s");
	$contexte['message_erreur'] = "";
	
	$sql_test_existance_produit = "SELECT * FROM spip_echoppe_paniers WHERE id_produit = '".$contexte['id_produit']."' AND token_panier = '".$contexte['token_panier']."' AND token_client = '".$contexte['token_client']."';";
	$res_test_existance_produit = spip_query($sql_test_existance_produit);
	$le_produit_existant = spip_fetch_array($res_test_existance_produit);	
	$contexte['id_panier'] = $le_produit_existant['id_panier'];
	
	$_page = lire_config('echoppe/squelette_panier','echoppe_panier');
	$contexte['redirect'] = generer_url_public($_page);
	
	if (session_get('echoppe_statut_panier') == "temp"){
		if (spip_num_rows($res_test_existance_produit) >= 1){
			if ($contexte['achat_rapide'] == "oui"){ $contexte['quantite'] = $le_produit_existant['quantite'] + $contexte['quantite']; }
			$sql_maj_produit_panier = "UPDATE spip_echoppe_paniers SET quantite = '".$contexte['quantite']."', configuration = '".$contexte['configuration']."', statut = '".$contexte['statut_panier']."', date ='".$contexte['date_maj']."' WHERE id_panier = '".$contexte['id_panier']."' AND token_panier = '".$contexte['token_panier']."'; ";
			//var_dump($sql_maj_produit_panier);
			$res_maj_produit_panier = spip_query($sql_maj_produit_panier);
			
		}else{
			$sql_insert_produit_panier = "INSERT INTO spip_echoppe_paniers VALUES ('', '".$contexte['id_client']."', '".$contexte['id_produit']."', '".$contexte['quantite']."', '".$contexte['configuration']."', '".$contexte['token_client']."', '".$contexte['token_panier']."', '".session_get('echoppe_statut_panier')."', '".$contexte['date_maj']."');";
			//var_dump($sql_insert_produit_panier);
			$res_insert_produit_panier = spip_query($sql_insert_produit_panier);
			$contexte['id_panier'] = spip_insert_id($res_maj_produit_panier);
		}
		
		
		if ($contexte['achat_rapide'] == "oui"){
			$contexte['redirect'] = generer_url_public("produit","id_produit=".$contexte['id_produit'],"&");
		}else{
			if ($contexte['achat_rapide'] == "maj"){
				$_page = lire_config('echoppe/squelette_panier','echoppe_panier');
				$contexte['redirect'] = generer_url_public($_page);
			}else{	
				$_page = lire_config('echoppe/squelette_panier','echoppe_panier');
				$contexte['redirect'] = generer_url_public($_page,"editer_produit=oui&id_panier=".$contexte['id_panier'],"&");
			}
		}
		
	}else{
		$contexte['message_erreur'] = _T('echoppe:votre_panier_en_en_cour_de_paiement_vous_ne_pouvez_plus_ajouter_de_produit_a_ce_stade');
	}
	
	$sql_test_lien_panier_auteur = "SELECT sec.id FROM spip_echoppe_client sec WHERE token_client = '".$contexte['token_client']."' ;";
	$res_test_lien_panier_auteur = spip_query($sql_test_lien_panier_auteur);
	if (spip_num_rows($res_test_lien_panier_auteur) < 1){
		$sql_lien = "INSERT INTO spip_echoppe_client VALUES ('','".$contexte['id_auteur']."','".$contexte['token_client']."')";
		$res_lien = spip_query($sql_lien);
		spip_log('liaison de l\'auteur '.$contexte['id_auteur'].' au token '.$contexte['token_client'].' from ajout de produit'.$contexte['token_client'],'echoppe');
	}
	
	redirige_par_entete($contexte['redirect']);
	
}

?>
