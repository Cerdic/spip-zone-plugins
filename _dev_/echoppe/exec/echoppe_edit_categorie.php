<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/echoppe');
include_spip('inc/commencer_page');
include_spip('inc/presentation');
include_spip('public/assembler');
include_spip('inc/barre');

function exec_echoppe_edit_categorie(){
	
	$contexte = array();
	$contexte['lang_categorie'] = _request('lang_categorie');
	$contexte['id_parent'] = _request('id_parent');
	$contexte['id_categorie'] = _request('id_categorie');
	$contexte['new'] = _request('new');
	
	if ($contexte['new'] != "oui"){
		
		$sql_select_categorie = sql_select("*", "spip_echoppe_categories", "id_categorie =" . $contexte['id_categorie']);
		$categorie = sql_fetch($sql_select_categorie);
		(is_array($categorie))?$contexte = array_merge($contexte,$categorie):$contexte = $contexte;
		
		
	}
	
	if ($GLOBALS['meta']['version_installee'] <= '1.927'){
		echo debut_page(_T('echoppe:les_categories'), "redacteurs", "echoppe");	
	}else{
		echo inc_commencer_page_dist(_T('echoppe:les_categories'), "redacteurs", "echoppe");
	}

	echo debut_gauche('',true);	
	
	include_spip('inc/echoppe_raccourcis');
	$raccourcis = generer_raccourcis_echoppe();
	echo bloc_des_raccourcis($raccourcis);
	
	echo creer_colonne_droite(true);
	echo debut_droite(_T('echoppe:edition_de_cetegorie'),true);
	
	echo recuperer_fond('fonds/echoppe_edit_categorie',$contexte);
	
	echo fin_gauche(true);
	echo fin_page(true);
	
}

?>
