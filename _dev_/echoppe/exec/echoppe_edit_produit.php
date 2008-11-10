<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/echoppe');
include_spip('inc/commencer_page');
include_spip('inc/presentation');
include_spip('public/assembler');

function exec_echoppe_edit_produit(){
	
	$contexte = array();
	$contexte['id_produit'] = _request('id_produit');
	$contexte['ref_produit'] = _request('ref_produit');
	$contexte['id_trad'] = _request('id_trad');
	$contexte['id_categorie'] = _request('id_categorie');
	$contexte['lang_produit'] = _request('lang_produit');
	$contexte['new'] = _request('new');
	
	$res_le_produit = sql_select("*", "spip_echoppe_produits", "id_produit=" . sql_quote($contexte['id_produit']) . " OR ref_produit = '".sql_quote($contexte['ref_produit'])."'");
	if (sql_count($res_le_produit) == 0 && $contexte['new'] != "oui"){
		die(inc_commencer_page_dist(_T('echoppe:les_produits'), "redacteurs", "echoppe")._T('echoppe:pas_de_produit_ici').fin_page());
	}
	$le_produit = sql_fetch($res_le_produit);
	
	(is_array($le_produit))?$contexte = array_merge($contexte, $le_produit):$contexte = $contexte;
	
	$contexte['action'] = 'echoppe_sauver_produit';
	
	echo inc_commencer_page_dist(_T('echoppe:les_produits'), "redacteurs", "echoppe");
	
	echo debut_gauche('',true);
	
	
	include_spip('inc/echoppe_raccourcis');
	$raccourcis = generer_raccourcis_echoppe();
	echo bloc_des_raccourcis($raccourcis);
	
	echo creer_colonne_droite(true);
	echo debut_droite(_T('echoppe:edition_de_produit'),true);
	echo recuperer_fond('fonds/echoppe_edit_produit', $contexte);

	echo fin_gauche(true);
	echo fin_page(true);
}

?>
