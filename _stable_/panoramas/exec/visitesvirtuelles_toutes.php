<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
function exec_visitesvirtuelles_toutes_dist()
{
	global $spip_lang_right;
 	include_spip("inc/presentation");
	include_spip('public/assembler');

	debut_page(_T("panoramas:visite_virtuelle"), "Visite virtuelle", "Visite virtuelle");

	

	debut_gauche();
	debut_boite_info();
	echo _T("panoramas:boite_info");
	echo "<p>";
	fin_boite_info();
	
	creer_colonne_droite();
	
	debut_droite();
	
	echo recuperer_fond("fonds/visitesvirtuelles_toutes");
	
	echo "<br />\n";

	if (!include_spip('inc/autoriser'))
		include_spip('inc/autoriser_compat');
	if (autoriser('creer','visitevirtuelle')) {
		echo "<div align='right'>";
		$link=generer_url_ecrire('visitesvirtuelles_edit', 'new=oui');
		$link=parametre_url($link,'retour',str_replace('&amp;', '&', self()));
		icone(_T("panoramas:icone_creer_visitevirtuelle"), $link, "../"._DIR_PLUGIN_PANORAMAS. "img_pack/logo_panoramas.png", "creer.gif");
		echo "</div>";
	}
	
	if ($GLOBALS['spip_version_code']>=1.9203)
		echo fin_gauche();
	
	echo fin_page();


}

?>

