<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/echoppe');
include_spip('inc/commencer_page');
include_spip('inc/presentation');
include_spip('public/assembler');

function exec_echoppe_edit_produit(){
	
	$id_produit = _request('id_produit');
	$id_categorie = _request('id_categorie');
	$lang_produit = _request('lang_produit');
	$new = _request('new');

	$sql_le_produit = "SELECT * FROM spip_echoppe_produits WHERE id_produit = '".$id_produit."';";
	$res_le_produit = spip_query($sql_le_produit);
	if (spip_num_rows($res_le_produit) == 0 && $new != "oui"){
		die(inc_commencer_page_dist(_T('echoppe:les_produits'), "redacteurs", "echoppe")._T('echoppe:pas_de_produit_ici').fin_page());
	}
	
	(spip_num_rows($res_descriptif_produit) > 0)?$new = $new:$new = 'description';
	
	
	if ($GLOBALS['meta']['version_installee'] <= '1.927'){
		echo debut_page(_T('echoppe:les_produits'), "redacteurs", "echoppe");	
	}else{
		echo inc_commencer_page_dist(_T('echoppe:les_produits'), "redacteurs", "echoppe");
	}
	
	echo debut_gauche();

	echo creer_colonne_droite();
	echo debut_droite(_T('echoppe:edition_de_produit'));
	echo gros_titre(_T("echoppe:edition_de_produit"));
	

	
	echo recuperer_fond('fonds/echoppe_edit_produit');


	
	echo fin_gauche();
	echo fin_page();
}

?>
