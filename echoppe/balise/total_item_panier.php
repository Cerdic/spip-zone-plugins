<?php

if (!defined("_ECRIRE_INC_VERSION")) return;    #securite


// http://code.spip.net/@balise_URL_LOGOUT
function balise_TOTAL_ITEM_PANIER ($p) {return calculer_balise_dynamique($p,'TOTAL_ITEM_PANIER', array());
}

// $args[0] = url destination apres logout [(#URL_LOGOUT{url})]
// http://code.spip.net/@balise_URL_LOGOUT_stat
function balise_TOTAL_ITEM_PANIER_stat ($args, $filtres) {
    return array($args[0]);
}

// http://code.spip.net/@balise_URL_LOGOUT_dyn
function balise_TOTAL_ITEM_PANIER_dyn($cible) {
	include_spip('inc/echoppe');
	$select_produit = sql_select('quantite','spip_echoppe_paniers',"token_panier = '".session_get('echoppe_token_panier')."'");
	
	$_quantite = 0;
	
	while($quantite = sql_fetch($select_produit)){
		$_quantite = $_quantite + $quantite['quantite'];
	}
	
    return zero_si_vide($_quantite);
}
?>
