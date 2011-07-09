<?php
/**
 * @name 		Publicites
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.0 (06/2009)
 * @package		Pub Banner
 * @subpackage	Pages exec
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_publicite_voir_dist() {
	global $connect_statut, $connect_id_auteur, $spip_lang_right;
	if ($connect_statut != "0minirezo" ) { include_spip('inc/minipres'); echo minipres(); exit; }
	include_spip('inc/presentation');
	$commencer_page = charger_fonction('commencer_page', 'inc');
 	$titre_page = _T('pubban:view_pub');
	$rubrique = 'pubban';
	$sous_rubrique = "home_pub";

	$id_publicite = _request('id_publicite');
	$pub = pubban_recuperer_publicite($id_publicite);
	$ext_pub = pubban_extension( $pub['objet'] );
	$icon = ($pub['type'] == 'img') ? ( isset($GLOBALS['pubban_pub_icons'][ $ext_pub ]) ? $GLOBALS['pubban_pub_icons'][ $ext_pub ] : $GLOBALS['pubban_pub_icons']['default'] ) : $GLOBALS['pubban_pub_icons']['flash'];
	$s = ($pub['statut'] == '5poubelle') ? '3' : ( ($pub['statut'] == '1inactif') ? '2' : '1' );
	$lien_popup = "javascript:popup(600,600,\"../?page=view_pub&id_publicite=".$id_publicite."\");";
	if($pub['statut'] == '3obsolete') {
		$boite = "<div class='cadre_padding'><div class='infos'>"
			."<div class='numero'>"._T('pubban:titre_info_pub')."<p>".$id_publicite."</p>"
			."<ul id='instituer_publicite-".$id_publicite."' class='instituer_publicite instituer'><li>"._T('pubban:pub_is')."&nbsp;:</li>"
			."<li><center><img src='".$GLOBALS['pubban_btns']['obsolete']."' title='"._T('pubban:obsolete')."' alt='"._T('pubban:obsolete')."' border='0' />&nbsp;"
			._T('pubban:obsolete')."</center></li></ul>"
			.icone_horizontale(_T('pubban:voir_un_apercu'), $lien_popup, "racine-24.gif", "rien.gif", false)
			."</div><div class='nettoyeur'></div></div></div>";
	}
	else {
		$boite = "<div class='cadre_padding'><div class='infos'>"
			."<div class='numero'>"._T('pubban:titre_info_pub')."<p>".$id_publicite."</p>"
			."<ul id='instituer_publicite-".$id_publicite."' class='instituer_publicite instituer'><li>"._T('pubban:pub_is')."&nbsp;:"
			."<ul>"
			."<li class='publie ".(($s == '1') ? "selected" : "" )."'><a href='".generer_action_auteur("activer_publicite", "activer-$id_publicite", generer_url_ecrire('publicite_voir','id_publicite='.$id_publicite))."&activer=oui' onclick='return confirm(confirm_changer_statut);'><img src='../prive/images/puce-verte.gif' alt=\""._T('pubban:active')."\"  />"._T('pubban:actif')."</a></li>"
			."<li class='prop ".(($s == '2') ? "selected" : "" )."'><a href='".generer_action_auteur("activer_publicite", "desactiver-$id_publicite", generer_url_ecrire('publicite_voir','id_publicite='.$id_publicite))."&activer=non' onclick='return confirm(confirm_changer_statut);'><img src='../prive/images/puce-rouge.gif' alt=\""._T('pubban:inactive')."\"  />"._T('pubban:inactif')."</a></li>"
			."<li class='poubelle ".(($s == '3') ? "selected" : "" )."'><a href=\"javascript:delete_entry('".generer_action_auteur("activer_publicite", "trash-$id_publicite", generer_url_ecrire('publicites_tous'))."', '"._T('pubban:confirm_delete')."');\"><img src='../prive/images/puce-poubelle.gif' alt=\""._T('pubban:poubelle')."\"  />"._T('pubban:poubelle')."</a></li>"
			."</ul></li></ul>"
			.icone_horizontale(_T('pubban:voir_un_apercu'), $lien_popup, "racine-24.gif", "rien.gif", false)
			."</div><div class='nettoyeur'></div></div></div>";
	}

	$res = icone_horizontale(_T('pubban:home'), generer_url_ecrire("pubbanner"), find_in_path('img/stock_home.png'), "rien.gif", false)
		. icone_horizontale(_T('pubban:retour_liste_pub'), generer_url_ecrire("publicites_tous"), find_in_path("img/stock_left.png"), "rien.gif", false)
		. icone_horizontale(_T('pubban:nouveau_pub'), generer_url_ecrire('publicite_edit','id_publicite=new'), find_in_path("img/stock_insert-object.png"), "creer.gif", false)
		. icone_horizontale(_T('pubban:page_stats'), generer_url_ecrire('statistiques_bannieres'), find_in_path("img/stock-tool-button-color-balance.png"), "rien.gif", false);
	$trash = pubban_poubelle_pleine();
	if($trash) $res .= icone_horizontale(_T('pubban:open_trash'), generer_url_ecrire('pubbanner','mode=trash'), find_in_path("img/stock_delete.png"), "rien.gif", false);

	$contexte = array(
		'id_publicite' => _request('id_publicite'),
		'icone_editer' => icone_inline(_T('bouton_modifier'), generer_url_ecrire("publicite_edit","id_publicite=$id_publicite"), find_in_path("img/stock_edit.png"), "rien.gif",$GLOBALS['spip_lang_right']),
		'redirect'=> generer_url_ecrire("publicite_voir"),
		'btn_apercu' => $GLOBALS['pubban_btns']['apercu'],
		'btn_editer' => $GLOBALS['pubban_btns']['editer'],
		'btn_poubelle' => $GLOBALS['pubban_btns']['poubelle'],
		'btn_inactif' => $GLOBALS['pubban_btns']['inactif'],
	);
	$milieu = recuperer_fond("prive/pub_voir", $contexte);

	echo($commencer_page(_T('pubban:pubban')." - ".$titre_page, $rubrique, $sous_rubrique)), debut_gauche('pub', true),
		"<br />" . debut_boite_info(true). $boite . fin_boite_info(true), bloc_des_raccourcis($res);
	if(_request('retour')) echo icone_inline(_T('pubban:retour_search'), _request('retour'), find_in_path("img/stock_search.png"), "rien.gif", $GLOBALS['spip_lang_left']);
	echo creer_colonne_droite('', true), debut_droite('', true), debut_cadre_trait_couleur($icon, true),
		pipeline('affiche_milieu', array( 'args' => array( 'exec' => 'publicite_voir' ), 'data' => $milieu ) ),
		fin_cadre_trait_couleur(true), fin_gauche(), fin_page();
}
?>