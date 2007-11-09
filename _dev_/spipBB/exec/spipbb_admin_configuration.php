<?php
#---------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                             #
#  File    : exec/spipbb_admin_configuration - config SpipBB    #
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

include_spip('inc/spipbb');

if( !empty($setmodules) )
{
	$file = basename(__FILE__);
	$modules['01_general']['configuration'] = $file;
	return;
}

// ------------------------------------------------------------------------------
// [fr] Genere la page de gestion globale des forums
// ------------------------------------------------------------------------------
function exec_spipbb_admin_configuration()
{
	global $connect_statut, $connect_toutes_rubriques;

	// [fr] initialisations
	// [en] initialize
	$id_rubrique=intval(_request('id_rubrique'));

	// [fr] recuperer les donnees du secteur
	// [en] load the sector datas
	$row_rub = sql_fetsel("id_secteur","spip_rubriques","id_rubrique=$id_rubrique");
	$id_secteur= $row_rub['id_secteur'];

	if (_request('save')=="oui") {
		if (!autoriser('configurer', 'plugins')) {
			include_spip('inc/minipres');
			echo minipres("Erreur pas autorise");
			exit;
		}
		spipbb_save_metas();
		include_spip('inc/headers');
		redirige_par_entete(generer_url_ecrire('spipbb_admin_configuration', ''));
	} else {

	if (!isset($GLOBALS['spipbb'])) spipbb_init_metas() ;

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('spipbb:titre_spipbb'), "configuration", 'spipbb_admin_configuration');

	echo gros_titre(_T('spipbb:titre_spipbb'),'',false) ;

	echo debut_grand_cadre(true);
	echo afficher_hierarchie($id_rubrique);
	echo fin_grand_cadre(true);

	echo debut_gauche('',true);
	echo debut_boite_info(true);
	echo  _T('spipbb:admin_forums_configuration');
	echo fin_boite_info(true);
	echo spipbb_admin_gauche($GLOBALS['spipbb']['spipbb_id_rubrique'],'spipbb_admin_configuration');

	echo creer_colonne_droite($id_rubrique,true);
	echo debut_droite($id_rubrique,true);

	echo debut_cadre_formulaire('',true);
	echo spipbb_admin_configuration($row);
	echo fin_cadre_formulaire(true);

	echo fin_gauche(), fin_page();

	} // not save
} // exec_spipbb_admin_configuration

// ------------------------------------------------------------------------------
// [fr] Affiche la partie configuration des forums avec le fond situe dans prive/
// ------------------------------------------------------------------------------
function spipbb_admin_configuration()
{
	if (!function_exists('recuperer_fond')) include_spip('public/assembler');

	$contexte = array( 'spipbb_id_rubrique'=>$GLOBALS['spipbb']['spipbb_id_rubrique'],
			'lien_action' => generer_url_ecrire('spipbb_admin_configuration', 'save=oui'), // generer_url_action ?
			'exec_script' => 'spipbb_admin_configuration',
			'spipbb_id_groupe_mot' => $GLOBALS['spipbb']['spipbb_id_groupe_mot'],
			'spipbb_id_mot_ferme' => $GLOBALS['spipbb']['spipbb_id_mot_ferme'],
			'spipbb_id_mot_annonce' => $GLOBALS['spipbb']['spipbb_id_mot_annonce'],
			'spipbb_id_mot_postit' => $GLOBALS['spipbb']['spipbb_id_mot_postit'],
			'spipbb_squelette_groupeforum' => $GLOBALS['spipbb']['spipbb_squelette_groupeforum'],
			'spipbb_squelette_filforum' => $GLOBALS['spipbb']['spipbb_squelette_filforum'],

			);
	$res = recuperer_fond("prive/spipbb_admin_configuration",$contexte) ;

	return $res;
} // spipbb_admin_configuration

?>
