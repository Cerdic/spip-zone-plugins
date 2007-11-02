<?php
#---------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                             #
#  File    : exec/spipbb_admin - base admin menu                #
#  Authors : Chryjs, 2007                                       #
#  Contact : chryjs¡@!free¡.!fr                                 #
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

include_spip("inc/spipbb"); // spipbb_admin_gauche + divers

// ------------------------------------------------------------------------------
function exec_spipbb_admin()
{
	global $connect_statut, $connect_toutes_rubriques;

	// [fr] initialisations
	// [en] initialize
	if (!isset($GLOBALS['spipbb'])) spipbb_init_metas() ;

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('spipbb:titre_spipbb'), "configuration", 'spipbb');

	echo gros_titre(_T('spipbb:titre_spipbb'),'',false) ;

//	echo "hierarchie ?";
	echo debut_grand_cadre(true);
	echo afficher_hierarchie($GLOBALS['spipbb']['spipbb_id_rubrique']);
	echo fin_grand_cadre(true);

	echo debut_gauche('',true);
	echo debut_boite_info(true);
	echo  _T('spipbb:titre_spipbb');
	echo fin_boite_info(true);
	echo spipbb_admin_gauche($GLOBALS['spipbb']['spipbb_id_rubrique'],'spipbb_admin');
	echo creer_colonne_droite('', true);
	echo debut_droite('',true);

	echo debut_cadre_formulaire('',true);
	echo spipbb_recap_config();
	echo fin_cadre_formulaire(true);

	echo fin_gauche(), fin_page();
} // exec_spipbb_admin

// ------------------------------------------------------------------------------
// [fr] Affiche les statistiques generales du forum SpipBB
// ------------------------------------------------------------------------------
function spipbb_recap_config() {
global $couleur_claire, $couleur_foncee;
// Welcome
// Statistiques du Forum

	$total_posts = get_db_stat('postcount');
	$total_users = get_db_stat('usercount');
	$start_date = strtotime(get_db_stat('oldestpost'));
	$boarddays = ( time() - $start_date ); // format de start_date ?
	$posts_per_day = sprintf("%.8f", $total_posts / $boarddays);
	$users_per_day = sprintf("%.8f", $total_users / $boarddays);
	$forum_age = ( date("Y",$boarddays)-1970 ) . " ans " . date("n",$boarddays). " mois " . date("j",$boarddays)." jours ";

	if($posts_per_day > $total_posts)
	{
		$posts_per_day = $total_posts;
	}

	if($users_per_day > $total_users)
	{
		$users_per_day = $total_users;
	}
/*
	$res  = "<table cellpadding='0' cellspacing='0' border='0' width='100%'>" ;
	$res .= "<thead><tr bgcolor='$couleur_foncee'><td>"._T('spipbb:statistique')."</td><td>"._T('spipbb:valeur')."</td></tr></thead>" ;
	$res .= "<tr bgcolor='$couleur_claire'><td>" . _T('spipbb:total_posts') . "</td><td>" . $total_posts ."</td></tr>" ;
	$res .= "<tr bgcolor='#fff'><td>" . _T('spipbb:average_posts') . "</td><td>" . $posts_per_day ."</td></tr>" ;
	$res .= "<tr bgcolor='$couleur_claire'><td>" . _T('spipbb:total_users') . "</td><td>" . $total_users ."</td></tr>" ;
	$res .= "<tr bgcolor='#fff'><td>" . _T('spipbb:average_users') . "</td><td>" . $users_per_day ."</td></tr>" ;
	$res .= "<tr bgcolor='$couleur_claire'><td>" . _T('spipbb:date_ouverture') . "</td><td>" . date("j n y",$start_date) ."</td></tr>" ;
	$res .= "<tr bgcolor='#fff'><td>" . _T('spipbb:age_forum') . "</td><td>" . $forum_age ."</td></tr>" ;
*/
// Qui est en ligne

	$r = spip_query("SELECT COUNT(*) AS total FROM spip_auteurs WHERE en_ligne>= DATE_SUB(NOW(), INTERVAL 5 MINUTE )");
	$o = spip_fetch_array($r); // fetch all ?
	$total_online = count($o);
/*
	$res .= "<tr bgcolor='$couleur_claire'><td>" . _T('spipbb:total_users_online') . "</td><td>" . $total_online ."</td></tr>" ;
// Check available spipbb updates
	$res .= "<tr bgcolor='#fff'><td>" . _T('spipbb:spipbb_release') . "</td><td>" . $GLOBALS['spipbb_version'] . "</td></tr>" ;

	$res .= "</table>" ;
*/

	if (!function_exists('recuperer_fond')) include_spip('public/assembler');
	$securiser_action = charger_fonction('securiser_action', 'inc');
	// on sait que cette fonction est dans le fichier associe

	$contexte = array( 
				'couleur_foncee'=>$couleur_foncee,
				'couleur_claire' => $couleur_claire,
				'total_posts' => $total_posts,
				'posts_per_day' => $posts_per_day,
				'total_users' => $total_users,
				'users_per_day' => $users_per_day,
				'posts_per_day' => $posts_per_day,
				'start_date' => date("j n y",$start_date),
				'forum_age' => $forum_age,
				'total_online' => $total_online,
				'spipbb_version' => $GLOBALS['spipbb']['version']
			);
	$res = recuperer_fond("prive/spipbb_admin_index",$contexte) ;

	return $res;

}

// ------------------------------------------------------------------------------
// [fr] Fourni des statistiques sur la base de donnees
// ------------------------------------------------------------------------------
function get_db_stat($mode)
{
	switch( $mode )
	{
		case 'usercount':
			$query="SELECT COUNT(id_auteur) AS total FROM spip_auteurs"; // peut etre rajouter where enligne<>NULL
			break;

		case 'newestuser':
			$query="SELECT id_auteur, nom FROM spip_auteurs ORDER BY id_auteur DESC LIMIT 0,1";
			break;

		case 'postcount':
			$query="SELECT COUNT(*) AS total FROM spip_forum";
			break;

		case 'oldestpost':
			$query="SELECT date_heure AS date FROM spip_forum ORDER BY date_heure ASC LIMIT 0,1";
			break;
	}

	if ($result=spip_query($query)) {
		$row=spip_fetch_array($result);
	}

	switch( $mode )
	{
		case 'usercount':
		case 'postcount':
			return $row['total'];
			break;

		case 'newestuser':
			return $row;
			break;

		case 'oldestpost':
			return $row['date'];
			break;

	}

	return false;
}

?>
