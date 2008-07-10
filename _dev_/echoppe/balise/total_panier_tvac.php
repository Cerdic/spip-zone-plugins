<?php

if (!defined("_ECRIRE_INC_VERSION")) return;    #securite

// http://doc.spip.org/@balise_URL_LOGOUT
function balise_TOTAL_PANIER_TVAC ($p) {return calculer_balise_dynamique($p,'TOTAL_PANIER_TVAC', array());
}

// $args[0] = url destination apres logout [(#URL_LOGOUT{url})]
// http://doc.spip.org/@balise_URL_LOGOUT_stat
function balise_TOTAL_PANIER_TVAC_stat ($args, $filtres) {
    return array($args[0]);
}

// http://doc.spip.org/@balise_URL_LOGOUT_dyn
function balise_TOTAL_PANIER_TVAC_dyn($cible) {
	$_sql = "SELECT id_produit, quantite FROM spip_echoppe_paniers WHERE token_panier='".session_get('echoppe_token_panier')."' AND token_client = '".session_get('echoppe_token_client')."' ;";
	$_res = spip_query($_sql);
	//echo $_sql;
	$total_panier = 0;
	while ($_produit = spip_fetch_array($_res)){
		$_sql_le_produit = "SELECT prix_base_htva FROM spip_echoppe_produits WHERE id_produit = '".$_produit['id_produit']."';";
		//echo $_sql_le_produit;
		$_res_le_produit = spip_query($_sql_le_produit);
		$_le_produit = spip_fetch_array($_res_le_produit);
		$total_panier = $total_panier + ($_produit['quantite'] * calculer_prix_tvac($_le_produit['prix_base_htva'], 0));
	}
    return zero_si_vide($total_panier);
}
?>
