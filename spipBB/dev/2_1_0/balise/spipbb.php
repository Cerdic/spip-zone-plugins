<?php
#------------------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                                      #
#  File    : balise/spipbb - balise #SPIPBB                              #
#  Authors : Scoty, 2007 +                                               #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs               #
#  Contact : chryjs!@!free!.!fr                                          #
#  ex gaf_balises Scoty                                                  #
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

/*
balise standard gafospip, nom champ en arg
retour donnee brut
| gaf 0.6 - 10/11/07
*/

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/spipbb_common');
spipbb_log('included',3,__FILE__);

function balise_SPIPBB_dist($p) {
	$_id_auteur = champ_sql('id_auteur', $p);
	$_champ = interprete_argument_balise(1,$p);
	$p->code = "afficher_champ_spipbb($_id_auteur,$_champ)";
	$p->interdire_scripts = true;
	return $p;
}

?>