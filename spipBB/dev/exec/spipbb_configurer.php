<?php
#----------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                        #
#  File    : action/spipbb_configurer                      #
#  Authors : chryjs, 2008                                  #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs #
#  Contact : chryjs¡@!free¡.!fr                            #
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

// * [fr] Acces restreint, plugin pour SPIP * //
// * [en] Restricted access, SPIP plugin * //

// inspire de ecrire/exec/configurer.php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/actions'); // pour fonction ajax_retour() 
include_spip('inc/spipbb_common');
spipbb_log('included',2,__FILE__);

// reaffichage du formulaire d'une option de configuration
// apres sa modification par appel du script action/configurer
// redirigeant ici.

function exec_spipbb_configurer_dist()
{
	if (version_compare($GLOBALS['spip_version_code'],_SPIPBB_REV_AJAXCONFIG,'>=')) {
		$configuration = charger_fonction(_request('configuration'), 'configuration', true);
		ajax_retour($configuration ? $configuration() : 'configure quoi?');
	}
	else { // 1.9.2
		include_spip('inc/headers');
		redirige_par_entete(generer_url_ecrire(_request('script')));
	}
}
?>
