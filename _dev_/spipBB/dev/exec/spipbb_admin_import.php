<?php
#---------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                             #
#  File    : exec/spipbb_admin_import                           #
#  Authors : Chryjs, 2008                                       #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs      #
#  Contact : chryjs!@!free!.!fr                                 #
# [fr] Menu d'accueil pour la migration d'un forum non SPIP     #
# [en] Home page base of external forum migration into SPIP     #
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

include_spip('inc/spipbb_import');

// ------------------------------------------------------------------------------
// [fr] Methode exec
// [en] Exec method
// [fr] Affiche la page complete spip privee avec le formulaire
// [en] Provides the full spip private space form
// ------------------------------------------------------------------------------
function exec_spipbb_admin_import()
{
	spipbb_log("CALL",3,"exec_spipbb_admin_import()");

	# initialiser spipbb
	include_spip('inc/spipbb_init');

	global $connect_statut, $connect_id_rubrique;
	// [fr] Pour le moment l acces est reserve a l administrateur, a voir plus tard
	// [fr] pour tester plutot en fonction rubrique de l import comme pour les articles...
	// [en] For now the access is only allowed to the admin, will check it later
	// [en] in order to check it for each rubrique like for the articles...

	if ($connect_statut != '0minirezo') {
		debut_page(_T('icone_admin_plugin'), "configuration", "plugin");
		echo "<strong>"._T('avis_non_acces_page')."</strong>";
		echo fin_page();
		exit;
	}

	// [fr] initialisations
	// [en] initialize

	// [fr] La conf pre-existante domine
	// [en] Pre-existing config leads
	$id_rubrique = $GLOBALS['spipbb']['id_secteur'];
	if (!$id_rubrique) {
		if ($connect_id_rubrique)
			$id_rubrique = $connect_id_rubrique[0];
		else {
			$row_rub = sql_fetsel('id_rubrique','spip_rubriques','','',array('id_rubriques DESC'),'0,1');
			$id_rubrique = $row_rub['id_rubrique'];
		}
		if (!autoriser('creerarticledans','rubrique',$id_rubrique )){
			// [fr] manque de chance, la rubrique n'est pas autorisee, on cherche un des secteurs autorises
			// [en] too bad , this rubrique is not allowed, we look for the first allowed sector
			$res = sql_select('id_rubrique','spip_rubriques',array('id_parent=0'));
			while (!autoriser('creerarticledans','rubrique',$id_rubrique ) && $row_rub = sql_fetch($res)){
				$id_rubrique = $row_rub['id_rubrique'];
			}
		}
	}
	// [fr] recuperer les donnees du secteur
	// [en] load the sector datas
	$row_rub = sql_fetsel('id_secteur','spip_rubriques',array("id_rubrique=".$GLOBALS['spipbb']['id_secteur']));
	$id_secteur = $row_rub['id_secteur'];

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('spipbb:admin_titre_page_'._request('exec')), "forum", "spipbb_admin", '');

	if (empty($id_rubrique)
	   OR !autoriser('creerarticledans','rubrique',$id_rubrique)) {
		echo "<strong>"._T('avis_acces_interdit')."</strong>";
		echo fin_page();
		exit;
	}

	echo "<a name='haut_page'></a>";
	echo debut_gauche('',true);
		spipbb_menus_gauche(_request('exec'));

	echo creer_colonne_droite($id_rubrique,true);
	echo debut_droite($id_rubrique,true);

	echo spipbb_import_formulaire($id_rubrique,$id_secteur);
	echo fin_gauche(), fin_page();
} // exec_spipbb_admin_import

// ------------------------------------------------------------------------------
// [fr] Genere le formulaire de saisie des parametres de migration
// [en] Generates the form to fill with migration parameters
// ------------------------------------------------------------------------------
function spipbb_import_formulaire($id_rubrique=0,$id_secteur=0)
{
	global $spipbb_import; // stockage des informations et des etapes

	// chryjs :  7/9/8 recuperer_fond est maintenant dans inc/utils
	if (!function_exists('recuperer_fond')) include_spip('inc/utils');

	import_load_metas('');
	if (!empty($spipbb_import['origine']) and $spipbb_import['etape'] != 0) {
		//
		// Il y a deja un import en cours...
		//

		$contexte = array( 
				'lien_action' => generer_action_auteur('spipbb_import',$id_rubrique,$retour) ,
				'exec_script' => 'spipbb_fromphpbb',
				'origine' => $spipbb_import['origine'],
				'etape' => $spipbb_import['etape'],
				);
		$res = recuperer_fond("prive/spipbb_admin_import_relance",$contexte) ;

	}
	else {
		//
		// C'est bien un nouvel import
		//

		$genere_liste_sources=import_charger_fonction(_request('origine'),'import_genere_liste_sources');
		$liste_sources=$genere_liste_sources($radio);

		$aider = charger_fonction('aider', 'inc');
		$config = "";
		$retour ="exec=spipbb_admin_import";

		$choix_rubrique = editer_article_rubrique($id_rubrique, $id_secteur, $config, $aider);

		$contexte = array( 
				'lien_action' => generer_action_auteur('spipbb_import',$id_rubrique,$retour) ,
				'exec_script' => 'spipbb_fromphpbb',
				'import_liste_fichiers' => $liste_sources,
				'choix_rubrique' => $choix_rubrique,
				'radio_checked' => $radio,
				'import_test' => _SPIPBB_IMPORT_TEST,
				);
		$res = recuperer_fond("prive/spipbb_admin_import_depart",$contexte) ;

	} // if (import en cours)

	return $res;
} // spipbb_import_formulaire

?>
