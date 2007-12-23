<?php
#------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                          #
#  File    : genie/statvisites - stats de visites des forums #
#  Authors : Chryjs, 2007 et als                             #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs   #
#  Contact : chryjs!@!free!.!fr                              #
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
if (!defined("_ECRIRE_INC_VERSION")) return;
spip_log(__FILE__.' : included','spipbb');

if (version_compare(substr($GLOBALS['spip_version_code'],0,5),'1.927','<')) {
	include_spip('inc/spipbb_192'); // SPIP 1.9.2
}
//
// prendre en compte un fichier de visite
//
function compte_fichier_visite_forum($fichier, &$visites_f) {

	$content = array();
	if (lire_fichier($fichier, $content)) {
		spip_log(__FILE__." compte_fichier_visite_forum[$fichier]:".$content,"spipbb");
		$content = @unserialize($content);
	}
	if (!is_array($content)) return;

	foreach ($content as $source => $num) {
		list($log_type, $log_id_num)
			= preg_split(",\t,", $source, 3);

		// S'il s'agit d'une visite de forum, noter ses visites
		if ($log_type == 'forum'
		AND $id_forum = intval($log_id_num)) {
			$visites_f[$id_forum]=$visites_f[$id_forum] + intval($num);
		}
	}
}

function calculer_visites_forums($t) {
	include_spip('base/abstract_sql');

	// Initialisations
	$visites_f = array(); # tableau des visites des forums

	// charger un certain nombre de fichiers de visites,
	// et faire les calculs correspondants

	// Traiter jusqu'a 100 sessions datant d'au moins 30 minutes
	$sessions = preg_files(sous_repertoire(_DIR_TMP, 'spipbb-visites'));

	$compteur = 100;
	$date_init = time()-5*60; // pour l'instant on a positionne a toutes les 5 minutes pour les tests
	foreach ($sessions as $item) {
		if (@filemtime($item) < $date_init) {
			spip_log(__FILE__." traite la session $item","spipbb");
			compte_fichier_visite_forum($item, $visites_f);
			spip_unlink($item);
			if (--$compteur <= 0)
				break;
		}
		#else spip_log("$item pas vieux");
	}

	if (!$visites_f) return;

	$date = date("Y-m-d", time() - 1800);

	// les visites des forums
	if ($visites_f) {
		$ar = array();	# tableau num -> liste des forums ayant num visites
		foreach($visites_f as $id_forum => $n) {
		  if (!sql_countsel('spip_visites_forums',
				 "id_forum=$id_forum AND date='$date'")){
			spip_log(__FILE__." sql_insertq[$n]:".$id_forum,"spipbb");
			sql_insertq('spip_visites_forums',
					array('id_forum' => $id_forum,
					      'visites' => $n,
					      'date' => $date));
			} else $ar[$n][] = $id_forum;
		}
		foreach ($ar as $n => $liste) {
			$tous = sql_in('id_forum', $liste);
			spip_log(__FILE__." sql_update[$n]:".$tous,"spipbb");
			sql_update('spip_visites_forums',
				array('visites' => "visites+$n"),
				   "date='$date' AND $tous");
		}
	}

	// S'il reste des fichiers a manger, le signaler pour reexecution rapide
	if ($compteur==0) {
		spip_log(__FILE__." il reste des visites a traiter...","spipbb");
		return -$t;
	}
} // calculer_visites_forums

function genie_statvisites($time) {
	spip_log(__FILE__." genie_statvisites : ".$time,'spipbb');

	$encore = calculer_visites_forums($time);

	// Si ce n'est pas fini on redonne la meme date au fichier .lock
	// pour etre prioritaire lors du cron suivant
	if ($encore)
		return (0 - $time);

	return true;
} // genie_statvisites

?>
