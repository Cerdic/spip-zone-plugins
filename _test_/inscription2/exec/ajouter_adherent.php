<?php

include_spip('public/assembler');
function exec_ajouter_adherent() {

	global $connect_statut, $connect_toutes_rubriques, $table_prefix;

	if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)) {
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}
	
	debut_page(_T('inscription2:gestion_adherents'), "", "");
	
	echo recuperer_fond('inc/nouveau_adherent');
	
	fin_page();
}
?>
