<?php
/**
 * @name 		Home
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.0 (06/2009)
 * @package		Pub Banner
 * @subpackage	Pages exec
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_pubban_admin_dist() {
	global $connect_statut, $connect_id_auteur, $spip_lang_right;
	include_spip('inc/presentation');
	$commencer_page = charger_fonction('commencer_page', 'inc');
 	$titre_page = _T('pubban:gestion_pubban');
	$rubrique = 'pubban';
	$bloc_racc = $bloc_stats = '';
	$texte_page = _T("pubban:texte_admin");

	$boutons = "<br class='nettoyeur' />"
		. icone_inline(_T('pubban:page_stats'), generer_url_ecrire('pubban_stats'), find_in_path("img/stock-tool-button-color-balance.png"), "rien.gif", $GLOBALS['spip_lang_right'])
		. icone_inline(_T('pubban:page_infos'), generer_url_ecrire('pubban_info'), find_in_path("img/status-dock-24.png"), "rien.gif", $GLOBALS['spip_lang_right']);

	$search_str = _request('search_pubban');
	$contexte = array(
		'search_pubban' => $search_str,
		'redirect'=> generer_url_ecrire("pubban_admin"),
		'inverse' => _request('inverse'),
		'btn_apercu' => $GLOBALS['pubban_btns']['apercu'],
		'btn_editer' => $GLOBALS['pubban_btns']['editer'],
		'btn_poubelle' => $GLOBALS['pubban_btns']['poubelle'],
		'btn_inactive' => $GLOBALS['pubban_btns']['inactif'],
		'btn_active' => $GLOBALS['pubban_btns']['actif'],
		'btn_obsolete' => $GLOBALS['pubban_btns']['obsolete'],
		'btn_sortie_poubelle' => $GLOBALS['pubban_btns']['sortie_poubelle'],
		'puce_empl' => $GLOBALS['_PUBBAN_PUCES_STATUTS']['banniere']['icon'],
	);

	$milieu = recuperer_fond("prive/search", $contexte);
	$contenu = pipeline('affiche_milieu',array('args'=>array('exec'=>'pubban_admin'),'data'=>$milieu));

	if ($connect_statut == "0minirezo" ) {
		$trash = pubban_poubelle_pleine();
		$boutons .= icone_inline(_T('pubban:liste_pub'), generer_url_ecrire('pubban_publicite_tous'), find_in_path("img/stock_insert-object.png"), "rien.gif", $GLOBALS['spip_lang_right'])
			. icone_inline(_T('pubban:list_empl'), generer_url_ecrire('pubban_banniere_tous'), find_in_path("img/stock_insert-image.png"), "rien.gif", $GLOBALS['spip_lang_right'])
			. ( defined('_DIR_PUBLIC_PUBBAN') && _PUBBAN_ADDS ? icone_inline(_T('pubban:page_config'), generer_url_ecrire('pubban_config'), find_in_path("img/stock_import.png"), "rien.gif", $GLOBALS['spip_lang_right']) : '')
			. ( defined('_DIR_PUBLIC_PUBBAN') && _PUBBAN_ADDS ? icone_inline(_T('pubban:page_tarifs'), generer_url_ecrire('pubban_tarifs'), find_in_path("img/money.png"), "rien.gif", $GLOBALS['spip_lang_right']) : '')
			. ( $trash ? icone_inline(_T('pubban:open_trash'), generer_url_ecrire('pubban_admin','mode=trash'), find_in_path("img/stock_delete.png"), "rien.gif", $GLOBALS['spip_lang_right']) : '');
		$res = icone_horizontale(_T('pubban:nouveau_pub'), generer_url_ecrire('pubban_editer_publicite','id_pub=new'), find_in_path("img/stock_insert-object.png"), "creer.gif", false)
			. icone_horizontale(_T('pubban:nouveau_empl'), generer_url_ecrire('pubban_editer_banniere','id_empl=new'), find_in_path("img/stock_insert-image.png"), "creer.gif", false);
		$bloc_racc = bloc_des_raccourcis($res);

		//poubelle
		if( _request('mode') == 'trash' ){
			$stats_ferme = true;
			$milieu = recuperer_fond("prive/pub_liste_trash",$contexte);
			$contenu_add = pipeline('affiche_milieu',array('args'=>array('exec'=>'pubban_admin'),'data'=>$milieu))
				. icone_inline(_T('pubban:vider_trash'), "javascript:delete_entry(\"".generer_action_auteur('vider_poubelle_pubban', false, generer_url_ecrire('pubban_admin','mode=trash'))."\", \""._T('pubban:confirm_vider_poubelle')."\");", find_in_path("img/trash-empty-accept.png"), "rien.gif", $GLOBALS['spip_lang_right'])
				. icone_inline(_T('pubban:home'), generer_url_ecrire("pubban_admin"), find_in_path("img/stock_home.png"), "rien.gif", $GLOBALS['spip_lang_right']);
		}
		//recherche
		elseif( strlen($search_str) ){
			$stats_ferme = true;
			$search = pubban_search(_request('search_pubban'));
			$contexte['pub'] = $search['pub'];
			$contexte['emp'] = $search['emp'];
			$contexte['boucle_search'] = count($contexte['pub'])+count($contexte['emp']);
			$milieu = recuperer_fond("prive/search_results", $contexte);
			$contenu_add = pipeline('affiche_milieu',array('args'=>array('exec'=>'pubban_admin'),'data'=>$milieu))
				. icone_inline(_T('pubban:home'), generer_url_ecrire("pubban_admin"), find_in_path("img/stock_home.png"), "rien.gif", $GLOBALS['spip_lang_right']);
		}
	}

	include_spip('base/abstract_sql');
	$verif_empl = sql_countsel('spip_bannieres', '', '', '', '', '');
	$verif = sql_countsel('spip_publicites', '', '', '', '', '');
	$bloc_pliable_plie = ( $verif != 0 AND $verif_empl != 0 AND !$stats_ferme ) ? 'deplie' : 'replie';
	$bloc_pliable_titre = "<div class='titrem $bloc_pliable_plie' onmouseover=\"jQuery(this).depliant('#stats_div');\">"
		."<a href='#' onclick=\"return jQuery(this).depliant_clicancre('#stats_div');\" class='titremancre'></a>"
		."<strong>"._T('pubban:info_stats')."</strong></div>";
	$bloc_stats .= debut_cadre_trait_couleur("rien.gif", true, '', $bloc_pliable_titre)
		. afficher_statistiques_pubban(true, 'stats_div', $bloc_pliable_plie)
		. fin_cadre_trait_couleur(true);

	echo($commencer_page(_T('pubban:pubban')." - ".$titre_page, $rubrique, '')), debut_gauche('', true), 
		debut_cadre_relief(find_in_path("img/ico-pubban.png"), true, "", "<br />".$titre_page), 
		$texte_page, pubban_lien_doc(), fin_cadre_relief(true), $bloc_racc, pubban_boite_info(),
		creer_colonne_droite('', true), debut_droite('', true), gros_titre($titre_page, '', false),
		$contenu, $bloc_stats, $contenu_add, ($contenu_add ? '' : $boutons), 
		fin_gauche(), fin_page();
}
?>