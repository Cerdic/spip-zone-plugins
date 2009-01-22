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
	$coun = 1;
	$lacourbe = '[';
	while ($ele=sql_fetch($select)){
		$date_vi = $ele['date'];
		$nvis = $ele['visites'];
		$var_s = '['.rendre_date($date_vi).', '.$nvis.']';
		$lacourbe = $lacourbe.$var_s;
		if ($coun<$nstats) {
			$coun++;
			$lacourbe = $lacourbe.',';
		}
	}
	$lacourbe = $lacourbe.']';
	
	return $lacourbe;
}
function courbe_moyenne () {
	$select = sql_select("*", "spip_visites");
	$nstats = sql_countsel('spip_visites');
	$lacourbe = '[';
	$coun = 1;
	$mtotal = 0;
	while ($ele=sql_fetch($select)){
		$mdate = $ele['date'];
		$mnvis = intval($ele['visites']);
		$mtotal = $mnvis + $mtotal;
		$mtotal = intval($mtotal);
		$moy = $mtotal / $coun;
		$moy = intval(round($moy));
		$var_s = '['.rendre_date($mdate).', '.$moy.']';
		$lacourbe = $lacourbe.$var_s;
		if ($coun<$nstats) {
			$coun++;
			$lacourbe = $lacourbe.',';
		}
	}
	$lacourbe = $lacourbe.']';
	
	return $lacourbe;		
	}
function show_points () {
	$nstats = sql_countsel('spip_visites'); 
	if ($nstats > 365) { 
		$show = "false";
	} 
	else { 
		$show = "true";
	}
	
	return $show;
}
?>