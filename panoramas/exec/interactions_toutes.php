<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
function exec_interactions_toutes_dist()
{
	global $spip_lang_right;
 	include_spip("inc/presentation");
	include_spip('public/assembler');

	$id_visite = intval(_request('id_visite'));
	$id_lieu = intval(_request('id_lieu'));
	debut_page(_T("panoramas:interaction"), "Interaction", "Interaction");


	debut_gauche();
	debut_boite_info();
	if ($id_lieu>0)
		echo "<div class=\"verdana1 spip_xx-small\" style=\"font-weight: bold; text-align: center; text-transform: uppercase;\">"._T("panoramas:lieu_numero")."<div align='center' style='font-size:3em;font-weight:bold;'>$id_lieu</div></div>\n";
		icone_horizontale(_T('icone_retour'), "?exec=lieux_tous&id_visite=".$id_visite, "../"._DIR_PLUGIN_PANORAMAS."img_pack/logo_panoramas.png", "rien.gif",'right');
	fin_boite_info();
	
	echo "<br /><br />\n";
	
	debut_boite_info();
	echo _T("panoramas:boite_info_interaction");
	echo "<p>";
	fin_boite_info();
	
	creer_colonne_droite();
	
	debut_droite();
	
	echo recuperer_fond("fonds/interactions_toutes","$id_lieu");
	
	echo "<br />\n";

	if (!include_spip('inc/autoriser'))
		include_spip('inc/autoriser_compat');
	if (autoriser('creer','interaction')) {
		echo "<div align='right'>";
		$link=generer_url_ecrire('interactions_edit', 'new=oui&id_lieu='.$id_lieu.'&id_visite='.$id_visite);
		$link=parametre_url($link,'retour',str_replace('&amp;', '&', self()));
		icone(_T("panoramas:icone_creer_interaction"), $link, "../"._DIR_PLUGIN_PANORAMAS. "img_pack/engrenagem_01.png", "creer.gif");
		echo "</div>";
	}
	
	if ($GLOBALS['spip_version_code']>=1.9203)
		echo fin_gauche();
	
	echo fin_page();


}

?>

