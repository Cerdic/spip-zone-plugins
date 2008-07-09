<?php
#---------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                             #
#  File    : configuration/spipbb_mots                          #
#  Authors : chryjs 2008 et als                                 #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs      #
#  Contact : chryjs!@!free!.!fr                                 #
# [fr] Gestion de mots cles necessaires pour SpipBB             #
# [en] Manage SpipBB required keywords                          #
#---------------------------------------------------------------#

//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

// * [fr] Acces restreint, plugin pour SPIP * //
// * [en] Restricted access, SPIP plugin * //

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/spipbb_common');
spipbb_log('included',2,__FILE__);
include_spip('inc/presentation');

// ------------------------------------------------------------------------------
// ------------------------------------------------------------------------------
function configuration_spipbb_mots_dist() {
	$articles_mots = $GLOBALS['meta']["articles_mots"];
	$mots_cles_forums = $GLOBALS['meta']["mots_cles_forums"];
	$forums_publics = $GLOBALS['meta']["forums_publics"];

	$res = "<table border='0' cellspacing='1' cellpadding='3' width=\"100%\">"
	. "<tr><td class='verdana2'>"
	. _T('texte_mots_cles')."<br />\n"
	. _T('info_question_mots_cles')
	. "</td></tr>"
	. "<tr>"
	. "<td align='center' class='verdana2'>"
	. bouton_radio("articles_mots", "oui", _T('item_utiliser_mots_cles'), $articles_mots == "oui", "changeVisible(this.checked, 'mots-config', 'block', 'none');")
	. " &nbsp;"
	. bouton_radio("articles_mots", "non", _T('item_non_utiliser_mots_cles'), $articles_mots == "non", "changeVisible(this.checked, 'mots-config', 'none', 'block');");

	//	$res .= afficher_choix('articles_mots', $articles_mots,
	//		array('oui' => _T('item_utiliser_mots_cles'),
	//			'non' => _T('item_non_utiliser_mots_cles')), "<br />");
	$res .= "</td></tr></table>";

	if ($articles_mots != "non") $style = "display: block;";
	else $style = "display: none;";

	$res .= "<div id='mots-config' style='$style'>"
	. "<br />\n" ;
	if ($forums_publics != "non"){
		$res .= "<br />\n"
		. debut_cadre_relief("", true, "", _T('titre_mots_cles_dans_forum'))
		. "<table border='0' cellspacing='1' cellpadding='3' width=\"100%\">"
		. "<tr><td class='verdana2'>"
		. _T('texte_mots_cles_dans_forum')
		. "</td></tr>"
		. "<tr>"
		. "<td align='$spip_lang_left' class='verdana2'>"
		. afficher_choix('mots_cles_forums', $mots_cles_forums,
			array('oui' => _T('item_ajout_mots_cles'),
				'non' => _T('item_non_ajout_mots_cles')))
		. "</td></tr>"
		. "</table>"
		. fin_cadre_relief(true);
	}
	$res .= "</div>";

/*
 * Presentation
	$res = debut_cadre_trait_couleur("mot-cle-24.gif", true, "", _T('info_mots_cles'))
	. ajax_action_post('configurer', 'mots', 'configuration','',$res)
	. fin_cadre_trait_couleur(true);

	return ajax_action_greffe('configurer-mots', '', $res);
*/

	$res = debut_cadre_trait_couleur("mot-cle-24.gif", true, "", _T('info_mots_cles'))
			. $res
			. fin_cadre_trait_couleur(true);


	if (version_compare($GLOBALS['spip_version_code'],_SPIPBB_REV_AJAXCONFIG,'>=')) {
		$res = ajax_action_post('configurer', 'mots', 'configuration','',$res) ;
		return ajax_action_greffe('configurer-mots','', $res); // creer action
	}
	else { // 1.9.2
		$res = ajax_action_post('configurer', 'mots', 'spipbb_configuration','',$res,_T('bouton_valider'),'  class="fondo" ') ;
		return ajax_action_greffe('configurer-mots', $res); // creer action
	}
} // configuration_spipbb_mots_dist

?>
