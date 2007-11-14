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
	$modules['01_general']['01_configuration'] = $file;
	return;
}

// ------------------------------------------------------------------------------
// [fr] Genere la page de gestion globale des forums
// ------------------------------------------------------------------------------
function exec_spipbb_admin_configuration()
{
	// est-ce qu'un redacteur peut voir ca ??

	// [fr] On verifie a quelle etape de la configuration on est
	// [en] We check which config stage it is
	if (!spipbb_is_configured()) spipbb_upgrade_all();
	if (!spipbb_check_tables()) {
		// creer un upgrade_tables pour faire tout cela
		include_spip('base/spipbb'); // inclure nouveau schema
		include_spip('base/create');
		include_spip('base/abstract_sql');
		creer_base();
		$GLOBALS['spipbb']['config_tables']='oui';
		spipbb_save_metas();
		spip_log('spipbb : exec_spipbb_admin_configuration tables OK');
	}

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
	echo spipbb_admin_gauche('spipbb_admin_configuration');

	echo creer_colonne_droite($id_rubrique,true);
	echo debut_droite($id_rubrique,true);

	echo spipbb_admin_configuration($row);

	echo fin_gauche(), fin_page();

} // exec_spipbb_admin_configuration

// ------------------------------------------------------------------------------
// [fr] Affiche la partie configuration des forums avec le fond situe dans prive/
// ------------------------------------------------------------------------------
function spipbb_admin_configuration()
{
	if (!function_exists('recuperer_fond')) include_spip('public/assembler'); // voir un charger fonction
	$etat_tables = spipbb_check_tables() ? 'oui' : 'non' ;
	// rajouter tests sur prerequis plugins config spip -motcles forums-
	$etat_spip = spipbb_check_spip_config();

	$contexte = array( 
			'lien_action' => generer_action_auteur('spipbb_admin_reconfig', 'save',generer_url_ecrire('spipbb_admin_configuration')), // generer_url_action ?
			'exec_script' => 'spipbb_admin_reconfig',
			'etat_tables' => $etat_tables ,
			'etat_spip' => $etat_spip ,
			'config_spipbb' => $GLOBALS['spipbb']['configure'],
			'spipbb_id_secteur' => $GLOBALS['spipbb']['id_secteur'] ,
			'id_groupe_mot' => $GLOBALS['spipbb']['id_groupe_mot'] ,
			'id_mot_ferme' => $GLOBALS['spipbb']['id_mot_ferme'],
			'id_mot_annonce' => $GLOBALS['spipbb']['id_mot_annonce'],
			'id_mot_postit' => $GLOBALS['spipbb']['id_mot_postit'],
			'squelette_groupeforum' => $GLOBALS['spipbb']['squelette_groupeforum'],
			'squelette_filforum' => $GLOBALS['spipbb']['squelette_filforum'],
			);
	$res = recuperer_fond("prive/spipbb_admin_configuration",$contexte) ;

	return $res;
} // spipbb_admin_configuration

// ------------------------------------------------------------------------------
// ------------------------------------------------------------------------------
function spipbb_check_spip_config() {
	// utiliser mot cles

	// mots_cles_forums articles_mots + mots_cles_forums
	if ( $GLOBALS['meta']['articles_mots']=='oui' ) $resultat=_T('spipbb:admin_spip_mots_cles_ok');
	else $resultat=_T('spipbb:admin_spip_mots_cles_erreur');
	$resultat.="<br />";
	if ( $GLOBALS['meta']['mots_cles_forums']=='oui' ) $resultat.=_T('spipbb:admin_spip_mots_forums_ok');
	else $resultat.=_T('spipbb:admin_spip_mots_forums_erreur');
	return $resultat;
}

?>
