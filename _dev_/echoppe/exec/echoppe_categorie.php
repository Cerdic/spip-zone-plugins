<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/echoppe');
include_spip('inc/commencer_page');
include_spip('inc/filtres');
include_spip('public/assembler');

function exec_echoppe_categorie(){
	
	if ($GLOBALS['connect_statut'] != "0minirezo"){
		echo(echoppe_echec_autorisation().fin_page());
		return;
	}
	
	$contexte['lang_categorie'] = _request('lang_categorie');
	$contexte['id_categorie'] = _request('id_categorie');
	$contexte['new'] = _request('new');
	
	/*$sql_test_categorie_existe = "SELECT * FROM spip_echoppe_categories WHERE id_categorie = '".$contexte['id_categorie']."';";
	$res_test_categorie_existe = spip_query($sql_test_categorie_existe);
	if (sql_count ($res_test_categorie_existe) != 1 && $new != 'oui'){
		die(inc_commencer_page_dist(_T('echoppe:les_categories'), "redacteurs", "echoppe")._T('echoppe:pas_de_categorie_ici').fin_page());
	}*/
	
	$sql_select_categorie = sql_select("*", "spip_echoppe_categories", "id_categorie =" . $contexte['id_categorie']);
	$categorie = sql_fetch($sql_select_categorie);
	(is_array($categorie))?$contexte = array_merge($contexte,$categorie):$contexte = $contexte;
	/*
	$date_derniere_modification = affdate($contexte['maj']);
	if (empty($date_derniere_modification)){
		$date_derniere_modification = _T('echoppe:pas_encore_cree');
	}else{
		if($date_derniere_modification == 0){
			$date_derniere_modification = _T('echoppe:pas_encore_modifie');
		}
	}
	*/
	
	echo inc_commencer_page_dist($contexte['titre'], "redacteurs", "echoppe");
	
	
	
	if ($contexte['id_categorie'] != "0"){
		debut_grand_cadre(true);
		echo recuperer_fond('prive/general/echoppe_chemin_categorie',$contexte);
		fin_grand_cadre(true);
	}
	
	echo debut_gauche('',true);
	
	if ($contexte['id_categorie'] != "0"){
			echo debut_boite_info(true);
				echo recuperer_fond('prive/categorie/info/echoppe_info_categorie',$contexte);
			echo fin_boite_info(true);
			echo recuperer_fond('fonds/echoppe_logo_categorie',$contexte);
	}else{
		echo debut_boite_info(true);
			echo recuperer_fond('fonds/descriptif_echoppe');
		echo fin_boite_info(true);
	}
	
	include_spip('inc/echoppe_raccourcis');
	$raccourcis = generer_raccourcis_echoppe();
	echo bloc_des_raccourcis($raccourcis);
	
	echo creer_colonne_droite(true);
	
	echo debut_droite(true,_T('echoppe:echoppe'));
	if ($contexte['id_categorie'] == "0"){
		echo recuperer_fond('prive/general/echoppe_echoppe', $contexte);
	}else{
		echo recuperer_fond('prive/categorie/voir/echoppe_categorie', $contexte);
	}
	echo fin_gauche(true);
	echo fin_page(true);	
}

?>
