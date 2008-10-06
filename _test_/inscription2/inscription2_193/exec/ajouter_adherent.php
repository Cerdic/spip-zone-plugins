<?php
/**
* Plugin Inscription2
*
* Copyright (c) 2007-2008
* Sergio and co
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
* Pour plus de details voir le fichier COPYING.txt.
*  
**/
include_spip('public/assembler');
function exec_ajouter_adherent() {

	global $connect_statut, $connect_toutes_rubriques, $table_prefix;

	if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)) {
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}
	
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('inscription2:gestion_adherents'), "", "", "");
	
	echo recuperer_fond('prive/nouveau_adherent');
	
	echo fin_page();
}
?>
