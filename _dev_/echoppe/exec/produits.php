<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/echoppe');
include_spip('inc/commencer_page');
include_spip('inc/presentation');

function exec_produits(){
		if ($GLOBALS['meta']['version_installee'] <= '1.927'){
		echo debut_page(_T('echoppe:les_produits'), "redacteurs", "echoppe");	
	}else{
		echo inc_commencer_page_dist(_T('echoppe:les_produits'), "redacteurs", "echoppe");
	}
	
	echo debut_gauche();
	
	
	
	echo fin_gauche();
	echo fin_page();
}

?>
