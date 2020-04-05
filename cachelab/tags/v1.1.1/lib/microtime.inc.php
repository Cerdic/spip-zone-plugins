<?php
//
// Chronométrage des appels php

function eleg_microtime($time, $unit = '') {
	$time = round($time, 6);

	switch ($unit) {
	case 'ms':
			return ($time*1000).' ms';
	case 'us':
	case 'µs':
			return ($time*1000000).' µs';
	case 's':
			nobreak;
			return $time.'s';
	case '':
			return $time;
	}
	spip_log('mauvais parametre unit pour eleg_microtime', 'erreur_microtime');
	return '';
}
// microtime_do reçoit des commandes et l'unité d'affichage us, ms ou s
// begin : reset
// now : microtime depuis init
// end : now + begin
// last : microtime depuis le dernier appel, sauf now
function microtime_do($command, $unit = '') {
static $u_init = 0;
static $s_init = 0;
static $u_last = 0;
static $s_last = 0;
	switch ($command) {
	case 'begin':
	case 'init':
		list ($u_last, $s_last) = list ($u_init, $s_init) = explode(' ', microtime());
			return 0;

	case 'now':
		list ($u_last, $s_last) = explode(' ', microtime());
			return eleg_microtime(($s_last - $s_init)+($u_last - $u_init), $unit);

	case 'end':
		list ($u_last, $s_last) = explode(' ', microtime());
		$res = ($s_last - $s_init)+($u_last - $u_init);
		$u_init = $u_last;
		$s_init = $s_last;
			return eleg_microtime($res, $unit);

	case 'last':
		list ($u_now, $s_now) = explode(' ', microtime());
		$res = ($s_now - $s_last)+($u_now - $u_last);
		$u_last = $u_now;
		$s_last = $s_now;
			return eleg_microtime($res, $unit);

	default:
			die("unknown microtime_do command « $command »");
	}
};
