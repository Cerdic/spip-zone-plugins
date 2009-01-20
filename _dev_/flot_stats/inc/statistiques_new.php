<?php
function rendre_date ($date) {
	$d = explode("-", $date);
	$date_ex = mktime(0, 0, 0, $d[1], $d[2], $d[0]);
	$date_ex = intval($date_ex);
	$date_ex = $date_ex * 1000;

	return $date_ex;
}
?>