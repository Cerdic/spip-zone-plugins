<?php
#----------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                        #
#  File    : action/spipbb_adminconfigurer                 #
#  Authors : chryjs, 2008                                  #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs #
#  Contact : chryjs¡@!free¡.!fr                            #
#----------------------------------------------------------#

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

// inspire de ecrire/action/configurer.php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/spipbb_common');
spipbb_log('included',2,__FILE__);

function action_spipbb_admin_configurer() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$r = rawurldecode(_request('redirect'));
	$r = parametre_url($r, 'configuration', $arg,"&");
	appliquer_modifs_config($arg);
	redirige_par_entete($r);
} // action_spipbb_admin_configurer

function appliquer_modifs_config($params='') {
	//echo $params;
	// $params peut == spipbb_param_tech
	/*
	if ( $liste_user=_request('ban_user') ) {
		if ( $liste_user AND is_array($liste_user) ) {
			$liste_id=join(",",$liste_user);
			// construction de  INSERT INTO spip_ban_liste ( ban_login ) (SELECT login from spip_auteurs)
			// c: 10/2/8 ca fonctionne partout ca ? IGNORE ?
			$req= sql_query("INSERT IGNORE INTO spip_ban_liste ( ban_login ) "
				. "SELECT login from spip_auteurs "
				. "WHERE id_auteur IN ($liste_id) ");
		}
	}
	*/

} // appliquer_modifs_config

?>