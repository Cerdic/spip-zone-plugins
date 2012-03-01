<?php

function autobr_correction($t) {
	static $nl = null;
	if(!$nl)
		$nl = preg_quote(trim(str_replace('A', '', PtoBR(propre("A\nA")))),',');


	$tm = autobr_marquer($t);

	$p = propre($tm);

	if (preg_match_all(", CHECKAUTOBR_(\d+)_$nl,", $p, $r, PREG_PATTERN_ORDER)) {
		$l = $r[1];
	} else {
		$l = array();
	}

	if ($l) {
		foreach($l as $n) {
			$tm = str_replace(' CHECKAUTOBR_'.$n.'_'."\n", ' ', $tm);
		}
	}
	$tm = preg_replace('/ CHECKAUTOBR_\d+_/', '', $tm);
	$tm = echappe_retour($tm, 'CHECK');

	#echo nl2br(htmlspecialchars($tm));

	return $tm;
}


function autobr_marquer($t) {
	$t = echappe_html($t, 'CHECK', true /*notransform*/);

	while ($t !== ($t1 = preg_replace("/(<[^>\r\n]*)\r?\n/S", '\1 CHECKNEWLINEINTAG ', $t)))
		$t = $t1;

	$t = preg_split("/\r?\n/", $t);

	foreach($t as $k=>$l) {
		if (strlen($t[$k] AND strlen($t[$k+1])))
		{
			$t[$k] = $t[$k].' CHECKAUTOBR_'.$k.'_';
		}
	}

	$t = join("\n", $t);

#echo "<pre>".(htmlspecialchars($t))."</pre>";
	$t = str_replace(' CHECKNEWLINEINTAG ', "\n", $t);

	return $t;
}


function autobr_simplifier($t) {
	$t = preg_replace("/ CHECKAUTOBR_\d+_\n(\n|-|_ |[]]|\|)/S", '', $t);

	return $t;
}