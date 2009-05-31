<?php
#---------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                             #
#  File    : exec/spipbb_admin_etat - base admin menu           #
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
if (defined("_ADMIN_ETAT")) return; else define("_ADMIN_ETAT", true);
include_spip('inc/spipbb_common');
spipbb_log('included',2,__FILE__);


// ------------------------------------------------------------------------------
function exec_spipbb_admin_etat() {

	# requis spip
	#

	# initialiser spipbb
	include_spip('inc/spipbb_init');

	# requis de cet exec

	#
	# affichage
	#
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('spipbb:admin_titre_page_'._request('exec')),  "forum", "spipbb_admin");
	echo "<a name='haut_page'></a>";

	echo debut_gauche('',true);
		spipbb_menus_gauche(_request('exec'),$id_salon);

	echo creer_colonne_droite('', true);

	echo debut_droite('',true);

	echo spipbb_recap_config();


	# pied page exec
	bouton_retour_haut();

	echo fin_gauche(), fin_page();
} // exec_spipbb_admin_etat


// ------------------------------------------------------------------------------
// [fr] Affiche les statistiques generales du forum SpipBB
// ------------------------------------------------------------------------------
function spipbb_recap_config()
{
	global $couleur_claire, $couleur_foncee;

	$total_posts = get_db_stat('postcount');
	$total_users = get_db_stat('usercount');
	$start_date = strtotime(get_db_stat('oldestpost'));
	$boarddays = ( time() - $start_date ); // format de start_date ?
	$posts_per_day = sprintf("%.8f", $total_posts / $boarddays);
	$users_per_day = sprintf("%.8f", $total_users / $boarddays);
	$forum_age = ( date("Y",$boarddays)-1970 ) . "/" . date("n",$boarddays);

	if($posts_per_day > $total_posts)
	{
		$posts_per_day = $total_posts;
	}

	if($users_per_day > $total_users)
	{
		$users_per_day = $total_users;
	}

	include_spip('inc/filtres.php');
	$version=$GLOBALS['spipbb']['version'];
	if ($svn_revision = version_svn_courante(_DIR_PLUGIN_SPIPBB)) {
		$version .= ' ' . (($svn_revision < 0) ? 'SVN ':'')
		. "[<a href='http://zone.spip.org/trac/spip-zone/changeset/"
		. abs($svn_revision) . "' onclick=\"window.open(this.href); return false;\">"
		. abs($svn_revision) . "</a>]";
	}

	// Qui est en ligne

	$r = sql_fetsel("count(*) AS total",'spip_auteurs', 'en_ligne>= DATE_SUB(NOW(), INTERVAL 5 MINUTE )');
	$total_online = $r['total'];

	// chryjs :  7/9/8 recuperer_fond est maintenant dans inc/utils
	if (!function_exists('recuperer_fond')) include_spip('inc/utils');

	$contexte = array(
				'couleur_foncee'=>$couleur_foncee,
				'couleur_claire' => $couleur_claire,
				'total_posts' => $total_posts,
				'posts_per_day' => $posts_per_day,
				'total_users' => $total_users,
				'users_per_day' => $users_per_day,
				'posts_per_day' => $posts_per_day,
				'start_date' => normaliser_date($start_date),
				'forum_age' => $forum_age,
				'total_online' => $total_online,
				'spipbb_version' => $version
			);
	$res = recuperer_fond("prive/spipbb_admin_etat",$contexte) ;

	return $res;
} // spipbb_recap_config

// ------------------------------------------------------------------------------
// [fr] Fourni des statistiques sur la base de donnees
// ------------------------------------------------------------------------------
function get_db_stat($mode)
{
	switch( $mode )
	{
		case 'usercount':
			//$query="SELECT COUNT(id_auteur) AS total FROM spip_auteurs"; // peut etre rajouter where enligne<>NULL
			$result = sql_select('COUNT(id_auteur) AS total','spip_auteurs');
			break;

		case 'newestuser':
			//$query="SELECT id_auteur, nom FROM spip_auteurs ORDER BY id_auteur DESC LIMIT 0,1";
			$result = sql_select('id_auteur, nom','spip_auteurs','','',array('id_auteur DESC'),'0,1');
			break;

		case 'postcount':
			//$query="SELECT COUNT(*) AS total FROM spip_forum";
			$result = sql_select('COUNT(*) AS total','spip_forum');
			break;

		case 'oldestpost':
			//$query="SELECT date_heure AS date FROM spip_forum ORDER BY date_heure ASC LIMIT 0,1";
			$result = sql_select('date_heure AS date','spip_forum','','',array('date_heure ASC'),'0,1');
			break;
	}

	if ($result) {
		$row=sql_fetch($result);
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
