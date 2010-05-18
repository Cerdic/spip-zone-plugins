<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
function exec_interactions_toutes_dist()
{
	global $spip_lang_right;
 	include_spip("inc/presentation");
	include_spip('public/assembler');

	$id_visite = intval(_request('id_visite'));
	$id_lieu = intval(_request('id_lieu'));
	
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T("panoramas:interaction"),"Interaction","Interaction");
	

	echo debut_gauche('', true);
	echo debut_boite_info(true);
	if ($id_lieu>0)
		echo "<div class=\"verdana1 spip_xx-small\" style=\"font-weight: bold; text-align: center; text-transform: uppercase;\">"._T("panoramas:lieu_numero")."<div align='center' style='font-size:3em;font-weight:bold;'>$id_lieu</div></div>\n";
		echo icone_horizontale(_T('icone_retour'), "?exec=lieux_tous&id_visite=".$id_visite, "../"._DIR_PLUGIN_PANORAMAS."img_pack/logo_panoramas.png", "rien.gif",'', false);
	echo fin_boite_info(true);
	
	echo "<br /><br />\n";
	
	echo debut_boite_info(true);
	echo _T("panoramas:boite_info_interaction");
	echo "<p>";
	echo fin_boite_info(true);
	
	echo creer_colonne_droite(true);
	
	echo debut_droite('', true);
	
	echo recuperer_fond("fonds/interactions_toutes", array('id_lieu' => $id_lieu));
	
	echo "<br />\n";

	if (!include_spip('inc/autoriser'))
		include_spip('inc/autoriser_compat');
	if (autoriser('creer','interaction')) {
		echo "<div align='right'>";
		$link=generer_url_ecrire('interactions_edit', 'new=oui&id_lieu='.$id_lieu.'&id_visite='.$id_visite);
		$link=parametre_url($link,'retour',str_replace('&amp;', '&', self()));
		echo icone(_T("panoramas:icone_creer_interaction"), $link, "../"._DIR_PLUGIN_PANORAMAS. "img_pack/engrenagem_01.png", "creer.gif");
		echo "</div>";
	}
	
	if ($GLOBALS['spip_version_code']>=1.9203)
		echo fin_gauche();
	
	echo fin_page();


}

?>

