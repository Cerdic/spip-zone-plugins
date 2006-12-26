<?php

function exec_test_validateur_interne(){
	$url = _request('urlAVerif');
	$url = str_replace("&amp;","&",$url);
	include_spip('inc/distant');
	$page = recuperer_page($url);
	$transformer_xml=charger_fonction('valider_xml', 'inc');
	$transformer_xml($page, false);
	var_dump($GLOBALS['xhtml_error']);
}
?>