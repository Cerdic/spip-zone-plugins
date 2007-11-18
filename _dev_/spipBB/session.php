<?php
#----------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                        #
#  File    : session.php - compat 192 / 193-               #
#  Authors :                                               #
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

// Chryjs : introduit pour maintenir la compatibilite pour spip 192 et SVN193 avant balise SESSION

if (version_compare(substr($GLOBALS['spip_version'],0,5),'1.945','<')) {
	@require_once(_DIR_PLUGIN_SESSION."/session.php"); // verifier que _DIR_PLUGIN_SESSION est bien defini ?
} else {
	@define('_DIR_RESTREINT_ABS', 'ecrire/');
	include _DIR_RESTREINT_ABS.'public.php';
} // sinon rien :-)

?>
