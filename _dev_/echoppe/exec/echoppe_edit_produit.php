<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/echoppe');
include_spip('inc/commencer_page');
include_spip('inc/presentation');
include_spip('public/assembler');

function exec_echoppe_edit_produit(){
	
	$contexte = array();
	$contexte['id_produit'] = _request('id_produit');
	$contexte['id_categorie'] = _request('id_categorie');
	$contexte['lang_produit'] = _request('lang_produit');
	$contexte['new'] = _request('new');
	
	
	
	$sql_le_produit = "SELECT * FROM spip_echoppe_produits WHERE id_produit = '".$contexte['id_produit']."';";
	$res_le_produit = spip_query($sql_le_produit);
	if (spip_num_rows($res_le_produit) != 1 && $contexte['new'] != "oui"){
		die(inc_commencer_page_dist(_T('echoppe:les_produits'), "redacteurs", "echoppe")._T('echoppe:pas_de_produit_ici').fin_page());
	}
	
	$le_produit = spip_fetch_array($res_le_produit);
	
	
	$sql_description_produit = "SELECT * FROM spip_echoppe_produits_descriptions WHERE id_produit = '".$contexte['id_produit']."' AND lang = '".$contexte['lang_produit']."';";
	$res_description_produit = spip_query($sql_description_produit);
	$description_produit = spip_fetch_array($res_description_produit);
	//echo $contexte['new'];
	
	(spip_num_rows($res_description_produit) == 0 && $contexte['new'] != 'oui')?$contexte['new'] = 'ajout_description':$contexte['new'] = $contexte['new'];
	(spip_num_rows($res_description_produit) > 0 && $contexte['new'] != 'oui')?$contexte['new'] = 'maj_descriptif':$contexte['new'] = $contexte['new'];
	
	//echo $contexte['new'];
	
	if ($GLOBALS['meta']['version_installee'] <= '1.927'){
		echo debut_page(_T('echoppe:les_produits'), "redacteurs", "echoppe");	
	}else{
		echo inc_commencer_page_dist(_T('echoppe:les_produits'), "redacteurs", "echoppe");
	}
	
	echo debut_gauche();

	echo creer_colonne_droite();
	echo debut_droite(_T('echoppe:edition_de_produit'));
	echo gros_titre(_T("echoppe:edition_de_produit"));
	
	(is_array($le_produit))?$contexte = array_merge($contexte, $le_produit):$contexte = $contexte;
	(is_array($description_produit))?$contexte = array_merge($contexte,$description_produit):$contexte = $contexte;
	
	$contexte['action'] = 'echoppe_sauver_produit';
	
	echo recuperer_fond('fonds/echoppe_edit_produit', $contexte);


	
	echo fin_gauche();
	echo fin_page();
}

?>
