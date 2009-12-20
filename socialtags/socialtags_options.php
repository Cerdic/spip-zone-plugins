<?php

# definir la source de trafic et l'indiquer dans un cookie
if (is_array($cfg = @unserialize($GLOBALS['meta']['socialtags']))
AND $cfg['ifreferer']) {
	define('SOCIALTAGS_SOURCES', 'facebook|yahoo|google');
	# $dom = domaine referent
	$ref = isset($_GET['HTTP_REFERER'])
		? $_GET['HTTP_REFERER']
		: (isset($_SERVER['HTTP_REFERER'])
			? $_SERVER['HTTP_REFERER']
			: '');
	list(,,$s_ref) = explode('/', $ref);
	if (preg_match(','.SOCIALTAGS_SOURCES.',i', $s_ref, $r)) {
		$coo = 'social_'.$r[0];
		setcookie($coo, $_COOKIE[$coo] = 1);
	}
}

