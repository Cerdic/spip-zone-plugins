<?php

include_spip('public/boucles');
include_spip('base/echoppe');
include_spip('inc/session');


//global $tables_jointures;

//$tables_jointures['spip_echoppe_categories'][] = 'spip_echoppe_categories_descriptions';
//$tables_jointures['spip_echoppe_categories_produits'][] = 'spip_echoppe_produits';

function generer_url_validation_panier(){
	return "?page=echoppe_panier";
	
}

function generer_logo($nom_fichier){
	
	$logo = '<img src="IMG/'.$nom_fichier.'" alt="'.textebrut($nom_fichier).'" />';
	if (strlen($nom_fichier) > 0) return $logo;
	
}

function generer_url_inscription(){
	return "";
}

function calculer_prix_tvac($prix_htva, $taux_tva){
	if ($taux_tva == 0){
		$taux_tva = lire_config('echoppe/taux_de_tva_par_defaut',21);
	}
	$prix_ttc = $prix_htva + ($prix_htva * ($taux_tva / 100));
	$prix_ttc = round($prix_ttc, lire_config('echoppe/nombre_chiffre_apres_virgule',2));
	return $prix_ttc;
}

function calculer_taux_tva($taux_tva){
	if ($taux_tva == 0){
		$taux_tva = lire_config('echoppe/taux_de_tva_par_defaut',21);
	}
	return $taux_tva;
}

function vide_si_zero($_var){
	if ($_var == 0){
		$_var = ""; 
	}
	return $_var;
}
function zero_si_vide($_var){
	if ($_var == ""){
		$_var = 0; 
	}
	return $_var;
}
function calculer_url_achat($_var){
	if (isset($_var)){
		$_page = lire_config('echoppe/squelette_panier','echoppe_panier');
		$url_result = generer_url_public($_page,'id_produit='.$_var);
		$url = generer_url_action('echoppe_ajouter_panier','id_produit='.$_var.'&quantite=1&achat_rapide=non');
		return $url;
	}
	
}
function calculer_url_achat_rapide($_var){
	if (isset($_var)){
		$url = generer_url_action('echoppe_ajouter_panier','id_produit='.$_var.'&quantite=1&achat_rapide=oui');
		return $url;
	}
	
}
function calculer_stock($_id_produit){
	$_sql_stock = "SELECT quantite FROM spip_echoppe_stock_produits WHERE id_produit = '$_id_produit';";
	$_res_stock = spip_query($_sql_stock);
	while($_la_quantite = spip_fetch_array($_res_stock)){
		$_quantite = $_quantite + $_la_quantite['quantite'];
	}
	$_quantite = zero_si_vide($_quantite);
	return $_quantite;
}
/*=============================BALISES===============================*/
function balise_PRIX_TVAC($p){
	$_prix = champ_sql('prix_base_htva', $p);
	$_tva = champ_sql('tva', $p);
	$p->code = "calculer_prix_tvac($_prix,$_tva)";
	return $p;
}

function balise_TAUX_TVA($p){
	$_tva = champ_sql('tva', $p);
	$p->code = "calculer_taux_tva($_tva)";
	return $p;
}

function balise_HAUTEUR($p){
	$_hauteur = champ_sql('hauteur', $p);
	$p->code = "vide_si_zero($_hauteur)";
	return $p;
}
function balise_POIDS($p){
	$_poids = champ_sql('poids', $p);
	$p->code = "vide_si_zero($_poids)";
	return $p;
}
function balise_LARGEUR($p){
	$_largeur = champ_sql('largeur', $p);
	$p->code = "vide_si_zero($_largeur)";
	return $p;
}
function balise_LONGUEUR($p){
	$_longueur = champ_sql('longueur', $p);
	$p->code = "vide_si_zero($_longueur)";
	return $p;
}
function balise_URL_ACHAT($p){
	$_id_produit = champ_sql('id_produit', $p);
	$p->code = "calculer_url_achat($_id_produit)";
	return $p;
}
function balise_URL_ACHAT_RAPIDE($p){
	$_id_produit = champ_sql('id_produit', $p);
	$p->code = "calculer_url_achat_rapide($_id_produit)";
	return $p;
}

function generer_URL_PANIER($p){
	$_page = lire_config('echoppe/squelette_panier','echoppe_panier');
	$_url = generer_url_public($_page);
	return $_url;
}

function balise_TOTAL_ITEM_PANIER($p){
	$_sql = "SELECT id_produit FROM spip_echoppe_paniers WHERE token_panier='".session_get('echoppe_token_panier')."' AND token_client = '".session_get('echoppe_token_client')."' ;";
	$_res = spip_query($_sql);
	$_quantite = spip_num_rows($_res);
	$p->code = "zero_si_vide($_quantite)";
	$p->interdire_scripts = false;
	return $p;
}

function balise_TOTAL_STOCK($p){
	$_id_produit = champ_sql('id_produit', $p);
	$p->code = "calculer_stock($_id_produit)";
	$p->interdire_scripts = false;
	return $p;
}

function balise_TOTAL_PANIER_HTVA($p){
	$_sql = "SELECT id_produit, quantite FROM spip_echoppe_paniers WHERE token_panier='".session_get('echoppe_token_panier')."' AND token_client = '".session_get('echoppe_token_client')."' ;";
	$_res = spip_query($_sql);
	//echo $_sql;
	$total_panier = 0;
	while ($_produit = spip_fetch_array($_res)){
		$_sql_le_produit = "SELECT prix_base_htva FROM spip_echoppe_produits WHERE id_produit = '".$_produit['id_produit']."';";
		//echo $_sql_le_produit;
		$_res_le_produit = spip_query($_sql_le_produit);
		$_le_produit = spip_fetch_array($_res_le_produit);
		$total_panier = $total_panier + ($_produit['quantite'] * $_le_produit['prix_base_htva']);
	}
	$p->code = "zero_si_vide($total_panier)";
	$p->interdire_scripts = false;
	return $p;
}

function balise_TOTAL_PANIER_TVAC($p){
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
	$p->code = "zero_si_vide($total_panier)";
	$p->interdire_scripts = false;
	return $p;
}
?>
