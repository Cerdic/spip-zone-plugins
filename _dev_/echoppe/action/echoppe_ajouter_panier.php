<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_echoppe_ajouter_panier(){
	
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
	
	$le_produit_existant = sql_fetsel(array("*"),"spip_echoppe_paniers","id_produit = '".$contexte['id_produit']."'");
	$contexte['id_panier'] = $le_produit_existant['id_panier'];
	
	$_page = lire_config('echoppe/squelette_panier','echoppe_panier');
	$contexte['redirect'] = generer_url_public($_page);
	
	if (session_get('echoppe_statut_panier') == "temp"){
		if (sql_count($res_test_existance_produit) >= 1){
			if ($contexte['achat_rapide'] == "oui"){ $contexte['quantite'] = $le_produit_existant['quantite'] + $contexte['quantite']; }
			$sql_maj_produit_panier = "UPDATE spip_echoppe_paniers SET quantite = '".$contexte['quantite']."', configuration = '".$contexte['configuration']."', statut = '".$contexte['statut_panier']."', date ='".$contexte['date_maj']."' WHERE id_panier = '".$contexte['id_panier']."' AND token_panier = '".$contexte['token_panier']."'; ";
			//var_dump($sql_maj_produit_panier);
			$res_maj_produit_panier = spip_query($sql_maj_produit_panier);
			
		}else{
			$panier = array(
				'id_client' => $contexte['id_client'],
				'id_produit' => $contexte['id_produit'],
				'quantite' => $contexte['quantite'],
				'configuration' => $contexte['configuration'],
				'token_client' => $contexte['token_client'],
				'token_panier' => $contexte['token_panier'],
				'statut' => session_get('echoppe_statut_panier'),
				'date_maj' => $contexte['date_maj'],
			);
			$contexte['id_panier'] = sql_insertq("spip_echoppe_paniers",$panier);
			
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

	if (sql_count(sql_select(array("id_auteur"),"spip_echoppe_clients","token_client = '".$contexte['token_client']."'")) <= 1){
		$sql_lien = "INSERT INTO spip_echoppe_clients VALUES ('','".$contexte['id_auteur']."','".$contexte['token_client']."')";
		$res_lien = spip_query($sql_lien);
		spip_log('liaison de l\'auteur '.$contexte['id_auteur'].' au token '.$contexte['token_client'].' from ajout de produit'.$contexte['token_client'],'echoppe');
	}
	
	redirige_par_entete($contexte['redirect']);
	
}

?>
