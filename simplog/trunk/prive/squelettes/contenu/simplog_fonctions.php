<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function simplog_phraser_log($ligne) {
	$tableau = array();

	if ($l = trim($ligne) AND $l != '[-- rotate --]') {
		preg_match('#^(.*:\d\d)\s(.*)\s\(pid\s(.*)\)\s:([bipru]*):([^:]*):\s(.*)$#i', $ligne, $matches);
		$tableau['date'] = date('Y-m-d H:i:s', strtotime($matches[1]));
		$tableau['ip'] = trim($matches[2]);
		$tableau['pid'] = trim($matches[3]);
		$tableau['hit'] = _T('simplog:info_hit_'. strtolower(trim($matches[4])));
		$tableau['gravite'] = strtolower(trim($matches[5]));
		$tableau['texte'] = trim($matches[6]);
	}

	return $tableau;
}

?>
