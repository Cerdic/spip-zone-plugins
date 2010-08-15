<?php
#------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                          #
#  File    : inc/statvisites - stats de visites des forums   #
#  Authors : Chryjs, 2007 et als                             #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs   #
#  Contact : chryjs!@!free!.!fr                              #
#------------------------------------------------------------#

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

spipbb_log("included",3,__FILE__);
// compatibilite SPIP 1.9.2

function cron_statvisites($time){
	include_spip('genie/statvisites');
	return genie_statvisites($time);
}

?>
