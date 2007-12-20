<?php
#----------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                        #
#  File    : spipbb_upgrade - plugin init/test/del         #
#  Authors : Chryjs, 2007 et als                           #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs #
#  Contact : chryjs!@!free!.!fr                            #
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

include_spip('inc/spipbb_inc_metas');
include_spip('inc/spipbb_inc_config');

# conversion spip 192
if (version_compare(substr($GLOBALS['spip_version_code'],0,5),'1.927','<')) {
	include_spip('inc/spipbb_192'); // SPIP 1.9.2
}

function spipbb_install($action)
{
	switch ($action)
	{
		case 'test' :
			return isset($GLOBALS['meta']['spipbb']) ;
			break;
		case 'install' :
//			spipbb_upgrade_all();
			spip_log('base/spipbb_upgrade.php spipbb_install(install)','spipbb');
			break;
		case 'uninstall' :
		default :
			spipbb_delete_metas();
			spipbb_delete_tables();
			spip_log('base/spipbb_upgrade.php spipbb_install(uninstall)','spipbb');
			break;
	}
} /* spipbb_install */

/* rend disponible l'icone de d�sinstallation */
/* ca doit certainement permettre d'effacer les fichiers et autres */
function spipbb_uninstall(){
}

?>
