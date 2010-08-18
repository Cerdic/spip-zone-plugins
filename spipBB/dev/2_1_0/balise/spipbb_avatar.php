<?php
#------------------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                                      #
#  File    : balise/spipbb_avatar                                        #
#  Authors : Chryjs, 2008 +                                              #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs               #
#  Contact : chryjs!@!free!.!fr                                          #
#  balise #SPIPBB_AVATAR                                                 #
#------------------------------------------------------------------------#

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

function balise_SPIPBB_AVATAR($p) {
	return calculer_balise_dynamique($p,'SPIPBB_AVATAR', array());
}

function balise_SPIPBB_AVATAR_stat($args, $filtres) {
	return $args;
}

function balise_SPIPBB_AVATAR_dyn($id_auteur=0) {
		include_spip('inc/presentation');
		include_spip('inc/acces');
		include_spip('inc/autoriser');

	$id_auteur=intval($id_auteur);

	// Interface de logo
	$iconifier = charger_fonction('iconifier', 'inc');

	if ($id_auteur > 0) {
		return $iconifier('id_auteur', $id_auteur, 'auteur_infos', false, autoriser('modifier', 'auteur', $id_auteur));
		//$js = "<script src='"._DIR_JAVASCRIPT."layer.js' type='text/javascript'></script>\n";
		//return $js."ICONIFIER".$iconifier('id_auteur', $id_auteur, 'spipbb_infos')."ICONIFIER";
	}
	else
		return ;
}

?>
