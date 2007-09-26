<?php
#-----------------------------------------------------#
#  Plugin  : spipBB - Licence : GPL                   #
#  File    : spipbb_pipelines - pipelines             #
#  Authors : Chryjs, 2007                             #
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

$p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_SPIPBB',(_DIR_PLUGINS.end($p)));

function spipbb_ajouter_boutons($boutons_admin) {
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo") {
		$boutons_admin['configuration']->sousmenu['cfg&cfg=spipbb']= new Bouton('../'._DIR_PLUGIN_SPIPBB.'/img_pack/spipbb-24.gif', _T('spipbb:titre_spipbb') );
	}
	return $boutons_admin;
}

?>
