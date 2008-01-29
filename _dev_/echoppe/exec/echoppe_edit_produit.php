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
	
	(spip_num_rows($res_description_produit) == 0 && $contexte['new'] != 'oui')?$contexte['new'] = 'ajout_description':$contexte['new'] = $contexte['new'];
	(spip_num_rows($res_description_produit) > 0 && $contexte['new'] != 'oui')?$contexte['new'] = 'maj_description':$contexte['new'] = $contexte['new'];
	(is_array($le_produit))?$contexte = array_merge($contexte, $le_produit):$contexte = $contexte;
	(is_array($description_produit))?$contexte = array_merge($contexte,$description_produit):$contexte = $contexte;
	
	$contexte['action'] = 'echoppe_sauver_produit';
	
	
	if ($GLOBALS['meta']['version_installee'] <= '1.927'){
		echo debut_page(_T('echoppe:les_produits'), "redacteurs", "echoppe");	
	}else{
		echo inc_commencer_page_dist(_T('echoppe:les_produits'), "redacteurs", "echoppe");
	}
	
	echo debut_gauche();

	/*echo debut_boite_info();
		echo recuperer_fond('fonds/echoppe_info_edit_produit',$contexte);
	echo fin_boite_info();*/
	
	
	($contexte['id_categorie'] > 0)?$raccourcis .= icone_horizontale(_T('echoppe:retour_a_la_categorie'), generer_url_ecrire("echoppe_categorie","id_categorie=".$contexte['id_categorie']."&lang=".$contexte['lang_categorie']), _DIR_PLUGIN_ECHOPPE."images/retour.png","", false)."<hr />":$raccourcis=$raccourcis;
	($contexte['id_produit'] > 0)?$raccourcis .= icone_horizontale(_T('echoppe:retour_au_produit'), generer_url_ecrire("echoppe_produit","id_produit=".$contexte['id_produit']."&lang=".$contexte['lang_produit']), _DIR_PLUGIN_ECHOPPE."images/retour.png","", false)."<hr />":$raccourcis=$raccourcis;	
	$raccourcis .= '<hr />';
	$raccourcis .= icone_horizontale(_T('echoppe:gerer_les_depots'), generer_url_ecrire("echoppe_gerer_depots",""), _DIR_PLUGIN_ECHOPPE."images/go-home.png","", false);
	$raccourcis .= "<hr />";
	$raccourcis .= icone_horizontale(_T('echoppe:gerer_echoppe'), generer_url_ecrire("echoppe",""), _DIR_PLUGIN_ECHOPPE."images/echoppe_blk_24.png","", false);
	echo bloc_des_raccourcis($raccourcis);

	if ($contexte['new'] != 'oui' && $contexte['new'] != 'ajout_description'){
		echo debut_boite_info();
		echo recuperer_fond('fonds/echoppe_logo_produit',$contexte);
		echo fin_boite_info();
	}

	echo creer_colonne_droite();
	echo debut_droite(_T('echoppe:edition_de_produit'));
	echo gros_titre(_T("echoppe:edition_de_produit"));
	
	echo recuperer_fond('fonds/echoppe_edit_produit', $contexte);

	echo fin_gauche();
	echo fin_page();
}

?>
