<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function inc_reservation_reference_dist($id_reservation){
	if ($date = sql_getfetsel('date', 'spip_reservations', 'id_reservation=' . intval($id_reservation))) {
		$t = strtotime($date);
	}
	else {
		$t = $_SERVER['REQUEST_TIME'];
	}

	// format YYYYMMDDNNNNNN
	$reference = date('Ymd', $t) . str_pad(intval($id_reservation), 6, '0', STR_PAD_LEFT);

	return $reference;
}
