<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_echoppe_upgrd_panier(){
	
	include_spip('inc/session');
	
	$contexte = array();
	$contexte['token_panier'] = _request('token_panier');
	$contexte['statut_panier'] = _request('echoppe_statut_panier');
	$contexte['redirect'] = _request('redirect');
	
	spip_log('upgr du panier '.$contexte['token_panier'].' ?');

	$contexte['maj'] = "UPDATE spip_echoppe_paniers SET statut = '".$contexte['statut_panier']."' WHERE token_panier = '".$contexte['token_panier']."' ;";
	$contexte['res'] = spip_query($contexte['maj']);

	
	/* --- old ---
	 * 
	 * $contexte['sql_maj_panier'] = "UPDATE spip_echoppe_paniers SET statut = '".$contexte['statut_panier']."' WHERE token_panier = '".$contexte['token_panier']."' ; ";
	 * $contexte['res_maj_panier'] = spip_query($contexte['sql_maj_panier']);
	 * 

	var_dump($contexte);	
*/
	redirige_par_entete($contexte['redirect']);
	
}

?>
