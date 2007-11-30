<?php
#---------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                             #
#  File    : exec/spipbb_admin_configuration - config SpipBB    #
#  Authors : Chryjs, Scoty 2007                                 #
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
spip_log(__FILE__.' : included','spipbb');

if (defined("_GENERAL_CONFIGURATION")) return; else define("_GENERAL_CONFIGURATION", true);

include_spip('inc/spipbb');
include_spip('inc/interface_admin');

// ------------------------------------------------------------------------------
// [fr] Genere la page de gestion globale des forums
// ------------------------------------------------------------------------------
function exec_spipbb_admin_configuration()
{
	// est-ce qu'un redacteur peut voir ca ??
	spip_log('exec/spipbb_admin_configuration.php exec_spipbb_admin_configuration()','spipbb');
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
		spip_log('exec/spipbb_admin_configuration.php exec_spipbb_admin_configuration() installation tables -fini','spipbb');
	}

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('spipbb:titre_spipbb'), "configuration", 'spipbb_admin_configuration');

	echo gros_titre(_T('spipbb:titre_spipbb'),'',false) ;

	if (spipbb_is_configured() AND $GLOBALS['spipbb']['config_id_secteur'] == 'oui' ) {
		echo debut_grand_cadre(true);
		echo afficher_hierarchie($GLOBALS['spipbb']['id_secteur']);
		echo fin_grand_cadre(true);
	}

	echo debut_gauche('',true);
	echo debut_boite_info(true);
	echo  _T('spipbb:admin_forums_configuration');
	echo fin_boite_info(true);
	echo spipbb_admin_gauche('spipbb_admin_configuration');

	echo creer_colonne_droite('',true);
	echo debut_droite('',true);

	echo spipbb_admin_configuration($row);
	if (spipbb_check_spip_config() and $GLOBALS['spipbb']['configure']=='oui') {
		echo spipbb_config_infos_auteurs();
	}
	echo "</form>"; // integration formulaires ci-dessus orig Scoty : gaf_install
	
	echo fin_gauche(), fin_page();

} // exec_spipbb_admin_configuration

// ------------------------------------------------------------------------------
// [fr] Affiche la partie configuration des forums avec le fond situe dans prive/
// ------------------------------------------------------------------------------
function spipbb_admin_configuration()
{
	spip_log('exec/spipbb_admin_configuration.php spipbb_admin_configuration() DEBUT','spipbb');
	$assembler = charger_fonction('assembler', 'public'); // recuperer_fond est dedans
	if (!function_exists('recuperer_fond')) include_spip('public/assembler'); // voir un charger fonction
	$prerequis=true;
	$etat_tables=$etat_spip=$etat_plugins="";
	$check_tables = spipbb_check_tables();
	while (list($table,$etat) = each($check_tables)) {
			$etat_tables.="<li>$table : ";
			$etat_tables.= ($etat) ? _T('spipbb:admin_config_tables_ok') : _T('spipbb:admin_config_tables_erreur');
			$etat_tables.="</li>";
			if (!$etat) $GLOBALS['spipbb']['config_tables']='non';
			$prerequis = ($prerequis AND $etat);
	}
	$check_spip = spipbb_check_spip_config();
	while (list($spip_s,$elem_conf) = each($check_spip)) {
			$etat_spip.="<li>$spip_s : ";
			$etat_spip.= $elem_conf['message'];
			$etat_spip.="</li>";
			$prerequis = ($prerequis AND $elem_conf['etat']);
	}
	$check_plugins = spipbb_check_plugins_config();
	while (list($plug,$etat) = each($check_plugins)) {
			$etat_plugins.="<li>$plug : ";
			$etat_plugins.= ($etat) ? _T('spipbb:admin_plugin_requis_ok') : _T('spipbb:admin_plugin_requis_erreur');
			$etat_plugins.="</li>";
			$prerequis = ($prerequis AND $etat);
	}

	if (!$prerequis) {
		$GLOBALS['spipbb']['configure']='non';
		spipbb_save_metas();
	}
	$contexte = array( 
			'lien_action' => generer_action_auteur('spipbb_admin_reconfig', 'save',generer_url_ecrire('spipbb_admin_configuration')), // generer_url_action ?
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
			'taille_avatar_suj' => $GLOBALS['spipbb']['taille_avatar_suj'],
			'taille_avatar_cont' => $GLOBALS['spipbb']['taille_avatar_cont'],
			'taille_avatar_prof' => $GLOBALS['spipbb']['taille_avatar_prof'],
			);
	$res = recuperer_fond("prive/spipbb_admin_configuration",$contexte) ;
	spip_log('exec/spipbb_admin_configuration.php spipbb_admin_configuration() END','spipbb');

	return $res;
} // spipbb_admin_configuration

// ------------------------------------------------------------------------------
// ------------------------------------------------------------------------------
function spipbb_check_spip_config() {
	$res=array();
	// Les forums de SPIP sont ils actives ????????
	if ( $GLOBALS['meta']['forums_publics']!='non' ) {
		$res['forums_publics']= array( 'etat'=>true, 'message'=>_T('spipbb:admin_spip_forums_ok'));
		$resultat=_T('spipbb:admin_spip_forums_ok');
	} else {
		$res['forums_publics']= array( 'etat'=>false, 
										'message'=>_T('spipbb:admin_spip_forums_erreur')
										//'message' => spipbb_
										);
		$resultat=_T('spipbb:admin_spip_forums_erreur');
	}

	// utiliser mot cles 
	// mots_cles_forums articles_mots + mots_cles_forums
	if ( $GLOBALS['meta']['articles_mots']=='oui' ) {
		$res['articles_mots']= array( 'etat'=>true, 
										'message'=>_T('spipbb:admin_spip_mots_cles_ok')
									
										);
		$resultat=_T('spipbb:admin_spip_mots_cles_ok');
	}
	else {
		$res['articles_mots']= array( 'etat'=>false, 
										'message'=>_T('spipbb:admin_spip_mots_cles_erreur')
										//'message'=> spipbb_config_mots()
										);
		$resultat=_T('spipbb:admin_spip_mots_cles_erreur');
	}
	$resultat.="<br />";
	if ( $GLOBALS['meta']['mots_cles_forums']=='oui' ) {
		$resultat.=_T('spipbb:admin_spip_mots_forums_ok');
		$res['mots_cles_forums']= array( 'etat'=>true, 'message'=>_T('spipbb:admin_spip_mots_forums_ok'));
	}
	else {
		$res['mots_cles_forums']= array( 'etat'=>false, 'message'=>_T('spipbb:admin_spip_mots_forums_erreur'));
		$resultat.=_T('spipbb:admin_spip_mots_forums_erreur');
	}
	return $res;
	//return $resultat;
} // spipbb_check_spip_config

// ------------------------------------------------------------------------------
# controle presence plugins necessaires
// adapte de scoty gaf_install
// ------------------------------------------------------------------------------
function spipbb_check_plugins_config() {
	$resultat="";
	$res=array();
	$tab_plugins_installes = unserialize($GLOBALS['meta']['plugin']);
	if(!is_array($tab_plugins_installes['CFG'])) {
		$resultat.= "<li>"._T('spipbb:admin_plugin_requis_erreur')." CFG</li>";
		$res['CFG']=false;
	} else {
		$resultat.= "<li>"._T('spipbb:admin_plugin_requis_ok')." CFG</li>";
		$res['CFG']=true;
	}

	// Le plugin balise_session n'est plus necessaire depuis SPIP 1.945
	if (version_compare(substr($GLOBALS['spip_version'],0,5),'1.945','<')) {
		if (!is_array($tab_plugins_installes['BALISESESSION'])) {
			$resultat.= "<li>"._T('spipbb:admin_plugin_requis_erreur')." BALISESESSION</li>";
			$res['BALISESESSION']=true;
		} else {
			$resultat.= "<li>"._T('spipbb:admin_plugin_requis_ok')." BALISESESSION</li>";
			$res['BALISESESSION']=true;
		}
	}
	if ($resultat) $resultat="<ul>".$resultat."</ul>";
	return $res;
	//return $resultat;
} // spipbb_check_plugins_config



// ------------------------------------------------------------------------------
#
# infos supp. auteurs extra/table
#
// adapte de scoty gaf_install
// ------------------------------------------------------------------------------
function spipbb_config_infos_auteurs()
{
	#$options_sap = array('extra','table','autre');
	$options_sap = array('extra','table');
	
	$res  = debut_cadre_trait_couleur("",true,"",_L('Gestion champs auteurs suppl&eacute;mentaires'));	
	$res .= "<table width='100%' cellpadding='2' cellspacing='0' border='0' align='center' class='verdana2'>\n";

	# les champs, infos
	$res .= "<tr><td colspan='3'>"._L('Les champs n&eacute;cessaires &agrave; SpipBB')."</td></tr>\n";
	$res .= "<tr><td colspan='3'>";
	foreach($GLOBALS['champs_sap_spipbb'] as $champ => $def) {
		$res .= "<b>".$champ."</b>, ".$def['info']."<br />";
	}
	$res .= "</td></tr>\n";
	$res .= "<tr><td colspan='3'>&nbsp;</td></tr>\n";

	# mode d exploitation	
	$res .= "<tr><td colspan='3'>"._L('Quel support utiliser pour les champs suppl.');
	$res .= "</td></tr>\n";
	$res .= "<tr><td>"._L('Infos champs EXTRA ou autre table, table auteurs_profils.').
		"</td><td width='5%'></td><td width='25%'>\n";

	# choix du mode
	foreach($options_sap as $val) {
		$aff_checked = ($GLOBALS['spipbb']['support_auteurs']==$val) ? 'checked=\"checked\"' : '' ;
		$res .= "<input type='radio' name='support_auteurs' value='".$val."' ".$aff_checked." />&nbsp;"._L($val)."<br />";
	}
	$res .= "[spip_]<input type='text' name='table_support' value='".$GLOBALS['spipbb']['table_support']."' size='8' />";
	$res .= "</td></tr>\n";

	$res .= "<tr><td colspan='3'>&nbsp;</td></tr>\n";
	$res .= "</table>\n";
	$res .= "<div align='right'><input type='submit' value='"._T('valider')."' class='fondo' /></div>\n";

	$res .= fin_cadre_trait_couleur(true);
	return $res;
} // spipbb_config_infos_auteurs


// config des mots cles importee de configuration/mots

function spipbb_config_mots(){
	include_spip('inc/presentation');
	include_spip('inc/config');

	global $spip_lang_left;

	$articles_mots = $GLOBALS['meta']["articles_mots"];
	$mots_cles_forums = $GLOBALS['meta']["mots_cles_forums"];
	$forums_publics = $GLOBALS['meta']["forums_publics"];

	$res .= "<table border='0' cellspacing='1' cellpadding='3' width=\"100%\">"
	. "<tr><td class='verdana2'>"
	. _T('texte_mots_cles')."<br />\n"
	. _T('info_question_mots_cles')
	. "</td></tr>"
	. "<tr>"
	. "<td align='center' class='verdana2'>"
	. bouton_radio("articles_mots", "oui", _T('item_utiliser_mots_cles'), $articles_mots == "oui", "changeVisible(this.checked, 'mots-config', 'block', 'none');")
	. " &nbsp;"
	. bouton_radio("articles_mots", "non", _T('item_non_utiliser_mots_cles'), $articles_mots == "non", "changeVisible(this.checked, 'mots-config', 'none', 'block');");

	//	$res .= afficher_choix('articles_mots', $articles_mots,
	//		array('oui' => _T('item_utiliser_mots_cles'),
	//			'non' => _T('item_non_utiliser_mots_cles')), "<br />");
	$res .= "</td></tr></table>";

	if ($articles_mots != "non") $style = "display: block;";
	else $style = "display: none;";
	
	$res .= "<div id='mots-config' style='$style'>"
	. "<br />\n" ;
	if ($forums_publics != "non"){
		$res .= "<br />\n"
		. debut_cadre_relief("", true, "", _T('titre_mots_cles_dans_forum'))
		. "<table border='0' cellspacing='1' cellpadding='3' width=\"100%\">"
		. "<tr><td class='verdana2'>"
		. _T('texte_mots_cles_dans_forum')
		. "</td></tr>"
		. "<tr>"
		. "<td align='$spip_lang_left' class='verdana2'>"
		. afficher_choix('mots_cles_forums', $mots_cles_forums,
			array('oui' => _T('item_ajout_mots_cles'),
				'non' => _T('item_non_ajout_mots_cles')))
		. "</td></tr>"
		. "</table>"
		. fin_cadre_relief(true);
	}
	$res .= "</div>";	

	$res = debut_cadre_trait_couleur("mot-cle-24.gif", true, "", _T('info_mots_cles'))
	. ajax_action_post('configurer', 'mots', 'configuration','',$res) 
	. fin_cadre_trait_couleur(true);

	return ajax_action_greffe('configurer-mots', '', $res);

}

?>
