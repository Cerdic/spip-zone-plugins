<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function jewishc_date_jewish ($date, $language = 'he') {
	include_spip('inc/NativeCalendar');
	$jcal = NativeCalendar::factory('Jewish');
	$jcal->settings(array('language' => ($language == 'he' ? CAL_LANG_NATIVE : CAL_LANG_FOREIGN)));
	$jewdate = $jcal->getLongDate($date);
	return $jewdate;
}
?>