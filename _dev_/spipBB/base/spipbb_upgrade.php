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

function spipbb_install($action)
{
	switch ($action)
	{
		case 'test' :
			return isset($GLOBALS['meta']['spipbb']) ;
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

/* rend disponible l'icone de d�sinstallation */
/* ca doit certainement permettre d'effacer les fichiers et autres */
function spipbb_uninstall(){
}

?>
