<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip('inc/mailsubscribinglists');

/**
 * CRON de synchro des listes
 *
 * @param $t
 * @return int
 */
function genie_mailsubscribers_update_segments_dist($t) {

	$time_out = $_SERVER['REQUEST_TIME'] + 20;
	$update_segments = array();
	if (isset($GLOBALS['meta']['mailsubscriptions_update_segments'])) {
		$update_segments = unserialize($GLOBALS['meta']['mailsubscriptions_update_segments']);
		if (!$update_segments){
			$update_segments = array();
		}
	}

	$n = sql_countsel('spip_mailsubscriptions','actualise_segments=1');
	spip_log("genie_mailsubscribers_update_segments : $n restants","mailsubscribers");

	$ids = array_keys($update_segments);
	foreach($ids as $id_mailsubscribinglist){

		do {
			$subs = sql_allfetsel('*','spip_mailsubscriptions','id_segment=0 AND actualise_segments=1 AND id_mailsubscribinglist='.intval($id_mailsubscribinglist),'','','0,100');
			foreach ($subs as $sub){
				//spip_log("genie_mailsubscribers_update_segments : ".$sub['id_mailsubscriber'].'-'.$sub['id_mailsubscribinglist'],"mailsubscribers");

				mailsubscribers_actualise_mailsubscribinglist_segments($sub['id_mailsubscriber'],$sub['id_mailsubscribinglist']);
				sql_updateq('spip_mailsubscriptions', array('actualise_segments' => 0), 'id_mailsubscriber='.intval($sub['id_mailsubscriber']) .' AND ' . 'id_mailsubscribinglist='.intval($sub['id_mailsubscribinglist']) );

				if (time()>$time_out){
					return -($t-90);
				}
			}

		} while ($subs);

		// ici on efface ce mailsubscribinglist dans l'update, en reprenant depuis les metas
		// et on sort si c'est fini
		if (mailsubscribers_remove_from_meta_update($id_mailsubscribinglist)){
			return 1;
		}

		if (time()>$time_out){
			return -($t-90);
		}
	}

	// securite : on vide toute la meta
	mailsubscribers_remove_from_meta_update('all');
	return 1;
}


/**
 * Enlever une entree/toutes les entrees de la meta mailsubscriptions_update_segments
 * @param string|int $remove
 * @return bool
 *   true si fini, false si encore
 */
function mailsubscribers_remove_from_meta_update($remove='all'){
	// ici on efface ce mailsubscribinglist dans l'update, en reprenant depuis les metas
	lire_metas();
	$update_segments = unserialize($GLOBALS['meta']['mailsubscriptions_update_segments']);

	if ($update_segments and isset($update_segments[$remove])) {
		unset($update_segments[$remove]);
	}

	if (!$update_segments or $remove==='all') {
		effacer_meta('mailsubscriptions_update_segments');
		sql_updateq('spip_mailsubscriptions',array('actualise_segments'=>0));
		return true;
	}

	ecrire_meta('mailsubscriptions_update_segments', serialize($update_segments));
	return false;
}