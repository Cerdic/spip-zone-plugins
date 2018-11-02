<?php

// a passer en fichier inc/
function inc_termes_recherches_to_array($referers){
	foreach($referers as $r){
		$termes_bruts[] = strtolower(
							preg_replace("`\"|'`","",urldecode(
								preg_replace("`&.*$`","",
									preg_replace("`^.*recherche=`","",$r)
										))));
	}
	
	foreach($termes_bruts as $t){
		$termes[$t]++ ;
	}
	
	arsort($termes);
	
	return $termes ;
}

function regexp_moteurs(){
	include_spip("inc/referenceurs");
	$e = stats_load_engines() ;
	foreach($e as $m)
		if($m[0] != "IP" AND $m[0] != "(email)")
			$moteurs[] = str_replace("!","",strtolower($m[0])) ; // yahoo!
	
	return implode("|",array_unique($moteurs)) ;
}

function url2domaine($url){
	$u = parse_url($url);
	$u = $u["host"] ;
	$u = preg_replace(",(co|com|qc)\.\w{2},","tld",$u);
	$u = explode(".",$u);
	$d = array_pop($u);
	$d = array_pop($u);
	return $d;
}

function compter_referers($ref){
	foreach($ref as $url){
		$u = parse_url($url);
		$cle = preg_replace(",^https?://,","",$url);
		$cle = preg_replace(",/$,","",$cle);
		// nettoyage en plus
		$cle = preg_replace(",\?amp=\d+$,","",$cle); // twitter
		$cle = preg_replace(",\?fbclid=.+$,","",$cle); // fb
		
		$r[$cle]["url"] = $url ;
		$r[$cle]["visites"]++;
		$r[$cle]["path"] = preg_replace(',^/,','',$u["path"]);
		
	}
	
	return $r ;
}