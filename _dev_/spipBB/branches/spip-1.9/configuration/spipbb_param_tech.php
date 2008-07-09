<?php
#---------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                             #
#  File    : configuration/spipbb_param_tech                    #
#  Authors : chryjs 2007 et als                                 #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs      #
#  Contact : chryjs!@!free!.!fr                                 #
# [fr] Configuration des paramètres techniques                  #
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
include_spip('public/assembler'); // pour le retour avec recuperer_fond()

// ------------------------------------------------------------------------------
// ------------------------------------------------------------------------------
function configuration_spipbb_param_tech_dist() {
	/*
	// ban email bloc

	$res = debut_cadre_relief("", true, "", "<label for='ban_email'>"._T('spipbb:admin_ban_email_info')."</label>")
		. "<input type='text' name='ban_email' id='ban_email' size='5' class='forml'>"
		. fin_cadre_relief(true);

		// unban email bloc

	$res .= debut_cadre_relief("", true, "", "<label for='unban_email'>"._T('spipbb:admin_unban_email_info')."</label>")
		. "<select name='unban_email[]' id='unban_email' multiple='multiple' size='5' class='forml'>";

	// c: 10/2/8 compat multibases
	//$query=sql_query("SELECT id_ban,ban_email FROM spip_ban_liste WHERE ban_email IS NOT NULL ");
	$query=sql_select(array("id_ban","ban_email"),"spip_ban_liste","ban_email IS NOT NULL");
	if ( sql_count($query) ) {
		while ($row = sql_fetch($query)) {
			$res .= "<option value='".$row['id_ban']."'>".$row['ban_email']."</option>";
		}
	}
	else {
		$res .= "<option value='-1'>"._T('spipbb:admin_ban_email_none')."</option>";
	}

	$res .= "</select>"
		. fin_cadre_relief(true);
	*/
	if (!isset($GLOBALS['spipbb'])) $GLOBALS['spipbb']=@unserialize($GLOBALS['meta']['spipbb']); // (au retour)

	$contexte = array(
			'fixlimit' => $GLOBALS['spipbb']['fixlimit'],
			'lockmaint' => $GLOBALS['spipbb']['lockmaint'],
			);
	//if (!function_exists('recuperer_fond'))
	$res = recuperer_fond("prive/spipbb_admin_configuration_param_tech",$contexte) ;

	if (version_compare($GLOBALS['spip_version_code'],_SPIPBB_REV_AJAXCONFIG,'>=')) {
		$res = ajax_action_post('spipbb_admin_configurer', 'spipbb_param_tech', 'spipbb_configuration','',$res) ;
		return ajax_action_greffe('spipbb_admin_configurer-spipbb_param_tech','', $res); // creer action : "ban" a  la facon de action/configurer
	}
	else { // 1.9.2
		$res = ajax_action_post('spipbb_admin_configurer', 'spipbb_param_tech', 'spipbb_configuration','',$res,_T('bouton_valider'),'  class="fondo" ') ;
		return ajax_action_greffe('spipbb_admin_configurer-spipbb_param_tech', $res); // creer action : "ban" a  la facon de action/configurer
	}
} // configuration_spipbb_ban_email_dist

?>
