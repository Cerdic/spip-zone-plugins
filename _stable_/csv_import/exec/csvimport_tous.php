<?php
/*
 * csvimport
 * plug-in d'import csv dans les tables spip
 *
 * Auteur :
 * Cedric MORIN
 * notre-ville.net
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

include_spip("inc/csvimport");
include_spip("inc/presentation");

function exec_csvimport_tous(){
	//
	// Afficher une liste de tables importables
	//
	
	debut_page(_L("Import CSV"), "documents", "csvimport");
	debut_gauche();
	
	debut_raccourcis();
	echo "<p>";
	icone_horizontale (_L('Administrer les tables'), generer_url_ecrire("csvimport_admin"), "../"._DIR_PLUGIN_CSVIMPORT."img_pack/csvimport-24.png");
	echo "</p>";
	fin_raccourcis();
	debut_droite();
	
	csvimport_afficher_tables(_L("Tables d&eacute;clar&eacute;es"));
	
	echo "<br />\n";
	
	fin_page();
}

?>