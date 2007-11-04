<?php
#-----------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                   #
#  File    : spipbb_upgrade - plugin init/test/del    #
#  Authors : Chryjs, 2007 et als                      #
#  Contact : chryjs�@!free�.!fr                       #
#-----------------------------------------------------#

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
include_spip('inc/spipbb');

function spipbb_install($action)
{
	switch ($action)
	{
		case 'test' : // test pour savoir si les actions sont n�cessaires + tester les bases
			return ( isset($GLOBALS['meta']['spipbb']) AND
				 isset($GLOBALS['spipbb']['version']) AND
				 ($GLOBALS['spipbb']['version']>= $GLOBALS['spipbb_version'] ) );
			break;
		case 'install' :
			spipbb_upgrade_all();
			break;
		case 'uninstall' :
		default :
			spipbb_delete_metas();
			break;
	}
} /* spipbb_install */

/* rend disponible l'icone de d�sinstallation */
/* ca doit certainement permettre d'effacer les fichiers et autres */
function spipbb_uninstall(){
}

// [fr] Met a jour la version et initialise les metas
// [en] Upgrade release and init metas
function spipbb_upgrade_all()
{
	$version_code = $GLOBALS['spipbb_version'] ;
	if ( isset($GLOBALS['meta']['spipbb'] ) )
	{
		if ( isset($GLOBALS['spipbb']['version'] ) )
		{
			$installed_version = $GLOBALS['spipbb']['version'];
		}
		else {
			$installed_version = 0.10 ; // first release didn't store the release level
		}
	}
	else {
		$installed_version = 0.0 ; // aka not installed
	}
	if ( $installed_version == 0.0 ) {
		spipbb_init_metas();
	}

	if ( $installed_version < 0.14 ) // 0.14 or schema
	{
		include_spip('base/spipbb');
		include_spip('base/create');
		include_spip('base/abstract_sql');
		creer_base();
		spip_log('spipbb : spipbb_upgrade_all OK');
	}

	if ( $installed_version < $version_code ) {
		spipbb_upgrade_metas();
	}

	spip_log('spipbb : spipbb_upgrade_all OK');
} /* spipbb_upgrade_all */

?>
