<?php

function googleplus1_insert_head($flux){

	$googleplus1_lang = lire_config('langue_site');
	$flux .= '
		<script type="text/javascript" src="https://apis.google.com/js/plusone.js">
		{lang: \''.$googleplus1_lang.'\'}
		</script>';
	return $flux;
}
?>
