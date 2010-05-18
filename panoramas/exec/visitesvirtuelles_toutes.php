<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
function exec_visitesvirtuelles_toutes_dist()
{
	global $spip_lang_right;
 	include_spip("inc/presentation");
	include_spip('public/assembler');

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T("panoramas:visite_virtuelle"),_T("panoramas:visite_virtuelle"),_T("panoramas:visite_virtuelle"));

	echo debut_gauche('', true);
	echo debut_boite_info(true);
	echo _T("panoramas:boite_info");
	echo "<p>";
	echo fin_boite_info(true);
	
	creer_colonne_droite();
	
	echo debut_droite('', true);
	
	echo recuperer_fond("fonds/visitesvirtuelles_toutes");
	
	echo "<br />\n";

	if (!include_spip('inc/autoriser'))
		include_spip('inc/autoriser_compat');
	if (autoriser('creer','visitevirtuelle')) {
		echo "<div align='right'>";
		$link=generer_url_ecrire('visitesvirtuelles_edit', 'new=oui');
		$link=parametre_url($link,'retour',str_replace('&amp;', '&', self()));
		echo icone(_T("panoramas:icone_creer_visitevirtuelle"), $link, "../"._DIR_PLUGIN_PANORAMAS. "img_pack/planet_costea_bogdan_r.png", "creer.gif");
		echo "</div>";
	}

	if ($GLOBALS['spip_version_code']>=1.9203)
		echo fin_gauche();
	
	echo fin_page();



}

?>

