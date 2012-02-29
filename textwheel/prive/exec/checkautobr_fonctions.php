<?php

function autobr_reperage($t) {
	$tm = autobr_marquer($t);
	$p = propre($tm);
	if (preg_match_all('/CHECKAUTOBR_(\d+)_<span class=\'autobr\'>/', $p, $r, PREG_PATTERN_ORDER)) {
		$l = $r[1];
	} else
		$l = array();

	if ($l) {
		foreach($l as $n) {
			$tm = str_replace('CHECKAUTOBR_'.$n.'_'."\n", ' ', $tm);
		}
	}
	$tm = preg_replace('/CHECKAUTOBR_\d+_/', '', $tm);
	$tm = echappe_retour($tm, 'CHECK');

	return $tm;
}


function autobr_marquer($t) {
	$t = echappe_html($t, 'CHECK', true /*notransform*/);

	$t = preg_split("/\r?\n/", $t);

	foreach($t as $k=>$l) {
		if (strlen($t[$k])) {
			$t[$k] = $t[$k].' CHECKAUTOBR_'.$k.'_';
		}
	}

	return join("\n", $t);
}