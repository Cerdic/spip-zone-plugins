<?php
#-----------------------------------------------------#
#  Plugin  : migre_static - Licence : GPL             #
#  File    : migre_static_init - plugin init/test/del #
#  Authors : Chryjs, 2007 - Beurt, 2006               #
#  Contact : chryjs¡@!free¡.!fr                       #
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

include_spip('inc/migre'); // [fr] Charge les fonctions de migre_static [en] Loads migre_static functions

function migrestatic_install($action)
{
switch ($action) {
 case 'test' : /* test pour savoir si les actions sont nécessaires */
	return ( isset($GLOBALS['meta']['migrestatic']) AND isset($GLOBALS['migrestatic']['version']) AND ($GLOBALS['migrestatic']['version']>= $GLOBALS['migrestatic_version'] ) );
	break;
 case 'install' :
	migre_static_upgrade();
	//migre_static_init_metas();
	break;
 case 'uninstall' :
	migre_static_delete_metas();
	break;
 }
} /* migre_static_install */

/* rend disponible l'icone de désinstallation */
/* ca doit certainement permettre d'effacer les fichiers et autres */
function migrestatic_uninstall(){
}

// [fr] Met a jour la version et initialise les metas
// [en] Upgrade release and init metas
function migre_static_upgrade()
{
	$version_code = $GLOBALS['migrestatic_version'] ;
	if ( isset($GLOBALS['meta']['migrestatic'] ) )
	{
		if ( isset($GLOBALS['migrestatic']['version'] ) )
		{
			$installed_version = $GLOBALS['migrestatic']['version'];
		}
		else {
			$installed_version = 0.80 ; // previous releases didn't store the release level
		}
	}
	else {
		$installed_version = 0.0 ; // aka not installed
	}

	if ( $installed_version < 0.83 ) {
		migre_static_init_metas(); // we reset everything
	}

	if ( $installed_version < $version_code ) {
		migre_static_update_metas();
	}

	spip_log('migrestatic : migre_static_upgrade OK');
} /* migre_static_upgrade */

?>
