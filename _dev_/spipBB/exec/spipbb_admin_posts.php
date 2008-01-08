<?php
#---------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                             #
#  File    : exec/spipbb_admin_posts                            #
#  Authors : Chryjs, 2008                                       #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs      #
#  Contact : chryjs!@!free!.!fr                                 #
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
// [fr] Methode exec
// [en] Exec method
// [fr] Affiche la page complete spip privee avec le formulaire
// [en] Provides the full spip private space form
// ------------------------------------------------------------------------------
function exec_spipbb_admin_posts()
{
	include_spip('inc/spipbb_init');


	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('spipbb:admin_titre_page_'._request('exec')), "forum", "spipbb_admin", '');

	echo debut_gauche('',true);
		spipbb_menus_gauche(_request('exec'));

	echo creer_colonne_droite('',true);
	echo debut_droite('',true);

	echo spipbb_admin_posts();
	echo fin_gauche(), fin_page();

} // exec_spipbb_admin_posts

// ------------------------------------------------------------------------------
// [fr] Genere le formulaire de saisie des parametres de migration
// [en] Generates the form to fill with migration parameters
// ------------------------------------------------------------------------------
function spipbb_admin_posts() {
	$assembler = charger_fonction('assembler', 'public'); // recuperer_fond est dedans
	if (!function_exists('recuperer_fond')) include_spip('public/assembler');

	if ( ($id_auteur=_request('id_auteur'))!==NULL) {
		$contexte = array(
				'id_auteur' => $id_auteur,
				);
		$res = recuperer_fond("prive/spipbb_admin_posts_auteur",$contexte) ;
		return $res;
	}

} // spipbb_admin_posts

?>
