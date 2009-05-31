<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
function exec_lieux_tous_dist()
{
	global $spip_lang_right;
 	include_spip("inc/presentation");
	include_spip('public/assembler');

	$id_visite = intval(_request('id_visite'));
	debut_page(_T("panoramas:lieu"), "Lieu", "Lieu");


	debut_gauche();
	debut_boite_info();
	if ($id_visite>0)
		echo "<div class=\"verdana1 spip_xx-small\" style=\"font-weight: bold; text-align: center; text-transform: uppercase;\">"._T("panoramas:visite_numero")."<div align='center' style='font-size:3em;font-weight:bold;'>$id_visite</div></div>\n";
		icone_horizontale(_T('icone_retour'), "?exec=visitesvirtuelles_toutes", "../"._DIR_PLUGIN_PANORAMAS."img_pack/logo_panoramas.png", "rien.gif",'right');
	fin_boite_info();
	
	echo "<br /><br />\n";
	
	debut_boite_info();
	echo _T("panoramas:boite_info_lieu");
	echo "<p>";
	fin_boite_info();
	
	creer_colonne_droite();
	
	debut_droite();
	
	echo recuperer_fond("fonds/lieux_tous","$id_visite");
	
	echo "<br />\n";

	if (!include_spip('inc/autoriser'))
		include_spip('inc/autoriser_compat');
	if (autoriser('creer','lieu')) {
		echo "<div align='right'>";
		$link=generer_url_ecrire('lieux_edit', 'new=oui&id_visite='.$id_visite);
		$link=parametre_url($link,'retour',str_replace('&amp;', '&', self()));
		icone(_T("panoramas:icone_creer_lieu"), $link, "../"._DIR_PLUGIN_PANORAMAS. "img_pack/house_gabrielle_nowicki_.png", "creer.gif");
		echo "</div>";
	}
	
	if ($GLOBALS['spip_version_code']>=1.9203)
		echo fin_gauche();
	
	echo fin_page();


}

?>

