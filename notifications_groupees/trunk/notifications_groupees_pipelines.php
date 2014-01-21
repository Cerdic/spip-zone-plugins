<?php
/*
 * Plugin Notifications groupees
 * (c) 2013
 * Distribue sous licence GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function notifications_groupees_taches_generales_cron($taches_generales){
	// au moins un evenement et un visiteur
	// en notifications groupees
	$evt = isset($GLOBALS['meta']['notifications_groupees_evenements']);
	$vst = sql_fetsel("id_forum","spip_forum","notifications_groupees=1") OR sql_fetsel("id_auteur","spip_auteurs","notifications_groupees=1");
	if ($evt AND $vst)
		$taches_generales['notifications_groupees'] = 3600*$GLOBALS['meta']['notifications_groupees_periode'];
	return $taches_generales;
}