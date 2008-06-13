<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/echoppe');
include_spip('inc/commencer_page');
include_spip('inc/presentation');
include_spip('public/assembler');

function exec_echoppe_depot(){

	$contexte = array();
	$contexte['id_depot'] = _request('id_depot');
	$contexte['new'] = _request('new');
	
	$sql_le_depot = "SELECT * FROM spip_echoppe_depots WHERE id_depot = '".$contexte['id_depot']."';";
	$res_le_depot = spip_query($sql_le_depot);
	$le_depot = spip_fetch_array($res_le_depot);
	$adresse_depot = unserialize($le_depot['adresse']);
	(is_array($le_depot))?$contexte = array_merge($contexte, $le_depot):$contexte = $contexte;
	(is_array($adresse_depot))?$contexte = array_merge($contexte, $adresse_depot):$contexte = $contexte;
	
	
	
	if (spip_num_rows($res_le_depot) != 1 && $contexte['new'] != "oui"){
		die(inc_commencer_page_dist(_T('echoppe:les_depots'), "redacteurs", "echoppe")._T('echoppe:pas_de_depot_ici').fin_page());
	}
	
	if ($GLOBALS['meta']['version_installee'] <= '1.927'){
		echo debut_page($contexte['titre'], "redacteurs", "echoppe");	
	}else{
		echo inc_commencer_page_dist($contexte['titre'], "redacteurs", "echoppe");
	}
	
	
	echo debut_gauche();
	
	//echo recuperer_fond('fonds/echoppe_depot',$contexte);
	echo debut_boite_info();
	echo recuperer_fond('fonds/echoppe_info_depot', $contexte);
	echo fin_boite_info();
	
	include_spip('inc/echoppe_raccourcis');
	$raccourcis = generer_raccourcis_echoppe();
	echo bloc_des_raccourcis($raccourcis);
	
	echo creer_colonne_droite();
	
	echo debut_droite(_T('echoppe:visualisation_d_un_depot'));
	//echo gros_titre($contexte['titre']);
	
	echo recuperer_fond('fonds/echoppe_depot', $contexte);
	echo fin_gauche();
	echo fin_page();
	
}

?>
