<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_echoppe_maj_panier(){
	
	include_spip('inc/session');
	include_spip('inc/session');
	
	$contexte = array();
	$contexte['token_panier'] = _request('token_panier');
	$contexte['statut_panier'] = _request('echoppe_statut_panier');
	$contexte['redirect'] = _request('redirect');
	
	$contexte['sql_maj_panier'] = "UPDATE spip_echoppe_paniers SET statut = '".$contexte['statut_panier']."' WHERE token_panier = '".$contexte['token_panier']."' ; ";
	$contexte['res_maj_panier'] = spip_query($contexte['sql_maj_panier']);
	
	redirige_par_entete($contexte['redirect']);
	
}

?>
