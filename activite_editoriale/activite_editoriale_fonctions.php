<?php
function age_rubrique($date_str) {
	/*
	$date = new DateTime();
	$age = $date->diff(new DateTime($date_str));
	return $age->d;
	*/
	return intval((time() - strtotime($date_str)) / (60 * 60 * 24));
}

function mysqldate($date_str)
{
	$date=split('/',$date_str);
	
	return $date[2].'-'.$date[1].'-'.$date[0];
}
?>