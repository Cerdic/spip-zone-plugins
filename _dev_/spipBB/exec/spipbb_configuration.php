<?php
#------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                          #
#  File    : exec/spipbb_configuration                       #
#  Authors : scoty 2007                                      #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs   #
#  Contact : scoty!@!koakidi!.!com                           #
# [fr] page de configuration                                 #
# [en] - general config page                                 #
#------------------------------------------------------------#

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

# initialiser spipbb
include_spip('inc/spipbb_init'); // + spipbb_util + spipbb_presentation + spipbb_menus_gauche

# requis de cet exec
include_spip('inc/spipbb_inc_config');
include_spip('inc/spipbb_inc_metas');

// ------------------------------------------------------------------------------
// [fr] Affichage de la page de configruation generale du plugin
// ------------------------------------------------------------------------------
function exec_spipbb_configuration() {
	# requis spip
	global 	$connect_statut,
			$connect_toutes_rubriques,
			$connect_id_auteur,
			$couleur_claire, $couleur_foncee;

	# reserve au Admins
	if ($connect_statut!='0minirezo' OR !$connect_toutes_rubriques) {
		debut_page(_T('icone_admin_plugin'), "configuration", "plugin");
		echo _T('avis_non_acces_page');
		echo fin_page();
		exit;
	}

	# cas install
	if(!spipbb_is_configured()) {
		spipbb_upgrade_metas($GLOBALS['spipbb']['version'],$GLOBALS['spipbb_plug_version']);
		spipbb_upgrade_tables($GLOBALS['spipbb']['version']);
	}

	#
	# affichage
	#
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('spipbb:admin_titre_page_'._request('exec')), "configuration", "spipbb_configuration");
	echo "<a name='haut_page'></a>";

	echo debut_gauche('',true);
	spipbb_menus_gauche(_request('exec'));

	echo debut_droite('',true);

	# install ou maj
	echo spipbb_admin_configuration();

	# pied page exec
	bouton_retour_haut();

	echo fin_gauche(), fin_page();
} // exec_spipbb_config


// ------------------------------------------------------------------------------
// [fr] Affiche la partie configuration des forums avec le fond situe dans prive/
// ------------------------------------------------------------------------------
function spipbb_admin_configuration() {

	spipbb_log('DEBUT',3,"spipbb_configuration()");

	# h : cet appel vers "assembler" et donc l_usage de skel backoffice
	# vont bloquer certaines redef de fonctions spip ...
	# très genant !!!
	# c: 11/1/8 je ne vois pas ce qui est bloque ? precise ?

	$assembler = charger_fonction('assembler', 'public'); // recuperer_fond est dedans
	if (!function_exists('recuperer_fond')) include_spip('public/assembler'); // voir un charger fonction

	$prerequis=true;

	$etat_tables=$etat_spip=$etat_plugins="";

	# verif etat des tables spipbb (pures)
	$check_tables = spipbb_check_tables();
	while (list($table,$etat) = each($check_tables)) {
			$etat_tables.="<li>$table : ";
			$etat_tables.= ($etat) ? _T('spipbb:admin_config_tables_ok') : _T('spipbb:admin_config_tables_erreur');
			$etat_tables.="</li>";
		# h. ne sert pas !?! ..
		#if (!$etat) $GLOBALS['spipbb']['config_tables']='non';
			$prerequis = ($prerequis AND $etat);
	}
	# verif etat spip config (mots sur article/forum)
	$check_spip = spipbb_check_spip_config();
	while (list($spip_s,$elem_conf) = each($check_spip)) {
			$etat_spip.="<li>$spip_s : ";
			$etat_spip.= $elem_conf['message'];
			$etat_spip.="</li>";
			$prerequis = ($prerequis AND $elem_conf['etat']);
	}
	# verif etat de presence plugins-requis
	$check_plugins = spipbb_check_plugins_config();
	while (list($plug,$etat) = each($check_plugins)) {
			$etat_plugins.="<li>$plug : ";
			$etat_plugins.= ($etat) ? _T('spipbb:admin_plugin_requis_ok') : _T('spipbb:admin_plugin_requis_erreur');
			$etat_plugins.="</li>";
			$prerequis = ($prerequis AND $etat);
	}

	if (!$prerequis) {
		# sur install ou MaJ ou reconfig si pas prerequis : config a non !
		$GLOBALS['spipbb']['configure']='non';
		spipbb_save_metas();
	}
	$contexte = array(
			'lien_action' => generer_action_auteur('spipbb_admin_reconfig', 'save',generer_url_ecrire('spipbb_configuration')), // generer_url_action ?
			'exec_script' => 'spipbb_admin_reconfig',
			'etat_tables' => $etat_tables ,
			'etat_plugins' => $etat_plugins,
			'etat_spip' => $etat_spip ,
			'prerequis' => $prerequis ? 'oui':'non',
			'config_spipbb' => $GLOBALS['spipbb']['configure'],
			'spipbb_id_secteur' => $GLOBALS['spipbb']['id_secteur'] ,
			'id_groupe_mot' => $GLOBALS['spipbb']['id_groupe_mot'] ,
			'id_mot_ferme' => $GLOBALS['spipbb']['id_mot_ferme'],
			'id_mot_annonce' => $GLOBALS['spipbb']['id_mot_annonce'],
			'id_mot_postit' => $GLOBALS['spipbb']['id_mot_postit'],
			'squelette_groupeforum' => $GLOBALS['spipbb']['squelette_groupeforum'],
			'squelette_filforum' => $GLOBALS['spipbb']['squelette_filforum'],
			'fixlimit' => $GLOBALS['spipbb']['fixlimit'],
			'lockmaint' => $GLOBALS['spipbb']['lockmaint'],
			'affiche_bouton_abus' => $GLOBALS['spipbb']['affiche_bouton_abus'],
			'affiche_bouton_rss' => $GLOBALS['spipbb']['affiche_bouton_rss'],
			'affiche_avatar' => $GLOBALS['spipbb']['affiche_avatar'],
			'taille_avatar_suj' => $GLOBALS['spipbb']['taille_avatar_suj'],
			'taille_avatar_cont' => $GLOBALS['spipbb']['taille_avatar_cont'],
			'taille_avatar_prof' => $GLOBALS['spipbb']['taille_avatar_prof'],
			'affiche_membre_defaut' => $GLOBALS['spipbb']['affiche_membre_defaut'],
			'log_level' => $GLOBALS['spipbb']['log_level'],
			);
	$res = recuperer_fond("prive/spipbb_admin_configuration",$contexte) ;
	spipbb_log('END',3,"spipbb_configuration()");

	if($GLOBALS['spipbb']['configure']=='oui') {
		$res.= spipbb_config_support_auteurs();
		$res.= spipbb_config_champs_supp();
	}

	$res .= "</form>"; //temporaire mais necessaire

	return $res;

} // spipbb_admin_configuration


// ------------------------------------------------------------------------------
#
# infos supp. auteurs extra/table
#
// adapte de scoty gaf_install
// ------------------------------------------------------------------------------
function spipbb_config_support_auteurs()
{
	#$options_sap = array('extra','table','autre');
	$options_sap = array('extra','table');

	$res = debut_cadre_trait_couleur("",true,"",_T('spipbb:config_champs_auteurs_plus'));
	$res.= "<table width='100%' cellpadding='2' cellspacing='0' border='0' align='center' class='verdana2'>\n";

	# les champs, infos
	$res.= "<tr><td colspan='3'>". _T('spipbb:config_champs_requis') . "</td></tr>\n";
	$res.= "<tr><td colspan='3'>";
	foreach($GLOBALS['champs_sap_spipbb'] as $champ => $def) {
		$res.= "<b>".$champ."</b>, ".$def['info']."<br />";
	}
	$res.= "</td></tr>\n";
	$res.= "<tr><td colspan='3'>&nbsp;</td></tr>\n";

	# mode d exploitation
	$res.= "<tr><td colspan='3'>". _T('spipbb:config_orig_extra');
	$res.= "</td></tr>\n";
	$res.= "<tr><td>". _T('spipbb:config_orig_extra_info') .
		"</td><td width='5%'></td><td width='25%'>\n";

	# choix du mode
	foreach($options_sap as $val) {
		$aff_checked = ($GLOBALS['spipbb']['support_auteurs']==$val) ? 'checked=\"checked\"' : '' ;
		$res.= "<input type='radio' name='support_auteurs' value='".$GLOBALS['spipbb']['support_auteurs']."' ".$aff_checked." />&nbsp;"._L($val)."<br />";
	}
	$res.= "[spip_]<input type='text' name='table_support' value='".$GLOBALS['spipbb']['table_support']."' size='8' />";
	$res.= "</td></tr>\n";

	$res.= "<tr><td colspan='3'>&nbsp;</td></tr>\n";
	$res.= "</table>\n";
	$res.= "<div align='right'><input type='submit' name='_spipbb_ok' value='"._T('valider')."' class='fondo' /></div>\n";

	$res.= fin_cadre_trait_couleur(true);
	return $res;
} // spipbb_config_infos_auteurs

// ------------------------------------------------------------------------------
# affichage champs suppl. (hors champs imperatif)
// ------------------------------------------------------------------------------
function spipbb_config_champs_supp() {

	$options_a = array('oui','non');

	$requis = array('date_crea_spipbb','avatar','annuaire_forum','refus_suivi_thread');
	$definis =array();

	foreach($GLOBALS['champs_sap_spipbb'] as $k => $v) { $definis[]=$k; }
	$montre = array_diff($definis,$requis);

	$res = debut_cadre_trait_couleur("",true,"",_T('spipbb:config_affiche_extra'));
	$res.= "<table width='100%' cellpadding='2' cellspacing='0' border='0' align='center' class='verdana2'>\n";

	foreach($montre as $chp) {
		# champs X
		$res.= "<tr><td valign='top'>"._T('spipbb:config_affiche_champ_extra',array('nom_champ',$chp)).'<br />'
			. $GLOBALS['champs_sap_spipbb'][$chp]['info'])
			. "</td><td width='5%'> </td><td width='25%'>\n";
		foreach($options_a as $val) {
			$param=($GLOBALS['spipbb']['affiche_'.$chp])? $GLOBALS['spipbb']['affiche_'.$chp]:'oui';
			$aff_checked = ($param==$val) ? 'checked=\"checked\"' : '' ;
			$res.= "<input type='radio' name='affiche_$chp' value='".$val."' ".$aff_checked." />&nbsp;"._L($val)."&nbsp;&nbsp;&nbsp;";
		}
			$res.= "</td></tr>\n"
			. "<tr><td colspan='3'>&nbsp;</td></tr>\n";
	}

	$res.= "<tr><td colspan='3'>&nbsp;</td></tr>\n"
		. "</table>\n"
		. "<div align='right'><input type='submit' name='_spipbb_ok' value='"._T('valider')."' class='fondo' /></div>\n";
	$res.= fin_cadre_trait_couleur(true);

	return $res;
}


?>
