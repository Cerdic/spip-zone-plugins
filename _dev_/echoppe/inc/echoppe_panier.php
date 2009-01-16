<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * 
 * Securite
 * 
 * */

function echoppe_panier_autoriser_modification($token_panier){
	return true;
}

function echoppe_panier_valider_disponibilite_produit($id_produit){
	return true;
}

/**
 * 
 * Modification du panier
 * 
 * */

function echoppe_panier_ajouter_produit($id_produit, $quantite = 1, $id_client, $token_panier, $token_client, $statut){
	$date_maj = date("Y-m-d h:i:s");
	
	$panier = array(
		'id_client' => $id_client,
		'id_produit' => $id_produit,
		'quantite' => $quantite,
		'configuration' => '',
		'token_client' => $token_client,
		'token_panier' => $token_panier,
		'statut' => $statut,
		'date_maj' => $date_maj,
		'date' => $date_maj
	);
	
	$contexte['id_panier'] = sql_insertq("spip_echoppe_paniers",$panier);
}

function echoppe_panier_supprimer_produit($id_produit, $token_panier){
	
	$test_presence_produit_dans_panier = sql_select("*","spip_echoppe_panier",Array("id_produit = '".$id_produit."'", "token_panier = '".$token_panier."'"));
		
	if(sql_count($test_presence_produit_dans_panier) == 1){
		
		sql_delete("spip_echoppe_paniers", Array("id_produit = ".$id_produit, "token_panier = ".$token_panier));
		
	}else{
			
		spip_log('ECHOPPE_ERROR : echoppe_panier_supprimer_produit => plus d\'1 enregistrement pour un produit dans le panier', 'echoppe');
		
		return false;
	}
	
}

function echoppe_panier_modifier_quantite_produit($id_produit, $quantite, $token_panier){
	
	if ($quantite == 0){
		
		echoppe_panier_supprimer_produit($id_produit, $token_panier);
		
	}else{
		
		$date_maj = date("Y-m-d h:i:s");
		
		$test_presence_produit_dans_panier = sql_select("quantite","spip_echoppe_paniers",Array("id_produit = '".$id_produit."'", "token_panier = '".$token_panier."'"));
		
		if(sql_count($test_presence_produit_dans_panier) == 1 ){
			
			$res_maj_produit_panier = sql_updateq('spip_echoppe_paniers',array('quantite'=>$quantite, 'date_maj' => $date_maj), "id_produit = '".$id_produit."' AND token_panier = '".$token_panier."'");	
			
			return true;
			
		}else{
			if(sql_count($test_presence_produit_dans_panier) > 1 ){
				spip_log('ECHOPPE_ERROR : echoppe_panier_modifier_quantite_produit => plus d\'1 enregistrement pour un produit dans le panier', 'echoppe');
				return false;
			}else{
				spip_log('ECHOPPE_WARNING : echoppe_panier_modifier_quantite_produit => produit pas encore dans le panier', 'echoppe');
				return false;
			}
			
		}
	}
		
}

function echoppe_panier_vider($token_panier){
	
}

?>
