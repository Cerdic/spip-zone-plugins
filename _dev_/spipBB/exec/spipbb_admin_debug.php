<?php
#---------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                             #
#  File    : exec/spipbb_admin_debug - base admin menu          #
#  Authors : Chryjs, 2007                                       #
#  Contact : chryjs!@!free!.!fr                                 #
# [en] admin menus                                              #
# [fr] menus d'administration                                   #
#---------------------------------------------------------------#

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
/*
spip_log(__FILE__.' : included','spipbb');

if (defined("_GENERAL_DEBUG")) return; else define("_GENERAL_DEBUG", true);

include_spip("inc/spipbb");
include_spip('inc/interface_admin');
*/


// ------------------------------------------------------------------------------
// Affiche le debogage pour la version SVN
// ------------------------------------------------------------------------------
function exec_spipbb_admin_debug() {

	# initialiser spipbb
	include_spip('inc/spipbb_init');
	
	# initialiser spipbb
	include_spip('inc/spipbb_init');
	
	# requis de cet exec
	#

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_L('titre_page_'._request('exec')), "forum", "spipbb_admin", '');
	echo "<a name='haut_page'></a>";


	echo debut_gauche('',true);
		spipbb_menus_gauche(_request('exec'));
		
	echo creer_colonne_droite('', true);
	
	echo debut_droite('',true);

	echo spipbb_show_debug();

	# pied page exec
	bouton_retour_haut();
	
	echo fin_gauche(), fin_page();
} // exec_spipbb_admin_debug

// ------------------------------------------------------------------------------
// [fr] Affiche les infos de debogage
// ------------------------------------------------------------------------------
function spipbb_show_debug()
{
	#$loc_meta = @unserialize($GLOBALS['meta']['spipbb']);
	$res = debut_cadre_trait_couleur('',true,'xxx',_L('SpipBB METAs'));
	#$res.= "<fieldset style='border:1px solid #000;'><legend>SPIPBB META</legend>";
	#$res.= print_r_html($GLOBALS['spipbb'],true);
	$res.= affiche_metas_spipbb($GLOBALS['spipbb']);
	#$res.="</fieldset>";
	$res.= fin_cadre_trait_couleur(true);
	return $res;
} // spipbb_show_debug

?>
