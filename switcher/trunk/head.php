<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function switcher_insert_head_css($flux){
	static $done = false;
	if (!$done) { 
		$done = true; 
		$flux .='
	<style type="text/css" media="print">
/* <![CDATA[ */
	#plugin_switcher { display: none; }
/* ]]> */
	</style>
';
	}
	return $flux;
}
function switcher_insert_head($flux){ 
	$flux .= switcher_insert_head_css($flux); // au cas ou il n'est pas implemente 
	return $flux;
}