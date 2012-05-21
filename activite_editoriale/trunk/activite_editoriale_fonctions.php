<?php
function age_rubrique($date_str) {
	/*
	$date = new DateTime();
	$age = $date->diff(new DateTime($date_str));
	return $age->d;
	*/
	return intval((time() - strtotime($date_str)) / (60 * 60 * 24));
}

?>