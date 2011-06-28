<?php
/**
 * @name 		Informations / Conseils
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.0 (06/2009)
 * @package		Pub Banner
 * @subpackage	Pages exec
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_pubban_info_dist() {
	global $connect_statut, $connect_id_auteur, $spip_lang_right;
	include_spip('inc/presentation');
	$commencer_page = charger_fonction('commencer_page', 'inc');
 	$titre_page = _T('pubban:infos_pubban');
	$rubrique = 'pubban';
	$sous_rubrique = "home_pub";

	$res = icone_horizontale(_T('pubban:home'), generer_url_ecrire("pubban_admin"), find_in_path("img/stock_home.png"), "rien.gif", false)
		. icone_horizontale(_T('pubban:page_stats'), generer_url_ecrire('pubban_stats'), find_in_path("img/stock-tool-button-color-balance.png"), "rien.gif", false);
	if ($connect_statut == "0minirezo") 
		$res .= icone_horizontale(_T('pubban:liste_pub'), generer_url_ecrire('pubban_pub_tous'), find_in_path("img/stock_insert-object.png"), "rien.gif", false)
			. icone_horizontale(_T('pubban:list_empl'), generer_url_ecrire('pubban_integer_tous'), find_in_path("img/stock_insert-image.png"), "rien.gif", false);

  	$contenu = propre(_T("pubban:infos_texte"));

	echo($commencer_page(_T('pubban:pubban')." - ".$titre_page, $rubrique, $sous_rubrique)), debut_gauche('', true),
		bloc_des_raccourcis($res), creer_colonne_droite('', true), debut_droite('', true), gros_titre($titre_page, '', false),
		debut_cadre_relief(find_in_path("img/ico-pubban.png"), true, "", "<br /><br />"),
		$contenu, fin_cadre_relief(true), fin_gauche(), fin_page();
}
?>