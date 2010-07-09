<?php
#------------------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                                      #
#  File    : balise/tableau_smileys - balise #TABLEAU_SMILEYS            #
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

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/spipbb_common');
spipbb_log('included',3,__FILE__);

/*
+----------------------------------+
| Balise #TABLEAU_SMILEYS
| gaf 0.6 - 30/09/07
| produit le tableau des smileys sur 'n' colonnes
| [(#TABLEAU_SMILEYS)] (defaut : aff. 2 col.)
| [(#TABLEAU_SMILEYS{x})] (ou x provoque aff. de x col. !))
*/
function balise_TABLEAU_SMILEYS_dist($p) {
	$_nb_col = interprete_argument_balise(1,$p);
	$p->code = "tableau_smileys($_nb_col)";
	$p->interdire_scripts = false;
	return $p;
}

?>