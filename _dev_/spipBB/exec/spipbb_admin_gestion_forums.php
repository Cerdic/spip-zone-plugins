<?php
#---------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                             #
#  File    : exec/spipbb_admin_gestion_forums - base admin menu #
#  Authors : Chryjs, 2007 et als                                #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs      #
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

include_spip('inc/spipbb');
include_spip('inc/editer_article');

if ( !empty($setmodules) and spipbb_is_configured() and $GLOBALS['spipbb']['configure']=='oui' )
{
	$file = basename(__FILE__);
	$modules['01_general']['gestion'] = $file;
	return;
}

// ------------------------------------------------------------------------------
// [fr] Genere la page de gestion globale des forums
// ------------------------------------------------------------------------------
function exec_spipbb_admin_gestion_forums()
{
	if (!spipbb_is_configured() or ($GLOBALS['spipbb']['configure']!='oui')) {
		include_spip('inc/headers');
		redirige_par_entete(generer_url_ecrire('spipbb_admin_configuration', ''));
		exit;
	}

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('spipbb:titre_spipbb'), "configuration", 'spipbb_admin_gestion_forums');

	echo gros_titre(_T('spipbb:titre_spipbb'),'',false) ;

	echo debut_grand_cadre(true);
	echo afficher_hierarchie($id_rubrique);
	echo fin_grand_cadre(true);

	echo debut_gauche('',true);
	echo debut_boite_info(true);
	echo  _T('spipbb:admin_forums_titre');
	echo fin_boite_info(true);
	echo spipbb_admin_gauche('spipbb_admin_gestion_forums');

	echo creer_colonne_droite('',true);
	echo debut_droite('',true);

	spipbb_renumerote();
	echo spipbb_admin_forums($row);

	echo fin_gauche(), fin_page();
} // exec_spipbb_admin_forums

// ------------------------------------------------------------------------------
// [fr] Affiche la partie gestion des forums avec le fond situe dans prive/
// ------------------------------------------------------------------------------
function spipbb_admin_forums()
{
	if (!function_exists('recuperer_fond')) include_spip('public/assembler');

	$contexte = array( 
			'id_secteur'=>$GLOBALS['spipbb']['id_secteur'],
			);
	$res = recuperer_fond("prive/spipbb_admin_gestion_forums",$contexte) ;

	return $res;
} // spipbb_admin_forums

?>
