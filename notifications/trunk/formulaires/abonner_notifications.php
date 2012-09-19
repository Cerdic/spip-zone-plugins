<?php
/*
 * Plugin Notifications
 * (c) 2009-2012 SPIP
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_abonner_notifications_charger_dist($email, $key, $id_auteur=null){

	$valeurs = array(
		"id_threads" => array(),
		"_all_threads" => array(),
		"_email" => $email,
	);

	// trouver tous les threads
	$rows = sql_allfetsel("id_thread,notification","spip_forum","notification_email=".sql_quote($email)
			  ." OR (notification_email=".sql_quote('')." AND email_auteur=".sql_quote($email).")");

	if (!$rows) return false;

	$valeurs['_all_threads'] = array_map('reset',$rows);
	$valeurs['_all_threads'] = array_unique($valeurs['_all_threads']);

	foreach ($rows as $row){
		if ($row['notification'])
			$valeurs['id_threads'][] = $row['id_thread'];
	}

	return $valeurs;
}
