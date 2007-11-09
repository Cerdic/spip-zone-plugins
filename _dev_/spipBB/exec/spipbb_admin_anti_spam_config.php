<?php

#---------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                             #
#  File    : exec/spipbb_admin_anti_spam_config                 #
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

include_spip('inc/spipbb');

if( !empty($setmodules) )
{
	$file = basename(__FILE__);
	$modules['spam']['swconfig'] = $file;
	return;
}

// ------------------------------------------------------------------------------
// [fr] Methode exec
// [en] Exec method
// [fr] Affiche la page complete spip privee avec le formulaire
// [en] Provides the full spip private space form
// ------------------------------------------------------------------------------
function exec_spipbb_admin_anti_spam_config() {
	global $connect_statut;
	// [fr] Pour le moment l acces est reserve a l administrateur, a voir plus tard
	// [fr] pour tester plutot en fonction rubrique de l import comme pour les articles...
	// [en] For now the access is only allowed to the admin, will check it later
	// [en] in order to check it for each rubrique like for the articles...
	$id_rubrique=intval(_request('id_rubrique'));

	// [fr] recuperer les donnees du secteur
	// [en] load the sector datas
	$row_rub = sql_fetsel("id_secteur","spip_rubriques","id_rubrique=$id_rubrique");
	$id_secteur= $row_rub['id_secteur'];

	if ($connect_statut != '0minirezo' or !autoriser('creerarticledans','rubrique',$id_secteur) ) {
		include_spip('inc/minipres');
		echo minipres("<strong>"._T('avis_non_acces_page')."</strong>");
		exit;
	}

	if (_request('save')=="oui") {
		if (!autoriser('configurer', 'plugins')) {
			include_spip('inc/minipres');
			echo minipres("<strong>"._T('avis_non_acces_page')."</strong>");
			exit;
		}
		$GLOBALS['spipbb']['disable_sw'] = _request('disable_sw');
		$GLOBALS['spipbb']['sw_nb_spam_ban'] = _request('sw_nb_spam_ban');
		$GLOBALS['spipbb']['sw_ban_ip'] = _request('sw_ban_ip');
		$GLOBALS['spipbb']['sw_admin_can_spam'] = _request('sw_admin_can_spam');
		$GLOBALS['spipbb']['sw_modo_can_spam'] = _request('sw_modo_can_spam');
		$GLOBALS['spipbb']['sw_send_pm_warning'] = _request('sw_send_pm_warning');
		$GLOBALS['spipbb']['sw_warning_from_admin'] = _request('sw_warning_from_admin');
		$GLOBALS['spipbb']['sw_warning_pm_titre'] = _request('sw_warning_pm_titre');
		$GLOBALS['spipbb']['sw_warning_pm_message'] = _request('sw_warning_pm_message');
		spipbb_save_metas();
		//include_spip('inc/headers');
		//redirige_par_entete(generer_url_ecrire('spipbb_admin_spam_words_config', 'id_rubrique='.$id_rubrique));
	}

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('spipbb:sw_spam_titre'), "configuration", 'spipbb');

	echo gros_titre(_T('spipbb:titre_spipbb'),'',false) ;

	echo debut_grand_cadre(true);
	echo afficher_hierarchie($id_rubrique);
	echo fin_grand_cadre(true);

	echo debut_gauche('',true);
	echo debut_boite_info(true);
	echo  _T('spipbb:sw_spam_titre');
	echo fin_boite_info(true);
	echo spipbb_admin_gauche($GLOBALS['spipbb']['spipbb_id_rubrique'],'spipbb_admin_spam_words_config');

	echo creer_colonne_droite($id_rubrique,true);
	echo debut_droite($id_rubrique,true);

	echo debut_cadre_formulaire('',true);
	//echo gros_titre(_T('spipbb:sw_spam_titre'),'',false)."<hr />\n";
	echo spipbb_anti_spam_formulaire($id_rubrique);
	echo fin_cadre_formulaire(true);
	echo fin_gauche(), fin_page();

} // exec_spipbb_admin_spam_words_config

// ------------------------------------------------------------------------------
// [fr] Genere le formulaire de saisie des parametres de migration
// [en] Generates the form to fill with migration parameters
// ------------------------------------------------------------------------------
function spipbb_anti_spam_formulaire($id_rubrique) {
	if (!function_exists('recuperer_fond')) include_spip('public/assembler');

	$contexte = array(
			'lien_action' => generer_url_ecrire('spipbb_admin_anti_spam_config', 'save=oui&id_rubrique='.$id_rubrique), // generer_url_action ?
			'exec_script' => 'spipbb_admin_spam_words_config',
			'disable_sw' => $GLOBALS['spipbb']['disable_sw'],
			'sw_nb_spam_ban' => $GLOBALS['spipbb']['sw_nb_spam_ban'],
			'sw_ban_ip' => $GLOBALS['spipbb']['sw_ban_ip'],
			'sw_admin_can_spam' => $GLOBALS['spipbb']['sw_admin_can_spam'],
			'sw_modo_can_spam' => $GLOBALS['spipbb']['sw_modo_can_spam'],
			'sw_send_pm_warning' => $GLOBALS['spipbb']['sw_send_pm_warning'],
			'sw_warning_from_admin' => $GLOBALS['spipbb']['sw_warning_from_admin'],
			'sw_warning_pm_titre' => $GLOBALS['spipbb']['sw_warning_pm_titre'],
			'sw_warning_pm_message' => $GLOBALS['spipbb']['sw_warning_pm_message'],
			);
	$res = recuperer_fond("prive/spipbb_admin_anti_spam_config",$contexte) ;

	return $res;
} // spipbb_anti_spam_formulaire

?>
