<?php

if (!defined("_ECRIRE_INC_VERSION")) return;    #securite

// https://code.spip.net/@balise_URL_LOGOUT
function balise_TOTAL_PANIER_TVAC ($p) {return calculer_balise_dynamique($p,'TOTAL_PANIER_TVAC', array());
}

// $args[0] = url destination apres logout [(#URL_LOGOUT{url})]
// https://code.spip.net/@balise_URL_LOGOUT_stat
function balise_TOTAL_PANIER_TVAC_stat ($args, $filtres) {
    return array($args[0]);
}

// https://code.spip.net/@balise_URL_LOGOUT_dyn
function balise_TOTAL_PANIER_TVAC_dyn($cible) {
	include_spip('inc/echoppe');
	include_spip('base/abstract_sql');
	$row = sql_select(array('spip_echoppe_paniers.id_produit', 'spip_echoppe_paniers.quantite', 'spip_echoppe_produits.prix_base_htva'), 
	'spip_echoppe_paniers LEFT JOIN spip_echoppe_produits ON spip_echoppe_paniers.id_produit = spip_echoppe_produits.id_produit', 
	'token_panier = '.sql_quote(session_get('echoppe_token_panier')).' AND token_client = '.sql_quote(session_get('echoppe_token_client')));
	$total_panier = 0;
	while ($produit = sql_fetch($row)){
		$total_panier = $total_panier + ($produit['quantite'] * calculer_prix_tvac($produit['prix_base_htva'], 0));
	}
    return zero_si_vide($total_panier);
}
?>
