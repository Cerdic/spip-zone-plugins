<?php
#---------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                             #
#  File    : exec/spipbb_admin_anti_spam_words                  #
#  Authors : Chryjs, 2007 et als                                #
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
spip_log(__FILE__.' : included','spipbb');

if ( !empty($setmodules) )
{
	if ( spipbb_is_configured() and $GLOBALS['spipbb']['configure']=='oui'
		 and $GLOBALS['spipbb']['config_spam_words']=='oui') {
		$file = basename(__FILE__);
		$modules['spam']['swwords'] = $file;
	}
	return;
}
if (defined("_SPAM_SWWORDS")) return; else define("_SPAM_SWWORDS", true);

// ------------------------------------------------------------------------------
// [fr] Methode exec
// [en] Exec method
// [fr] Affiche la page complete spip privee avec le formulaire
// [en] Provides the full spip private space form
// ------------------------------------------------------------------------------
function exec_spipbb_admin_anti_spam_words()
{
	include_spip('inc/spipbb');
	if (!spipbb_is_configured() or ($GLOBALS['spipbb']['configure']!='oui')) {
		include_spip('inc/headers');
		redirige_par_entete(generer_url_ecrire('spipbb_admin_configuration', ''));
		exit;
	}

	if ($GLOBALS['spipbb']['config_spam_words']!='oui') {
		include_spip('inc/headers');
		redirige_par_entete(generer_url_ecrire('spipbb_admin_anti_spam_config', ''));
		exit;
	}

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('spipbb:sw_spam_words_titre'), "configuration", 'spipbb');

	echo gros_titre(_T('spipbb:titre_spipbb'),'',false) ;

	if (spipbb_is_configured() AND 	$GLOBALS['spipbb']['config_id_secteur'] == 'oui' ) {
		echo debut_grand_cadre(true);
		echo afficher_hierarchie($GLOBALS['spipbb']['id_secteur']);
		echo fin_grand_cadre(true);
	}

	echo debut_gauche('',true);
	echo debut_boite_info(true);
	echo  _T('spipbb:sw_spam_words_titre');
	echo fin_boite_info(true);
	echo spipbb_admin_gauche('spipbb_admin_anti_spam_words');

	echo creer_colonne_droite('',true);
	echo debut_droite('',true);

	echo spipbb_anti_spam_words_formulaire();
	echo fin_gauche(), fin_page();

} // exec_spipbb_admin_spam_words

// ------------------------------------------------------------------------------
// [fr] Genere le formulaire de saisie des parametres de migration
// [en] Generates the form to fill with migration parameters
// ------------------------------------------------------------------------------
function spipbb_anti_spam_words_formulaire() {
	$assembler = charger_fonction('assembler', 'public'); // recuperer_fond est dedans
	if (!function_exists('recuperer_fond')) include_spip('public/assembler');

	$contexte = array(
			'action_script' => 'spipbb_spam_word',
			'exec_script' => 'spipbb_admin_anti_spam_words',
			);
	$res = recuperer_fond("prive/spipbb_admin_anti_spam_words",$contexte) ;

	return $res;
} // spipbb_anti_spam_words_formulaire

?>
