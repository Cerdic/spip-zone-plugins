<?php
#----------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                        #
#  File    : base/spipbb_upgrade                           #
#  Authors : Chryjs, 2007 et als                           #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs #
#  Contact : chryjs!@!free!.!fr                            #
#  plugin init/test/del                                    #
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

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/spipbb_common');
spipbb_log('included',2,__FILE__);

include_spip('inc/spipbb_inc_metas');
include_spip('inc/spipbb_inc_config');
include_spip('inc/presentation');

function spipbb_install($action)
{
	static $test=0;
	switch ($action)
	{
		case 'test' :
			// Affichage ici du cadre sous la sous la partie plugins de Spip (affiche_gauche n'y est pas activte)
			if(!defined('SPIPBB_SPIP19300'))
			{
				if ($test++==1) { // test est appelé 2 fois !
					spipbb_log('(install) Version incompatible',1,"spipbb_install");
					echo "<BR />";
					echo debut_cadre_enfonce('', true),
						icone_horizontale(_T('spipbb:plugin_mauvaise_version'), '', find_in_path('img_pack/spipbb-24.png'), '', false),
						fin_cadre_enfonce(true);
				}
				return false;
			}
			else {
				if (_request('exec') == 'admin_plugin') {
					echo debut_cadre_enfonce('', true),
						icone_horizontale(_T('spipbb:titre_spipbb'), generer_url_ecrire('spipbb_configuration'), find_in_path('img_pack/spipbb-24.png'), '', false),
						fin_cadre_enfonce(true);
				}
				// a ce stade les metas ne sont pas encore initialisees : return isset($GLOBALS['meta']['spipbb']) ;
				return true;
			}
			break;
		case 'install' :
//			spipbb_upgrade_all();
			spipbb_log('(install)',1,"spipbb_install");
			break;
		case 'uninstall' :
		default :
			spipbb_delete_metas();
			spipbb_delete_tables();
			spipbb_log('(uninstall)',1,'spipbb_install');
			break;
	}
} /* spipbb_install */

/* rend disponible l'icone de désinstallation */
/* ca doit certainement permettre d'effacer les fichiers et autres */
function spipbb_uninstall(){
}

?>
