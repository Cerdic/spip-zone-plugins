<?php

/**
 * Convertir une date au format d'un datepicker
 * vers le format DATETIME Mysql
 *
 * @param string $date format jj.mm.aaaa
 *
 * @return bool|string format datetime mysql
 */
function date_picker_to_date($date) {
	if (preg_match("/([0-9]{2}).([0-9]{2}).([0-9]{4})/", $date)) {
		return preg_replace("#([0-9]{2}).([0-9]{2}).([0-9]{4})#", "\\3-\\2-\\1 00:00:00", $date);
	} else {
		return false;
	}

}