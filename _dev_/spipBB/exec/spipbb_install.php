<?php
#---------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                             #
#  File    : exec/spipbb_install - config SpipBB                #
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

// ------------------------------------------------------------------------------
// [fr] Genere la page de configuration initiale
// ------------------------------------------------------------------------------
function exec_spipbb_install()
{
	global $sppipbb_config, $spipbb;

	// liste de tout ce qui doit etre configure
	// manque tables / extras
	$a_configurer = array (
			0=>'tables',
			1=>'secteur_forum',
			2=>'groupe_mots_cles',
			3=>'mots_cles',
			4=>'parametres',
				);

	// [fr] On verifie a quelle etape de la configuration on est
	// [en] We check which config stage it is
	if (!spipbb_check_tables()) {
		include_spip('base/spipbb'); // inclure nouveau schema
		include_spip('base/create');
		include_spip('base/abstract_sql');
		creer_base();
		$spipbb_config['tables']='oui';
		$etape=0;
		spip_log('spipbb : exec_spipbb_install 0-tables OK');
	}
	elseif ( ($spipbb_config['secteur_forum']!='oui') OR (empty($spipbb['id_secteur'])) ) $etape=1;
	elseif ( ($spipbb_config['groupe_mots_cles']!='oui') OR (empty($spipbb['id_groupe_mot'])) ) $etape=2;
	elseif ( ($spipbb_config['mots_cles']!='oui') OR (empty($spipbb['id_mot_ferme']))
		OR  (empty($spipbb['id_mot_annonce'])) OR (empty($spipbb['id_mot_postit'])) ) $etape=3;
	else $etape=4;

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('spipbb:titre_spipbb'), "configuration", 'spipbb_install');

	echo gros_titre(_T('spipbb:titre_spipbb'),'',false) ;

//	echo debut_grand_cadre(true);
//	echo afficher_hierarchie($id_rubrique);
//	echo fin_grand_cadre(true);

	echo debut_gauche('',true);
	echo debut_boite_info(true);
	echo  _T('spipbb:install');
	echo fin_boite_info(true);

	echo spipbb_admin_gauche($spipbb['id_secteur'],'spipbb_install');

	echo creer_colonne_droite($spipbb['id_secteur'],true);
	echo debut_droite($spipbb['id_secteur'],true);

	echo spipbb_install_etape($etape);

	echo fin_gauche(), fin_page();
} // exec_spipbb_install

// ------------------------------------------------------------------------------
// [fr] Affiche l'etat de la configuration et permet de saisir les informations
// [en] Prompts the config state and allows to fill informations
// Modeles utilises / Skeletons used :
// prive/spipbb_install_global
// prive/spipbb_install_etape_tables
// prive/spipbb_install_etape_secteur_forum
// prive/spipbb_install_etape_groupe_mot
// prive/spipbb_install_etape_secteur_forum
// ------------------------------------------------------------------------------
function spipbb_install_etape($etape)
{
	global $spipbb_config,$spipbb;

	if (!function_exists('recuperer_fond')) include_spip('public/assembler');// voir un charger fonction

	// etape 0
	$etat_tables = spipbb_check_tables() ? 'oui' : 'non' ;
	$contexte = array( 'etat_tables' => $etat_tables );
/*
	$affichage = recuperer_fond("prive/spipbb_install_etape_tables",$contexte);

	if ($etat_tables=='oui') {
		// etape 1
		$contexte = array( 'id_secteur_spipbb' => $spipbb['id_secteur'] );
		$affichage .= recuperer_fond("prive/spipbb_install_etape_secteur_forum",$contexte);
		if ( ($spipbb_config['secteur_forum']=='oui') AND (!empty($spipbb['id_secteur'])) ) {
			// etape 2
			$contexte = array( 'id_groupe_mot' => $spipbb['id_groupe_mot'] );
			$affichage .= recuperer_fond("prive/spipbb_install_etape_groupe_mot",$contexte)
			if ( ($spipbb_config['mots_cles']=='oui') AND (!empty($spipbb['id_mot_ferme']))
				AND (!empty($spipbb['id_mot_annonce'])) AND (!empty($spipbb['id_mot_postit'])) ) {
				// etape 3
				$contexte = array( 	'id_mot_ferme' => $spipbb['id_mot_ferme'],
							'id_mot_annonce' => $spipbb['id_mot_ferme'],
							'id_mot_postit' => $spipbb['id_mot_postit'] );
				$affichage .= recuperer_fond("prive/spipbb_install_etape_secteur_forum",$contexte);
			} //etape 3
		} // etape 2
	} // etape 1

	$contexte = array( 'bloc_affichage' => $affichage );
	$affichage = recuperer_fond("prive/spipbb_install_global",$contexte);
*/

	$contexte = array(
			'etape' => $etape,
			'etat_tables' => $etat_tables ,
			'id_secteur_spipbb' => $spipbb['id_secteur'] ,
			'id_groupe_mot' => $spipbb['id_groupe_mot'] ,
			'id_mot_ferme' => $spipbb['id_mot_ferme'],
			'id_mot_annonce' => $spipbb['id_mot_ferme'],
			'id_mot_postit' => $spipbb['id_mot_postit']
			);
	$affichage = recuperer_fond("prive/spipbb_install_global",$contexte);

	return $affichage;
} // spipbb_install_etape

?>
