<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_echoppe_supprimer_element_panier(){
	$contexte = array();
	$contexte['id_panier'] = _request('id_panier');
	$contexte['token_panier'] = session_get('echoppe_token_panier');
	$contexte['token_client'] = session_get('echoppe_token_client');
	
	$contexte['sql'] = "DELETE FROM spip_echoppe_paniers WHERE id_panier = '".$contexte['id_panier']."' AND token_client = '".$contexte['token_client']."' AND token_panier = '".$contexte['token_panier']."';";
	//echo $contexte['sql'];
	$contexte['res'] = spip_query($contexte['sql']);
	
	redirige_par_entete(generer_url_public(lire_config('echoppe/squelette_panier', 'echoppe_panier')));
	
	
		
}

?>
