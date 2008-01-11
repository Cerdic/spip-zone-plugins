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
if (!defined("_INC_SPIPBB_COMMON")) include_spip('inc/spipbb_common');
spipbb_log('included',2,__FILE__);

//----------------------------------------------------------------------------
// prendre en compte un fichier de visite
//----------------------------------------------------------------------------
function compte_fichier_visite_forum($fichier, &$visites_f) {

	$content = array();
	if (lire_fichier($fichier, $content)) {
		spipbb_log("[$fichier]:".$content,1,"compte_fichier_visite_forum");
		$content = @unserialize($content);
	}
	if (!is_array($content)) return;
	spipbb_log("Depart:".join(",",$visites_f,1,"compte_fichier_visite_forum"));

	foreach ($content as $source => $num) {
		list($log_type, $log_id_num)
			= preg_split(",\t,", $source, 3);

		// S'il s'agit d'une visite de forum, noter ses visites
		if ($log_type == 'forum'
		AND $id_forum = intval($log_id_num)) {
			$visites_f[$id_forum]=$visites_f[$id_forum] + intval($num);
		}
	}
	spipbb_log("Sortie:".join(",",$visites_f,1,"compte_fichier_visite_forum"));
}

//----------------------------------------------------------------------------
// Calcul des visites forum par forum
//----------------------------------------------------------------------------
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
			spipbb_log("traite la session $item",1,"calculer_visites_forums");
			compte_fichier_visite_forum($item, $visites_f);
			spip_unlink($item);
			if (file_exists($item)) spipbb_log("Erreur suppression impossible $item",1,"calculer_visites_forums");
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
			spipbb_log("sql_insertq[$n]:".$id_forum,1,"calculer_visites_forums");
			sql_insertq('spip_visites_forums',
					array('id_forum' => $id_forum,
					      'visites' => $n,
					      'date' => $date));
			} else $ar[$n][] = $id_forum;
		}
		foreach ($ar as $n => $liste) {
			$tous = sql_in('id_forum', $liste);
			spipbb_log("sql_update[$n]:".$tous,1,"calculer_visites_forums");
			sql_update('spip_visites_forums',
				array('visites' => "visites+$n"),
				   "date='$date' AND $tous");
		}
	}

	// S'il reste des fichiers a manger, le signaler pour reexecution rapide
	if ($compteur==0) {
		spipbb_log("il reste des visites a traiter...",1,"calculer_visites_forums");
		return -$t;
	}
} // calculer_visites_forums

//----------------------------------------------------------------------------
// Fonction cron pour calcul des visites pour les threads
//----------------------------------------------------------------------------
function genie_statvisites($time) {
	spipbb_log("DEBUT:".$time,1,"genie_statvisites");
	$spipbb_meta = @unserialize($GLOBALS['meta']['spipbb']);

	if (!is_array($spipbb_meta) OR ($spipbb_meta['configure']!='oui')) {
		spipbb_log("END: Non Configure",1,"genie_statvisites");
		return true;
	}

	$encore = calculer_visites_forums($time);

	// Si ce n'est pas fini on redonne la meme date au fichier .lock
	// pour etre prioritaire lors du cron suivant
	if ($encore)
		return (0 - $time);

	return true;
} // genie_statvisites

?>
