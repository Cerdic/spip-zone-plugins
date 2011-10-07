<?php
/*
 * Plugin Job Queue
 * (c) 2009 Cedric&Fil
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_purger_queue_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$securiser_action();

	if (autoriser('purger','queue')){
		include_spip('inc/queue');
		queue_purger();
	}

}

?>