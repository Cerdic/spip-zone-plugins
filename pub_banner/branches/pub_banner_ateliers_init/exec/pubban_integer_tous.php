<?php
/**
 * @name 		Bannieres toutes
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.0 (06/2009)
 * @package		Pub Banner
 * @subpackage	Pages exec
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_pubban_integer_tous_dist() {
	global $connect_statut, $connect_id_auteur, $spip_lang_right;
	if ($connect_statut != "0minirezo" ) { include_spip('inc/minipres'); echo minipres(); exit; }
	include_spip('inc/presentation');
	$commencer_page = charger_fonction('commencer_page', 'inc');
 	$titre_page = _T('pubban:list_empl');
	$rubrique = 'pubban';
	$sous_rubrique = "home_pub";

	$res = icone_horizontale(_T('pubban:home'), generer_url_ecrire("pubban_admin"), find_in_path("img/stock_home.png"), "rien.gif", false)
		. icone_horizontale(_T('pubban:nouveau_empl'), generer_url_ecrire('pubban_integer_edit','id_empl=new'), find_in_path("img/stock_insert-image.png"), "creer.gif", false)
		. icone_horizontale(_T('pubban:page_stats'), generer_url_ecrire('pubban_stats'), find_in_path("img/stock-tool-button-color-balance.png"), "rien.gif", false);
	$trash = pubban_poubelle_pleine();
	if($trash) $res .= icone_horizontale(_T('pubban:open_trash'), generer_url_ecrire('pubban_admin','mode=trash'), find_in_path("img/stock_delete.png"), "rien.gif", false);

	$contexte = array(
		'bdd' 			=> _BDD_PUBBAN,
		'redirect'		=> generer_url_ecrire("pubban_pub"),
		'inverse' 		=> _request('inverse'),
		'btn_apercu' 	=> $GLOBALS['pubban_btns']['apercu'],
		'btn_editer' 	=> $GLOBALS['pubban_btns']['editer'],
		'btn_poubelle' 	=> $GLOBALS['pubban_btns']['poubelle'],
		'btn_inactive' 	=> $GLOBALS['pubban_btns']['inactif'],
		'btn_active'	=> $GLOBALS['pubban_btns']['actif'],
		'btn_lister' 	=> $GLOBALS['pubban_btns']['lister'],
	);
	$milieu = recuperer_fond("prive/emplacement_liste_voir",$contexte);

	echo($commencer_page(_T('pubban:pubban')." - ".$titre_page, $rubrique, $sous_rubrique)), debut_gauche('', true),
		debut_cadre_relief(find_in_path("img/ico-pubban.png"), true, "", "<br />"._T('pubban:intro_integer')),
		_T("pubban:intro_integer_texte"), _T("pubban:voir_page"), 
		icone_horizontale(_T('pubban:page_infos'), generer_url_ecrire('pubban_info'), find_in_path("img/status-dock-24.png"), "rien.gif", false),
  		fin_cadre_relief(true), bloc_des_raccourcis($res), creer_colonne_droite('', true), debut_droite('', true),
		pipeline('affiche_milieu', array( 'args' => array( 'exec' => 'pubban_integer_tous' ), 'data' => $milieu ) ),
		fin_gauche(), fin_page();
}
?>