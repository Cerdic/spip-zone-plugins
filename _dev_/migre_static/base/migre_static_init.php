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
include_spip("inc/migre"); // [fr] Charge les fonctions de migre_static [en] Loads migre_static functions

function migre_static_install($action)
{
switch ($action) {
 case 'test' : /* test pour savoir si les actions sont nécessaires */
	return isset($GLOBALS['meta']['migre_static']);
	break;
 case 'install' :
	migre_static_init_metas();
	break;
 case 'uninstall' :
	migre_static_delete_metas();
	break;
 }
} /* migre_static_install */

/* rend disponible l'icone de désinstallation */
/* ca doit certainement permettre d'effacer les fichiers et autres */
function migre_static_uninstall(){
}

?>
