<?php

include_spip('inc/console');

function exec_spiplog(){
	global $connect_statut;
	global $connect_toutes_rubriques;
  
	if ($connect_statut != "0minirezo" OR !$connect_toutes_rubriques) {
		return "";
	}
	$logfile = _request('logfile');
	
	if (!in_array($logfile,array('spip','mysql'))){
		$logfile = 'spip';
	}
	
	$out = console_lit_log($logfile);

	$format = _request('format');
	if($format=='text') {
		echo $out;
		return;
	}
	header("Content-type: text/xml; charset=Unicode");
	echo '<' . '?xml version="1.0" encoding="Unicode" ?'.'>';
	echo "<log><$logfile>";
	echo $out;
	echo "</$logfile></log>";
}

?>
