<?php
/**
* Copyright (c) 2007
* BoOz  
**/
include_spip('public/assembler');
function exec_abonnement_stats() {

	global $connect_statut, $connect_toutes_rubriques, $table_prefix;

	if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)) {
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}
	

	debut_page("abonnements", "", "");

	echo recuperer_fond('inc/abonnement_stats');
	
	fin_page();
}
?>