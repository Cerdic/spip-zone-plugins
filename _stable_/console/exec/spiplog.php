<?php

include_spip('inc/console');
include_spip("console_options"); // Pour Spip 1.9.2 ?

function exec_spiplog(){
	global $connect_statut;
	global $connect_toutes_rubriques;
  
	if ($connect_statut != "0minirezo" OR !$connect_toutes_rubriques) {
		return "";
	}
	$logfile = _request('logfile');

	if (!$logfile) {$logfile = _FILE_LOG;}

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
