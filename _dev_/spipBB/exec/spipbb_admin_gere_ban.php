<?php
#---------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                             #
#  File    : exec/spipbb_admin_gere_ban                         #
#  Authors : chryjs 2007 et als                                 #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs      #
#  Contact : chryjs!@!free!.!fr                                 #
# [fr] Gestion du banissement                                   #
# [en] Manage ban lists                                         #
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

// ------------------------------------------------------------------------------
// ------------------------------------------------------------------------------
function exec_spipbb_admin_gere_ban() {
	# requis spip
	global $spip_display;

	# initialiser spipbb
	include_spip('inc/spipbb_init');

	#
	# affichage
	#
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('spipbb:admin_titre_page_'._request('exec')), "forum", "spipbb_admin", '');

	echo "<a name='haut_page'></a>";

	echo debut_gauche('',true);
	spipbb_menus_gauche(_request('exec'));

	echo debut_droite('',true);

	echo debut_cadre_formulaire('',true);

	echo gros_titre(_T('spipbb:admin_titre_page_'._request('exec')),'',false);

	echo debut_cadre_trait_couleur("racine-site-24.gif", true, "", _T('spipbb:admin_ban_user'));
	echo afficher_ban_user();
	echo afficher_unban_user();
	echo fin_cadre_trait_couleur(true);

	echo debut_cadre_trait_couleur("racine-site-24.gif", true, "", _T('spipbb:admin_ban_ip'));
	echo afficher_ban_ip();
	echo afficher_unban_ip();
	echo fin_cadre_trait_couleur(true);

	echo debut_cadre_trait_couleur("racine-site-24.gif", true, "", _T('spipbb:admin_ban_email'));
	echo afficher_ban_email();
	echo afficher_unban_email();
	echo fin_cadre_trait_couleur(true);

	echo fin_cadre_formulaire(true);

	# pied page exec
	bouton_retour_haut();

	echo fin_gauche(), fin_page();

} // exec_spipbb_inscrits

function afficher_ban_user() {
		// ban user bloc
	
	$res = "<div id='ban-user'>"
		. debut_cadre_relief("", true, "", "<label for='ban_user'>"._T('spipbb:admin_ban_user_info')."</label>")
		. "<select name='ban_user[]' multiple='multiple' size='5' class='forml'>";
	
	// Il ne faudra pas mettre dans cette liste les user deja bannis ! 
	$query=sql_query("SELECT id_auteur,login FROM spip_auteurs
					WHERE (statut='6forum' OR statut='nouveau' OR statut='5poubelle' )
					AND NOT EXISTS (SELECT ban_login FROM spip_ban_liste WHERE ban_login=login) ");
	if ( sql_count($query) ) {
		while ($row = sql_fetch($query)) {
			$res .= "<option value='".$row['id_auteur']."'>".$row['login']."</option>";
		}
	}
	else {
		$res .= "<option value='-1'>"._T('spipbb:admin_ban_user_none')."</option>";
	}
	
	$res .= "</select>"
		. fin_cadre_relief(true)
		. "</div>";
	
	$res = ajax_action_post('spipbb_admin_gere_ban', 'ban-user', 'ban','',$res) ;

	return ajax_action_greffe('ban-user','', $res); // creer action : "ban" a  la facon de action/configurer 
}

function afficher_unban_user() {
	$res = debut_cadre_relief("", true, "", "<label for='unban_user'>"._T('spipbb:admin_unban_user_info')."</label>")
		. "<select name='unban_user[]' multiple='multiple' size='5' class='forml'>";

	$query=sql_query("SELECT id_ban,ban_login FROM spip_ban_liste WHERE ban_login IS NOT NULL ");
	if ( sql_count($query) ) {
		while ($row = sql_fetch($query)) {
			$res .= "<option value='".$row['id_ban']."'>".$row['ban_login']."</option>";
		}
	}
	else {
		$res .= "<option value='-1'>"._T('spipbb:admin_ban_user_none')."</option>";
	}
	
	$res .= "</select>"
		. fin_cadre_relief(true);
	
	$res = ajax_action_post('spipbb_admin_gere_ban', 'unban-user', 'unban','',$res) ;
	echo ajax_action_greffe('unban-user','', $res); // creer action : "ban" a  la facon de action/configurer 
}

function afficher_ban_ip() {
		// ban user bloc
	
	$res = debut_cadre_relief("", true, "", "<label for='ban_ip'>"._T('spipbb:admin_ban_ip_info')."</label>")
		. "<input type='text' name='ban_ip' size='5' class='forml'>"
		. fin_cadre_relief(true);
	
	$res = ajax_action_post('spipbb_admin_gere_ban', 'ban-ip', 'ban','',$res) ;
	
	return ajax_action_greffe('ban-ip','', $res); // creer action : "ban" a  la facon de action/configurer 
}

function afficher_unban_ip() {
	$res = debut_cadre_relief("", true, "", "<label for='unban_ip'>"._T('spipbb:admin_unban_ip_info')."</label>")
		. "<select name='unban_ip[]' multiple='multiple' size='5' class='forml'>";

	$query=sql_query("SELECT id_ban,ban_ip FROM spip_ban_liste WHERE ban_ip IS NOT NULL ");
	if ( sql_count($query) ) {
		while ($row = sql_fetch($query)) {
			$res .= "<option value='".$row['id_ban']."'>".$row['ban_ip']."</option>";
		}
	}
	else {
		$res .= "<option value='-1'>"._T('spipbb:admin_ban_ip_none')."</option>";
	}
	
	$res .= "</select>"
		. fin_cadre_relief(true);
	
	$res = ajax_action_post('spipbb_admin_gere_ban', 'unban-ip', 'unban','',$res) ;
	echo ajax_action_greffe('unban-ip','', $res); // creer action : "ban" a  la facon de action/configurer 
}

function afficher_ban_email() {
		// ban user bloc
	
	$res = debut_cadre_relief("", true, "", "<label for='ban_email'>"._T('spipbb:admin_ban_email_info')."</label>")
		. "<input type='text' name='ban_email' size='5' class='forml'>"
		. fin_cadre_relief(true);
	
	$res = ajax_action_post('spipbb_admin_gere_ban', 'ban-email', 'ban','',$res) ;
	
	return ajax_action_greffe('ban-email','', $res); // creer action : "ban" a  la facon de action/configurer 
}

function afficher_unban_email() {
	$res = debut_cadre_relief("", true, "", "<label for='unban_email'>"._T('spipbb:admin_unban_email_info')."</label>")
		. "<select name='unban_email[]' multiple='multiple' size='5' class='forml'>";

	$query=sql_query("SELECT id_ban,ban_email FROM spip_ban_liste WHERE ban_email IS NOT NULL ");
	if ( sql_count($query) ) {
		while ($row = sql_fetch($query)) {
			$res .= "<option value='".$row['id_ban']."'>".$row['ban_email']."</option>";
		}
	}
	else {
		$res .= "<option value='-1'>"._T('spipbb:admin_ban_email_none')."</option>";
	}
	
	$res .= "</select>"
		. fin_cadre_relief(true);
	
	$res = ajax_action_post('spipbb_admin_gere_ban', 'unban-email', 'unban','',$res) ;
	echo ajax_action_greffe('unban-email','', $res); // creer action : "ban" a  la facon de action/configurer 
}



?>
