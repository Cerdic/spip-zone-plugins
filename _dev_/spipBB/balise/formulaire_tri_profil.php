<?php
#-------------------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                                       #
#  File    : balise/formulaire_tri_profil - balise #FORMULAIRE_TRI_PROFIL #
#  Authors : Scoty, 2007 +                                                #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs                #
#  Contact : chryjs!@!free!.!fr                                           #
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

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite
include_spip('inc/spipbb_common'); // define + log
spipbb_log('included',2,__FILE__);

function balise_FORMULAIRE_TRI_PROFIL ($p) {
	spipbb_log('main',2,__FILE__);
	return calculer_balise_dynamique($p, 'FORMULAIRE_TRI_PROFIL', array());
}

function balise_FORMULAIRE_TRI_PROFIL_dyn() {
	spipbb_log('dyn',2,__FILE__);

	return array("formulaires/tri_profil", 0,
			array('mode' => _request('mode'),
				'ordre' => _request('ordre'),
				'self' => self()
			)
		);
}

?>