<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_echoppe_maj_panier(){
	
	include_spip('inc/session');
	
	$contexte = array();
	$contexte['token_panier'] = session_get('echoppe_token_panier');
	$contexte['token_client'] = session_get('echoppe_token_client');
	$contexte['statut_panier'] = _request('echoppe_statut_panier');
	$contexte['redirect'] = _request('redirect');
	$contexte['quantite'] = _request('quantite');
	$contexte['produits'] = _request('produit');
	
	spip_log('Maj du panier '.$contexte['token_panier'].' ?');
	
	$contexte['sql_list_panier'] = "SELECT * FROM spip_echoppe_paniers WHERE token_client = '".$contexte['token_client']."' AND token_panier = '".$contexte['token_panier']."';";
	$contexte['res_list_panier'] = spip_query($contexte['sql_list_panier']);
	while ($le_item = spip_fetch_array($contexte['res_list_panier'])){
		
		if ($le_item['quantite'] != $contexte['quantite'][$le_item['id_produit']] && $contexte['quantite'][$le_item['id_produit']] > 0){
			spip_log('Maj du panier '.$contexte['token_panier'],'echoppe');
			$contexte['maj_'.$contexte['quantite'][$le_item['id_produit']]] = "UPDATE spip_echoppe_paniers SET quantite = '".$contexte['quantite'][$le_item['id_produit']]."' WHERE token_client = '".$contexte['token_client']."' AND token_panier = '".$contexte['token_panier']."' AND id_produit = '".$le_item['id_produit']."' ;";
			$contexte['res_'.$contexte['quantite'][$le_item['id_produit']]] = spip_query($contexte['maj_'.$contexte['quantite'][$le_item['id_produit']]]);
		}
		
	}
	spip_log('Maj du panier ok');
	
	/* --- old ---
	 * 
	 * $contexte['sql_maj_panier'] = "UPDATE spip_echoppe_paniers SET statut = '".$contexte['statut_panier']."' WHERE token_panier = '".$contexte['token_panier']."' ; ";
	 * $contexte['res_maj_panier'] = spip_query($contexte['sql_maj_panier']);
	 * 

	var_dump($contexte);	
*/
	redirige_par_entete(generer_url_public(lire_config('echoppe/squelette_panier, echoppe_panier','echoppe_panier')));
	
}

?>
