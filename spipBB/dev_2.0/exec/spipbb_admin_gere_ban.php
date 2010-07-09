<?php
#---------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                             #
#  File    : exec/spipbb_admin_gere_ban                         #
#  Authors : chryjs 2007 et als                                 #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs      #
#  Contact : chryjs!@!free!.!fr                                 #
# [fr] Gestion du banissement                                   #
# [en] Manage ban lists                                         #
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

// * [fr] Acces restreint, plugin pour SPIP * //
// * [en] Restricted access, SPIP plugin * //

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/spipbb_common');
spipbb_log('included',2,__FILE__);

// ------------------------------------------------------------------------------
// ------------------------------------------------------------------------------
function exec_spipbb_admin_gere_ban() {
	# requis spip
	global $spip_display;

	# initialiser spipbb
	include_spip('inc/spipbb_init');

	#
	# affichage
	#
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('spipbb:admin_titre_page_'._request('exec')), "forum", "spipbb_admin", '');

	echo "<a name='haut_page'></a>";

	echo debut_gauche('',true);
	spipbb_menus_gauche(_request('exec'));

	echo debut_droite('',true);

	echo debut_cadre_formulaire('',true);

	echo gros_titre(_T('spipbb:admin_titre_page_'._request('exec')),'',false);

	echo debut_cadre_trait_couleur("racine-site-24.gif", true, "", _T('spipbb:admin_ban_user'));
	$spipbb_ban_user = charger_fonction('spipbb_ban_user', 'configuration');
	echo $spipbb_ban_user();
	echo fin_cadre_trait_couleur(true);

	echo debut_cadre_trait_couleur("racine-site-24.gif", true, "", _T('spipbb:admin_ban_ip'));
	$spipbb_ban_ip = charger_fonction('spipbb_ban_ip', 'configuration');
	echo $spipbb_ban_ip();
	echo fin_cadre_trait_couleur(true);

	echo debut_cadre_trait_couleur("racine-site-24.gif", true, "", _T('spipbb:admin_ban_email'));
	$spipbb_ban_email = charger_fonction('spipbb_ban_email', 'configuration');
	echo $spipbb_ban_email();
	echo fin_cadre_trait_couleur(true);

	echo fin_cadre_formulaire(true);

	# pied page exec
	bouton_retour_haut();

	echo fin_gauche(), fin_page();

} // exec_spipbb_admin_gere_ban

?>
