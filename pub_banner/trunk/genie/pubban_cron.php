<?php
/**
 * @name 		Taches Cron
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.0 (06/2009)
 * @package		Pub Banner
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Action CRON
 * Verifie la validite de chaque pub (selon ses dates) et active ou desactive
 * si necessaire ...
 */
function genie_pubban_cron($time){
	include_spip('base/abstract_sql');
	$nb_modif = $nb_delete = $nb_errors = 0;
	$gdate = date("Y-m-d");

	$requete = sql_select("*", $GLOBALS['_PUBBAN_CONF']['table_pub'], "date_debut!='' OR date_fin!=''", '', '', '', '',_BDD_PUBBAN);
	while($tableau = spip_fetch_array($requete)) {
		$nom = $tableau['titre'];
		$actif = $tableau['statut'];
		$datedebut = (isset($tableau['date_debut']) AND strlen($tableau['date_debut'])) ? $tableau['date_debut'] : false;
		$datefin = (isset($tableau['date_fin']) AND strlen($tableau['date_fin'])) ? $tableau['date_fin'] : false;
		if($datedebut AND $actif == '1inactif' AND $datedebut >= $gdate) {
			sql_updateq($GLOBALS['_PUBBAN_CONF']['table_pub'], array('statut' => '2actif'), "titre='".$nom."'", '', _BDD_PUBBAN);
			$nb_modif++;
		}
		if($datefin AND $actif == '2actif' AND $datefin <= $gdate) {
			sql_updateq($GLOBALS['_PUBBAN_CONF']['table_pub'], array( 'statut' => '3obsolete', 'affichages_restant' => '0', 'clics_restant' => '0' ), "titre='".$nom."'", '', _BDD_PUBBAN);
			$nb_modif++;
		}
	}

	$requete2 = sql_select("id_pub, date_add", $GLOBALS['_PUBBAN_CONF']['table_pub'], "statut='0cree'", '', '', '', '',_BDD_PUBBAN);
	while($tableau = spip_fetch_array($requete2)) {
		list($date1, $rien) = explode(' ', $tableau['date_add']);
		list($y1, $m1, $d1) = explode('-', $date1);
		$time1 = mktime(0, 0, 0, $m1, $d1, $y1);
		list($y2, $m2, $d2) = explode('-', $gdate);
		$time2 = mktime(0, 0, 0, $m2, $d2, $y2);
		if( ($time2-$time1) >= 2592000){
			echo "ON y va pour id : ".$tableau['id_pub'];
			if( $ok = sql_delete($GLOBALS['_PUBBAN_CONF']['table_pub'], 'id_pub='.$tableau['id_pub'], _BDD_PUBBAN) )
				$nb_delete++;
			else $nb_errors++;
		}
	}

	$old_delete_stats = date('z', time() - 8726400);
	sql_delete($GLOBALS['_PUBBAN_CONF']['table_stats'], "jour < ('".$old_delete_stats."')", _BDD_PUBBAN);

	spip_log("PUB BANNER CRON - Mise a jour OK [ $nb_modif updates | $nb_delete deletes | $nb_errors erreurs ]");
}
?>