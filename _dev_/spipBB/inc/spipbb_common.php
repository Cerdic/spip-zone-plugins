<?php
#----------------------------------------------------------#
#  Plugin  : spipbb_common - Licence : GPL                 #
#  File    : inc/spipbb_common                             #
#  Authors : Chryjs, 2007 et als                           #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs #
#  Contact : chryjs!@!free!.!fr                            #
# [fr] Fonction et définitions essentielles du plugin      #
# [en] Functions and data required for this plugin         #
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
// [fr] Protection contre les inclusions multiples (ne devrait jamais arriver)
// [en] Protects against multiples includes (should never occur)
if (defined("_INC_SPIPBB_COMMON")) return; else define("_INC_SPIPBB_COMMON", true);

spipbb_log('included',2,__FILE__);

// Default log level
define('_SPIPBB_LOG_LEVEL',3);

// [fr] Plugin ecrit pour spip rev 1.9.3 -> fournir les fonctions requises pour spip 1.9.2
// [en] Plugin written for spip rev 1.9.3 -> provide required functions for spip 1.9.2
if (version_compare(substr($GLOBALS['spip_version_code'],0,5),'1.927','<')) {
	include_spip('inc/spipbb_192'); // SPIP 1.9.2
}

//----------------------------------------------------------------------------
// [fr] Genere une trace pour spipbb sauf si on ne veut pas de log
// [fr] pour cela mettre : define('_SPIPBB_LOG_LEVEL',0); dans spipbb_options.php
// [en] Log for spipbb except if we don't want logs
// [en] in this case just put: define('_SPIPBB_LOG_LEVEL',0); in spipbb_options.php
// [en] log_level : 0 none
// [en] log_level : 1 low
// [en] log_level : 2 medium
// [en] log_level : 3 high (very verbose)
//----------------------------------------------------------------------------
function spipbb_log($message='',$log_level=1,$obsolete_prefix="") {

	if (defined('_SPIPBB_LOG_LEVEL')) $spipbb_log_level=_SPIPBB_LOG_LEVEL;
	else $spipbb_log_level=1;

	if ($log_level<=$spipbb_log_level) {
		if (function_exists('debug_backtrace')) { // dispo a partir de PHP 4.3
			// on prefixe avec l'appelant
			$bt=debug_backtrace();
			$message = $bt[0]['file'].":".$bt[1]['function']."():".$message;
		}
		else $message=$obsolete_prefix.":".$message;
		spip_log($message,'spipbb');
	} // should we log ?

} // spipbb_log

?>
