<?php

#---------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                             #
#  File    : exec/spipbb_admin_fromphpbb - first step form      #
#  Authors : 2004+ Jean-Luc B�chennec - Chryjs, 2007            #
#  Contact : chryjs�@!free�.!fr                                 #
# [fr] Menu d'accueil pour la migration d'un forum phpBB        #
# [en] Home page base of phpBB forum migration                  #
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

include_spip('inc/spipbb');
include_spip('inc/editer_article');

if( !empty($setmodules) )
{
	$file = basename(__FILE__);
	$modules['outils']['fromphpbb'] = $file;
	return;
}

// ------------------------------------------------------------------------------
// [fr] Methode exec
// [en] Exec method
// [fr] Affiche la page complete spip privee avec le formulaire
// [en] Provides the full spip private space form
// ------------------------------------------------------------------------------
function exec_spipbb_admin_fromphpbb() {
	global $connect_statut;
	// [fr] Pour le moment l acces est reserve a l administrateur, a voir plus tard
	// [fr] pour tester plutot en fonction rubrique de l import comme pour les articles...
	// [en] For now the access is only allowed to the admin, will check it later
	// [en] in order to check it for each rubrique like for the articles...
	$id_rubrique=intval(_request('id_rubrique'));

	if ($connect_statut != '0minirezo') {
		debut_page(_T('icone_admin_plugin'), "configuration", "plugin");
		echo "<strong>"._T('avis_non_acces_page')."</strong>";
		echo fin_page();
		exit;
	}

	// [fr] initialisations
	// [en] initialize
	if (!function_exists('filtrer_entites')) @include_spip('inc/filtres');
	$row['titre'] = filtrer_entites(_T('info_nouvel_article'));
	if (!isset($GLOBALS['meta']['spipbb'])) spipbb_init_metas() ;

	// [fr] La conf pre-existante domine
	// [en] Pre-existing config leads
	$row['id_rubrique'] = (!empty($GLOBALS['spipbb']['spipbb_id_rubrique'])) ? $GLOBALS['spipbb']['spipbb_id_rubrique'] : $id_rubrique;
	if (!$row['id_rubrique']) {
		if ($connect_id_rubrique)
			$row['id_rubrique'] = $id_rubrique = $connect_id_rubrique[0];
		else {
			$r = sql_query("SELECT id_rubrique FROM spip_rubriques ORDER BY id_rubrique DESC LIMIT 1");
			$row_rub = sql_fetch($r);
			$row['id_rubrique'] = $id_rubrique = $row_rub['id_rubrique'];
		}
		if (!autoriser('creerarticledans','rubrique',$row['id_rubrique'] )){
			// [fr] manque de chance, la rubrique n'est pas autorisee, on cherche un des secteurs autorises
			// [en] too bad , this rubrique is not allowed, we look for the first allowed sector
			$res = sql_query("SELECT id_rubrique FROM spip_rubriques WHERE id_parent=0");
			while (!autoriser('creerarticledans','rubrique',$row['id_rubrique'] ) && $row_rub = sql_fetch($res)){
				$row['id_rubrique'] = $row_rub['id_rubrique'];
			}
		}
	}
	// [fr] recuperer les donnees du secteur
	// [en] load the sector datas
	$r = sql_query("SELECT id_secteur FROM spip_rubriques WHERE id_rubrique=$id_rubrique");
	$row_rub = sql_fetch($r);
	$row['id_secteur'] = $row_rub['id_secteur'];
	$id_rubrique = $row['id_rubrique'];

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('spipbb:fromphpbb_titre'), "configuration", 'spipbb');

	if (!$row
	   OR !autoriser('creerarticledans','rubrique',$id_rubrique)) {
		echo "<strong>"._T('avis_acces_interdit')."</strong>";
		echo fin_page();
		exit;
	}

	echo gros_titre(_T('spipbb:titre_spipbb'),'',false) ;

	echo debut_grand_cadre(true);
	echo afficher_hierarchie($id_rubrique);
	echo fin_grand_cadre(true);

	echo debut_gauche('',true);
	echo debut_boite_info(true);
	echo  _T('spipbb:fromphpbb_titre');
	echo fin_boite_info(true);
	echo spipbb_admin_gauche($GLOBALS['spipbb']['spipbb_id_rubrique'],'spipbb_admin_fromphpbb');

	echo creer_colonne_droite($id_rubrique,true);
	echo debut_droite($id_rubrique,true);

	echo debut_cadre_formulaire('',true);
	echo gros_titre(_T('spipbb:fromphpbb_titre'),'',false)."<hr />\n";
	echo spipbb_fromphpbb_formulaire($row);
	echo fin_cadre_formulaire(true);
	echo fin_gauche(), fin_page();
} // exec_spipbb_admin_fromphpbb

// ------------------------------------------------------------------------------
// [fr] Genere le formulaire de saisie des parametres de migration
// [en] Generates the form to fill with migration parameters
// ------------------------------------------------------------------------------
function spipbb_fromphpbb_formulaire($row=array()) {
	$aider = charger_fonction('aider', 'inc');
	$config = "";
	$id_rubrique = $row['id_rubrique'];
	$id_secteur = $row['id_secteur'];
	$form= "\n".
		editer_article_rubrique($id_rubrique, $id_secteur, $config, $aider) .
		"\n<p><b>" . _T('spipbb:fromphpbb_parametres_titre') . "</b><br />" .
		"\n<label for='phpbb_login'>" . _T('spipbb:fromphpbb_login') . "</label>" .
		"\n<input type='text' id='phpbb_login' name='phpbb_login' value='' /><br />" .
		"\n<label for='phpbb_pass'>" . _T('spipbb:fromphpbb_password') . "</label>" .
		"\n<input type='password' id='phpbb_pass' name='phpbb_pass' value='' /><br />" .
		"\n<label for='phpbb_base'>" . _T('spipbb:fromphpbb_base') . "</label>" .
		"\n<input type='text' id='phpbb_base' name='phpbb_base' value='' /><br />" .
		"\n<label for='phpbb_prefix'>" . _T('spipbb:fromphpbb_prefix') . "</label>" .
		"\n<input type='text' id='phpbb_prefix' name='phpbb_prefix' value='phpbb_' /><br />" .
		"\n<label for='phpbb_root'>" . _T('spipbb:fromphpbb_racine') . "</label>" .
		"\n<input type='text' id='phpbb_root' name='phpbb_root' value='' /><br />" .
		"\n<p><b>" .  _T('spipbb:fromphpbb_choix_test') .  "</b>" .
		"\n<br />" .  _T('spipbb:fromphpbb_sous_choix_test') .
		"\n<input type='checkbox' name='phpbb_test' id='phpbb_test' checked='on' class='check' /></p>" .
		"\n<div align='right'>" .
		"\n<input class='fondo' type='submit' value='" . _T('bouton_valider') ."' />" .
		"\n</div>" ;

	return generer_action_auteur("spipbb_fromphpbb_action", $id_rubrique, $retour, $form, " method='post' name='formulaire'");
} // spipbb_fromphpbb_formulaire

?>