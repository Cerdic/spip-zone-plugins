<?php
/**
 * @name 		Statistiques
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.0 (06/2009)
 * @package		Pub Banner
 * @subpackage	Pages exec
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_statistiques_bannieres_dist() {
	global $connect_statut, $connect_id_auteur, $spip_lang_right, $spip_lang_left;
	include_spip('inc/presentation');
	include_spip('base/abstract_sql');
	$commencer_page = charger_fonction('commencer_page', 'inc');
 	$titre_page = _T('pubban:stats_pubban');
	$rubrique = 'pubban';
	$sous_rubrique = "home_pub";

	$res = icone_horizontale(_T('pubban:home'), generer_url_ecrire("pubbanner"), find_in_path("img/stock_home.png"), "rien.gif", false);
	if ($connect_statut == "0minirezo") 
		$res .= icone_horizontale(_T('pubban:liste_pub'), generer_url_ecrire('publicites_tous'), find_in_path("img/stock_insert-object.png"), "rien.gif", false)
			. icone_horizontale(_T('pubban:list_empl'), generer_url_ecrire('bannieres_tous'), find_in_path("img/stock_insert-image.png"), "rien.gif", false);
	$contexte = array();
	$verif = sql_select("*", 'spip_pubban_stats', '', '', '', '', '');
	if (sql_count($verif) == 0) $contexte['no_datas'] = true;
	$milieu = recuperer_fond("prive/pubban_statistiques",$contexte);

	echo($commencer_page(_T('pubban:pubban')." - ".$titre_page, $rubrique, $sous_rubrique)), debut_gauche('', true),
		debut_cadre_relief(find_in_path("img/ico-pubban.png"), true, "", "<br />"._T('pubban:intro_stats')),
  		_T("pubban:intro_texte_stats_banner"), _T("pubban:voir_page"), icone_horizontale(_T('pubban:page_infos'), generer_url_ecrire('pubban_info'), find_in_path("img/status-dock-24.png"), "rien.gif", false),
  		fin_cadre_relief(true), bloc_des_raccourcis($res),
		creer_colonne_droite('', true), debut_droite('', true), gros_titre($titre_page, '', false),
		"\n<div id='pubban_info_nojs' class='verdana2' style='text-align: justify;border:1px solid #404040;padding:1em;'><p>"
			. http_img_pack("warning.gif", (_T('avis_attention')),
				"width='48' height='48' style='float: $spip_lang_right; padding-$spip_lang_left: 10px;'")
			. "<br />"._T('pubban:avertissement_js_necessaire')
			. "</p></div>", "<p>&nbsp;</p>\n",
			"<script type=\"text/javascript\"><!--\n \$(document).ready(function(){\$(\"#pubban_info_nojs\").hide(); });\n //--></script>",
		pipeline('affiche_milieu', array( 'args' => array( 'exec' => 'pubbanner' ), 'data' => $milieu ) ),
		fin_gauche(), fin_page();
}
?>