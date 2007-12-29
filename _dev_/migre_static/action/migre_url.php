<?php

#---------------------------------------------------------------#
#  Plugin  : migre_static - Licence : GPL                       #
#  File    : action/migre_url                                   #
#  Authors : Chryjs, 2007                                       #
#  Contact : chryjs¡@!free¡.!fr                                 #
# [fr] Cette page sert a revisiter tous les liens internes      #
# [fr] pour voir s'il est possible de les actualiser            #
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

include_spip("inc/migre"); // [fr] Charge les fonctions de migre_static [en] Loads migre_static functions
include_spip("base/abstract_sql");
include_spip('inc/rubriques');
include_spip('inc/charsets');
include_spip('inc/minipres');
include_spip("inc/presentation");

if (!function_exists('spip_insert_id')) include_spip('inc/vieilles_defs');

global $migre_meta;
$migre_meta = $GLOBALS['migrestatic'];

ini_set('max_execution_time',600); // pas toujours possible mais requis


// ------------------------------------------------------------------------------
// [fr] Affiche la page complete spip privee avec le formulaire
// [en] Provides the full spip private space form
// ------------------------------------------------------------------------------
function action_migre_url()
{
	//$securiser_action = charger_fonction('securiser_action', 'inc');
	//$arg = $securiser_action();

	$id_rubrique = _request($id_rubrique);
	$step = _request('etape');
	$go_back = generer_url_ecrire("naviguer","id_rubrique=$id_rubrique");
	$link_back  = icone(_T('icone_retour'), $go_back, "rubrique-12.gif", "rien.gif", ' ',false);
	// presentation pour 192
	$corps = $link_back."<div style='width:100%;background: #FFF; font-size:90%;'>" ;

	$corps.= "<b>"._T('migrestatic:mis_a_jour').migre_up_url($id_rubrique)."</b>";
	$corps.= $link_back;

	// presentation pour 1.9.2
	$style=" style='
	font-size: 0.9em; color: #2e3436; background: #babdb6;
	' ";
	$corps.="</div>";

	echo minipres(_T("migrestatic:titre_migre_action_etape")." 3",$corps,$style);

} // exec_migre_formulaire

// On pourrait utiliser id_rubrique pour limiter les articles mis a jour (pas les sources de liens)
function migre_up_url($id_rubrique="") {
	$cpt=0;
	$sql="SELECT id_article,url_site FROM spip_articles WHERE url_site<>\"\" AND url_site IS NOT NULL ORDER BY id_article";
	$result=spip_query($sql);
	$table_uri=array();
	while ($row=spip_fetch_array($result)) {
		$uri=$row['url_site'];
		//$uri=preg_replace(";(.*?)(\?.*);","\\1",$row['url_site']); // voir aussi les index/default .htm[l] .php[x] .asp
//		$uri=preg_replace(";(.*?)(index\..{3,4});","\\1",$uri);
//echo "$uri <br>\n";
		$table_uri[$row['id_article']]=$uri;
	}

	if (empty($table_uri) or !is_array($table_uri)) return;
	//copie de la liste
	reset($table_uri);
	while (list($id,)=each($table_uri)) $table_art[]=$id;
	//construction des remplacements
	reset($table_uri);
	//while (list($uri,)=each($table_uri)) $table_search[]=";\-\>$uri(index\..{3,4})*(\?*.*?)\];";
	//while (list(,$uri)=each($table_uri)) $table_search[]=";\-\>$uri(\?*.*?)\];";
	while (list(,$uri)=each($table_uri)) $table_search[]=";\-\>$uri\];";
	reset($table_uri);
	while (list($id,)=each($table_uri)) $table_replace[]="->art$id]"; // conversion des URIs dans migre_html_to_spip

//print_r($table_search);
	reset($table_art);
	while (list(,$id)=each($table_art)) {
		$sql = "SELECT texte FROM spip_articles WHERE id_article=$id";
		$result=spip_query($sql);
		if ($row = spip_fetch_array($result)) {
			$row['texte']=stripslashes($row['texte']);
			$texte = preg_replace($table_search,$table_replace,$row['texte']); 
			if ($texte!=$row['texte']) { // on ne fait d'update que s'il y a eu modification(s)
				$texte=addslashes($texte);
				$sqlup="UPDATE spip_articles SET texte='$texte' WHERE id_article=$id";
				$res=spip_query($sqlup);
				$cpt++;
				//echo $sql."<br>\n";
			} // if modif
		} // if $row
	} // while
	return $cpt;
}

?>
