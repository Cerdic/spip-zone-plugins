<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/echoppe');
include_spip('inc/commencer_page');
include_spip('inc/presentation');
include_spip('public/assembler');

function exec_echoppe_produit(){

	$contexte = array();
	$contexte['id_produit'] = _request('id_produit');
	$contexte['lang_produit'] = _request('lang_produit');
	/*
	$sql_lien_produit_categorie = "SELECT id_categorie FROM spip_echoppe_categories_produits WHERE id_produit = '".$contexte['id_produit']."';";
	//echo $sql_lien_produit_categorie;
	$res_lien_produit_categorie = spip_query($sql_lien_produit_categorie);
	$lien_produit_categorie = spip_fetch_array($res_lien_produit_categorie);
	$contexte['id_categorie'] = $lien_produit_categorie['id_categorie'];

	$sql_le_produit = "SELECT * FROM spip_echoppe_produits WHERE id_produit = '".$contexte['id_produit']."';";
	$res_le_produit = spip_query($sql_le_produit);
	$le_produit = spip_fetch_array($res_le_produit);
	(is_array($le_produit))?$contexte = array_merge($contexte, $le_produit):$contexte = $contexte;
	
	$sql_description_produit = "SELECT * FROM spip_echoppe_produits_descriptions WHERE id_produit = '".$contexte['id_produit']."' AND lang = '".$contexte['lang_produit']."';";
	$res_description_produit = spip_query($sql_description_produit);
	$description_produit = spip_fetch_array($res_description_produit);
	(is_array($description_produit))?$contexte = array_merge($contexte,$description_produit):$contexte = $contexte;
	
	$contexte['action'] = 'echoppe_sauve_general_produit';
	
	*/
	
	$res_leproduit = sql_select('*','spip_echoppe_produits','id_produit = '.$contexte['id_produit']);
	$contexte = array_merge($contexte,sql_fetch($res_leproduit));
	
	if (sql_count($res_leproduit) != 1 && $contexte['new'] != "oui"){
		die(inc_commencer_page_dist(_T('echoppe:les_produits'), "redacteurs", "echoppe")._T('echoppe:pas_de_produit_ici').fin_page());
	}
	

	echo inc_commencer_page_dist($contexte['titre'], "redacteurs", "echoppe");
	
	/*echo debut_grand_cadre();
	echo recuperer_fond('fonds/echoppe_chemin_categorie',$contexte);
	echo fin_grand_cadre();*/
	
	debut_grand_cadre(true);
		echo recuperer_fond('fonds/echoppe_chemin_categorie',$contexte);
	fin_grand_cadre(true);
	
	echo debut_gauche('',true);
	
	echo debut_boite_info(true);
	echo recuperer_fond('fonds/echoppe_info_produit', $contexte);
	echo fin_boite_info(true);
	
	echo recuperer_fond('fonds/echoppe_logo_produit',$contexte);
	
	include_spip('inc/echoppe_raccourcis');
	$raccourcis = generer_raccourcis_echoppe();
	echo bloc_des_raccourcis($raccourcis);
	
	echo creer_colonne_droite(true);
	
	echo debut_droite(false,_T('echoppe:visualisation_d_un_produit'),'');
	//echo gros_titre($contexte['titre']);
	
	echo recuperer_fond('fonds/echoppe_produit', $contexte);
	echo recuperer_fond('fonds/echoppe_options_produit', $contexte);
	echo fin_gauche(true);
	echo fin_page(true);
	
}

?>
