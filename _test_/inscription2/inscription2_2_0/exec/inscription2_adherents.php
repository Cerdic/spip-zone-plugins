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

function exec_inscription2_adherents() {

	global $connect_statut, $connect_toutes_rubriques;

	if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)) {
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}
	
	$commencer_page = charger_fonction('commencer_page', 'inc');
	
	$contexte['case'] = _request('case');
	$contexte['valeur'] = _request('valeur');

	$contexte = array_merge($contexte,$_GET);
	
	$commencer_page = charger_fonction('commencer_page', 'inc');
	
	pipeline('exec_init',array('args'=>$_GET,'data'=>''));
	
	echo $commencer_page(_T('inscription2:gestion_adherents'), "", "", "");
	
	echo recuperer_fond('prive/table_adherents',$contexte);
}
?>