<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/echoppe');
include_spip('inc/commencer_page');


function exec_echoppe_categorie(){
	
	if ($GLOBALS['meta']['version_installee'] <= '1.927'){
		echo debut_page(_T('echoppe:les_categories'), "redacteurs", "echoppe");	
	}else{
		echo inc_commencer_page_dist(_T('echoppe:les_categories'), "redacteurs", "echoppe");
	}
	
	echo debut_gauche();
	
	echo debut_boite_info();
	echo (_T('echoppe:descriptif_echoppe'));
	echo fin_boite_info();
	
	
	echo creer_colonne_droite();
	echo debut_droite(_T('echoppe:echoppe'));
	echo gros_titre(_T("echoppe:les_categories"));
	
	if ($GLOBALS['connect_statut'] == "0minirezo"){
		echo 'Bonjour le monde !';
	}else{
		echo echoppe_echec_autorisation();
	}
	
	echo fin_gauche();
	echo fin_page();	
}

?>
