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
	$contexte['onglet'] = _request('onglet');
	
	$res_leproduit = sql_select('*','spip_echoppe_produits','id_produit = '.$contexte['id_produit']);
	$contexte = array_merge($contexte,sql_fetch($res_leproduit));
	
	if (sql_count($res_leproduit) != 1 && $contexte['new'] != "oui"){
		die(inc_commencer_page_dist(_T('echoppe:les_produits'), "redacteurs", "echoppe")._T('echoppe:pas_de_produit_ici').fin_page());
	}
	

	echo inc_commencer_page_dist($contexte['titre'], "redacteurs", "echoppe");
	
	
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
	echo gros_titre($contexte['titre'],'',false);
	echo recuperer_fond('fonds/echoppe_onglets_produit',$contexte);
	switch ($contexte['onglet']){
		case 'options' :
			echo recuperer_fond('fonds/echoppe_produit_options', $contexte);
		break;
		
		case 'stocks' :
			echo recuperer_fond('fonds/echoppe_produit_stocks', $contexte);
		break;
		
		case 'statistiques' :
			echo recuperer_fond('fonds/echoppe_produit_statistiques', $contexte);
		break;
		
		case 'infos' :
			echo recuperer_fond('fonds/echoppe_produit_infos', $contexte);
		break;
		
		default :
			echo recuperer_fond('fonds/echoppe_produit', $contexte);
		break;
	}
	
	
	
	echo fin_gauche(true);
	echo fin_page(true);
	
}

?>
