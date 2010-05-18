<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
function exec_lieux_tous_dist()
{
	global $spip_lang_right;
 	include_spip("inc/presentation");
	include_spip('public/assembler');

	$id_visite = intval(_request('id_visite'));
	
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T("panoramas:lieu"),_T("panoramas:lieu"),_T("panoramas:lieu"));
	

	echo debut_gauche('', true);
	echo debut_boite_info(true);
	if ($id_visite>0)
		echo "<div class=\"verdana1 spip_xx-small\" style=\"font-weight: bold; text-align: center; text-transform: uppercase;\">"._T("panoramas:visite_numero")."<div align='center' style='font-size:3em;font-weight:bold;'>$id_visite</div></div>\n";
		echo icone_horizontale(_T('icone_retour'), "?exec=visitesvirtuelles_toutes", "../"._DIR_PLUGIN_PANORAMAS."img_pack/logo_panoramas.png", "rien.gif",'', false);
	echo fin_boite_info(true);
	
	echo "<br /><br />\n";
	
	echo debut_boite_info(true);
	echo _T("panoramas:boite_info_lieu");
	echo "<p>";
	echo fin_boite_info(true);
	
	echo creer_colonne_droite();
	
	echo debut_droite('', true);
	
	echo recuperer_fond("fonds/lieux_tous", array('id_visite' => $id_visite));
	
	echo "<br />\n";

	if (!include_spip('inc/autoriser'))
		include_spip('inc/autoriser_compat');
	if (autoriser('creer','lieu')) {
		echo "<div align='right'>";
		$link=generer_url_ecrire('lieux_edit', 'new=oui&id_visite='.$id_visite);
		$link=parametre_url($link,'retour',str_replace('&amp;', '&', self()));
		echo icone(_T("panoramas:icone_creer_lieu"), $link, "../"._DIR_PLUGIN_PANORAMAS. "img_pack/house_gabrielle_nowicki_.png", "creer.gif");
		echo "</div>";
	}
	
	if ($GLOBALS['spip_version_code']>=1.9203)
		echo fin_gauche();
	
	echo fin_page();

}

?>

