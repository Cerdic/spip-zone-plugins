<?php
#---------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                             #
#  File    : exec/spipbb_admin - base admin menu                #
#  Authors : Chryjs, 2007                                       #
#  Contact : chryjs¡@!free¡.!fr                                 #
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

if( !empty($setmodules) )
{
	$file = basename(__FILE__);
	$modules['01_general']['gestion'] = $file;
	return;
}

// ------------------------------------------------------------------------------
// [fr] Genere la page de gestion globale des forums
// ------------------------------------------------------------------------------
function exec_spipbb_admin_forums()
{
	global $connect_statut, $connect_toutes_rubriques;

	$id_rubrique=intval(_request('id_rubrique'));

	// [fr] recuperer les donnees du secteur
	// [en] load the sector datas
	$row_rub = sql_fetsel("id_secteur","spip_rubriques","id_rubrique=$id_rubrique");
	$id_secteur= $row_rub['id_secteur'];

	// [fr] initialisations
	// [en] initialize
	if (!isset($GLOBALS['spipbb'])) spipbb_init_metas() ;

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('spipbb:titre_spipbb'), "configuration", 'spipbb_admin_forums');

	echo gros_titre(_T('spipbb:titre_spipbb'),'',false) ;

	echo debut_grand_cadre(true);
	echo afficher_hierarchie($id_rubrique);
	echo fin_grand_cadre(true);

	echo debut_gauche('',true);
	echo debut_boite_info(true);
	echo  _T('spipbb:admin_forums_titre');
	echo fin_boite_info(true);
	echo spipbb_admin_gauche($GLOBALS['spipbb']['spipbb_id_rubrique'],'spipbb_admin_forums');

	echo creer_colonne_droite($id_rubrique,true);
	echo debut_droite($id_rubrique,true);

//	echo debut_cadre_formulaire('',true);
	spipbb_renumerote();
	echo spipbb_admin_forums($row);
//	echo fin_cadre_formulaire(true);

	echo fin_gauche(), fin_page();
} // exec_spipbb_admin_forums

// ------------------------------------------------------------------------------
// [fr] Affiche la partie gestion des forums avec le fond situe dans prive/
// ------------------------------------------------------------------------------
function spipbb_admin_forums()
{
	if (!function_exists('recuperer_fond')) include_spip('public/assembler');
	$securiser_action = charger_fonction('securiser_action', 'inc');
	// on sait que cette fonction est dans le fichier associe
	$hash_supprimer = calculer_action_auteur("supprimer");
	$hash_move = calculer_action_auteur("spipbb_move",$GLOBALS['spipbb']['spipbb_id_rubrique']);
	$hash_instituer = calculer_action_auteur("instituer_article");

	$contexte = array( 
			'id_rubrique'=>$GLOBALS['spipbb']['spipbb_id_rubrique'],
			'hash_move' => $hash_move,
			'hash_supprimer' => $hash_supprimer,
			'hash_instituer' => $hash_instituer
			);
	$res = recuperer_fond("prive/spipbb_admin_forums",$contexte) ;

	return $res;
} // spipbb_admin_forums


// http://www.firewall-net.com/phpBB2/admin/admin_forums.php?mode=forum_order&move=-15&f=2&sid=925c4f9ea2841dfd4c3491cfe3361352
?>
