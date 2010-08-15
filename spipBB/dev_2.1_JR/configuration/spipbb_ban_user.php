<?php
#---------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                             #
#  File    : configuration/spipbb_ban_user                      #
#  Authors : chryjs 2007 et als                                 #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs      #
#  Contact : chryjs!@!free!.!fr                                 #
# [fr] Gestion du banissement                                   #
# [en] Manage ban lists                                         #
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
function configuration_spipbb_ban_user_dist() {

	// ban user bloc

	$res = debut_cadre_relief("", true, "", "<label for='ban_user'>"._T('spipbb:admin_ban_user_info')."</label>")
		. "<select name='ban_user[]' id='ban_user' multiple='multiple' size='5' class='forml'>";

	// Il ne faudra pas mettre dans cette liste les user deja bannis !

	$query=sql_select("ban_login","spip_ban_liste","ban_login IS NOT NULL");
	$tab_login=array();
	while ($row=sql_fetch($query)) {$tab_login[]="'".$row['ban_login']."'";}
	$liste_login=join(",",$tab_login);
	if (count($tab_login)>0) $not_in="AND login NOT IN ($liste_login)";
	else $not_in="";

	$query=sql_select(array("id_auteur","login"),"spip_auteurs","(statut='6forum' OR statut='nouveau' OR statut='5poubelle') ".$not_in );

	if ( sql_count($query) ) {
		while ($row = sql_fetch($query)) {
			$res .= "<option value='".$row['id_auteur']."'>".$row['login']."</option>";
		}
	}
	else {
		$res .= "<option value='-1'>"._T('spipbb:admin_ban_user_none')."</option>";
	}

	$res .= "</select>"
		. fin_cadre_relief(true);

	// unban user bloc

	$res .= debut_cadre_relief("", true, "", "<label for='unban_user'>"._T('spipbb:admin_unban_user_info')."</label>")
		. "<select name='unban_user[]' id='unban_user' multiple='multiple' size='5' class='forml'>";

	$query=sql_select(array("id_ban","ban_login"),"spip_ban_liste","ban_login IS NOT NULL");
	if ( sql_count($query) ) {
		while ($row = sql_fetch($query)) {
			$res .= "<option value='".$row['id_ban']."'>".$row['ban_login']."</option>";
		}
	}
	else {
		$res .= "<option value='-1'>"._T('spipbb:admin_ban_user_none')."</option>";
	}

	$res .= "</select>"
		. fin_cadre_relief(true);

	if (version_compare($GLOBALS['spip_version_code'],_SPIPBB_REV_AJAXCONFIG,'>=')) {
		$res = ajax_action_post('spipbb_configurer', 'spipbb_ban_user', 'spipbb_admin_gere_ban','',$res) ;
		return ajax_action_greffe('spipbb_configurer-spipbb_ban_user','', $res); // creer action : "ban" a  la facon de action/configurer
	}
	else { // 1.9.2
		$res = ajax_action_post('spipbb_configurer', 'spipbb_ban_user', 'spipbb_admin_gere_ban','',$res,_T('bouton_valider'),'  class="fondo" ') ;
		return ajax_action_greffe('spipbb_configurer-spipbb_ban_user', $res); // creer action : "ban" a  la facon de action/configurer
	}
} // configuration_ban_user_dist

?>
