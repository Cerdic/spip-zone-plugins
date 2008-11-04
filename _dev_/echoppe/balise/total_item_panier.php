<?php

if (!defined("_ECRIRE_INC_VERSION")) return;    #securite

// http://doc.spip.org/@balise_URL_LOGOUT
function balise_TOTAL_ITEM_PANIER ($p) {return calculer_balise_dynamique($p,'TOTAL_ITEM_PANIER', array());
}

// $args[0] = url destination apres logout [(#URL_LOGOUT{url})]
// http://doc.spip.org/@balise_URL_LOGOUT_stat
function balise_TOTAL_ITEM_PANIER_stat ($args, $filtres) {
    return array($args[0]);
}

// http://doc.spip.org/@balise_URL_LOGOUT_dyn
function balise_TOTAL_ITEM_PANIER_dyn($cible) {

	$_sql = "SELECT id_produit FROM spip_echoppe_paniers WHERE token_panier = '".session_get('echoppe_token_panier')."';";
	//echo $_sql;
	$_res = sql_query($_sql);
	//var_dump($_res);
	$_quantite = sql_count($_res);
	//var_dump($_quantite);
	zero_si_vide($_quantite);
	//var_dump($_quantite);
	//$_quantite = "Bliiiii";
    return $_quantite;
}
?>
