<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_echoppe_ajouter_panier(){
	
	include_spip('inc/session');
	include_spip('inc/echoppe_panier');
	
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
	
	$res_le_produit_existant = sql_select(array("quantite"),"spip_echoppe_paniers","id_produit = '".$contexte['id_produit']."' AND token_panier = '".$contexte['token_panier']."'");
	$le_produit_existant = sql_fetch($res_le_produit_existant);
	
	$contexte['id_panier'] = $le_produit_existant['id_panier'];
	
	$contexte['quantite'] = $contexte['quantite'] + $le_produit_existant['quantite'];
	
	if (echoppe_panier_autoriser_modification($contexte['token_panier'])){
		if (sql_count($res_le_produit_existant) == 1){
			if (echoppe_panier_valider_disponibilite_produit($contexte['id_produit'])){
				echoppe_panier_modifier_quantite_produit($contexte['id_produit'], $contexte['quantite'], $contexte['token_panier']);
				spip_log('envoie à la modif',"echoppe");
			}else{
				$contexte['message_erreur'] = _T('echoppe:produit_non_disponible');
				
			}
		}elseif (sql_count($res_le_produit_existant) == 0){
			if (echoppe_panier_valider_disponibilite_produit($contexte['id_produit'])){
				$quantite_originale = sql_fetch($res_le_produit_existant);
				$contexte['quantite'] = $contexte['quantite'] + $quantite_originale['quantite'];
				echoppe_panier_ajouter_produit($contexte['id_produit'], $contexte['quantite'], $contexte['id_client'], $contexte['token_panier'], $contexte['token_client'], session_get('echoppe_statut_panier'));
			}else{
				$contexte['message_erreur'] = _T('echoppe:produit_non_disponible');
			}
		}elseif (sql_count($res_le_produit_existant) == 0){
			
			$contexte['message_erreur'] = "ECHOPPE ERROR !";
			spip_log('ECHOPPE_ERROR : formulaire_panier => plus d\'1 enregistrement pour un produit dans le panier '.$contexte['id_panier'], 'echoppe');
			
		}
		
	}else{
		$contexte['message_erreur'] = _T('echoppe:votre_panier_en_en_cour_de_paiement_vous_ne_pouvez_plus_ajouter_de_produit_a_ce_stade');
	}

	if (sql_count(sql_select(array("id_auteur"),"spip_echoppe_clients","token_client = '".$contexte['token_client']."'")) < 1){
		$sql_lien = "INSERT INTO spip_echoppe_clients VALUES ('','".$contexte['id_auteur']."','".$contexte['token_client']."')";
		$res_lien = sql_insertq('spip_echoppe_clients',array('id_auteur' => $contexte['id_auteur'], 'token_client' => $contexte['token_client']));
		spip_log('liaison de l\'auteur '.$contexte['id_auteur'].' au token *** from ajout de produit ***','echoppe');
	}
	
	redirige_par_entete($contexte['redirect']);
	
}

?>
