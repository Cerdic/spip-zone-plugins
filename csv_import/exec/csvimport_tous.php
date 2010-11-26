<?php
/*
 * CSVimport
 * Plug-in d'import csv dans les tables spip et d'export CSV des tables
 *
 * Auteur :
 * Cedric MORIN
 * notre-ville.net
 * © 2005,2009 - Distribue sous licence GNU/GPL
 *
 */

include_spip("inc/csvimport");
include_spip("inc/presentation");

/**
 * Afficher une liste de tables importables / exportables
 */
function exec_csvimport_tous(){
	$icone = _DIR_PLUGIN_CSVIMPORT."img_pack/csvimport-24.png";
	$commencer_page = charger_fonction('commencer_page', 'inc');
	
	pipeline('exec_init',array('args'=>$_GET,'data'=>''));
	
	echo $commencer_page(_T("csvimport:import_csv"),"csvimport");
	echo debut_gauche('',true);
	
	$raccourcis = icone_horizontale(_T('csvimport:administrer_tables'), generer_url_ecrire("csvimport_admin"), $icone, "", false);
	echo bloc_des_raccourcis($raccourcis);

	echo debut_droite('',true);
	
	echo csvimport_afficher_tables(_T('csvimport:tables_declarees'));

	echo fin_gauche(), fin_page();
}

?>