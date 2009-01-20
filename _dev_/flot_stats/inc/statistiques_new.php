<?php
function rendre_date ($date) {
	$d = explode("-", $date);
	$date_ex = mktime(0, 0, 0, $d[1], $d[2], $d[0]);
	$date_ex = intval($date_ex);
	$date_ex = $date_ex * 1000;

	return $date_ex;
}
function courbe_visites () {
	$select = sql_select("*", "spip_visites");
	$nstats = sql_countsel('spip_visites');
	echo '[';
	$coun = 1;
	while ($ele=sql_fetch($select)){
		$date_vi = $ele['date'];
		$nvis = $ele['visites'];
		echo '['.rendre_date($date_vi).', '.$nvis.']';
		if ($coun<$nstats) {
			$coun++;
			echo ",";
		}
	}
	echo ']';
	
	return true;
}
function courbe_moyenne () {
	$select = sql_select("*", "spip_visites");
	$nstats = sql_countsel('spip_visites');
	echo '[';
	$coun = 1;
	$mtotal = 0;
	while ($ele=sql_fetch($select)){
		$mdate = $ele['date'];
		$mnvis = intval($ele['visites']);
		$mtotal = $mnvis + $mtotal;
		$mtotal = intval($mtotal);
		$moy = $mtotal / $coun;
		echo '['.rendre_date($mdate).', '.$moy.']';
		if ($coun<$nstats) {
			$coun++;
			echo ",";
		}
	}
	echo ']';
	
	return true;		
	}
?>