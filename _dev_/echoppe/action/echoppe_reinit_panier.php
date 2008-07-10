<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_echoppe_reinit_panier(){
	
	include_spip('inc/session');
	
	$contexte = array();
	$contexte['token_panier'] = session_get('echoppe_token_panier');	
	$contexte['redirect'] = generer_url_public(lire_config('echoppe/squelette_panier','echoppe_panier'));	
	
	$sql_vide_panier = "DELETE FROM spip_echoppe_paniers WHERE token_panier='".$contexte['token_panier']."';" ;
	$res_vide_panier = spip_query($sql_vide_panier);
	session_set('echoppe_statut_panier', 'temp' );
	
	redirige_par_entete($contexte['redirect']);
	
}

?>
