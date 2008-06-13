<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/echoppe');
include_spip('inc/commencer_page');
include_spip('inc/presentation');
include_spip('public/assembler');

function exec_echoppe_prestataire_paiement(){

	$contexte = array();
	$contexte['id_prestataire_paiement'] = _request('id_prestataire');
	$contexte['new'] = _request('new');
	
	$sql_le_prestataire_paiement = "SELECT * FROM spip_echoppe_prestataires_paiement WHERE id_prestataire = '".$contexte['id_prestataire_paiement']."';";
	$res_le_prestataire_paiement = spip_query($sql_le_prestataire_paiement);
	$le_prestataire_paiement = spip_fetch_array($res_le_prestataire_paiement);
	$adresse_prestataire_paiement = unserialize($le_prestataire_paiement['adresse']);
	(is_array($le_prestataire_paiement))?$contexte = array_merge($contexte, $le_prestataire_paiement):$contexte = $contexte;
	(is_array($adresse_prestataire_paiement))?$contexte = array_merge($contexte, $adresse_prestataire_paiement):$contexte = $contexte;
	
	
	
	if (spip_num_rows($res_le_prestataire_paiement) != 1 && $contexte['new'] != "oui"){
		die(inc_commencer_page_dist(_T('echoppe:les_prestataire_paiements'), "redacteurs", "echoppe")._T('echoppe:pas_de_prestataire_paiement_ici').fin_page());
	}
	
	if ($GLOBALS['meta']['version_installee'] <= '1.927'){
		echo debut_page($contexte['titre'], "redacteurs", "echoppe");	
	}else{
		echo inc_commencer_page_dist($contexte['titre'], "redacteurs", "echoppe");
	}
	
	
	echo debut_gauche();
	
	//echo recuperer_fond('fonds/echoppe_prestataire_paiement',$contexte);
	echo debut_boite_info();
	echo recuperer_fond('fonds/echoppe_info_prestataire_paiement', $contexte);
	echo fin_boite_info();
	
	include_spip('inc/echoppe_raccourcis');
	$raccourcis = generer_raccourcis_echoppe();
	echo bloc_des_raccourcis($raccourcis);
	
	echo creer_colonne_droite();
	
	echo debut_droite(_T('echoppe:visualisation_d_un_prestataire_paiement'));
	//echo gros_titre($contexte['titre']);
	
	echo recuperer_fond('fonds/echoppe_prestataire_paiement', $contexte);
	echo fin_gauche();
	echo fin_page();
	
}

?>
