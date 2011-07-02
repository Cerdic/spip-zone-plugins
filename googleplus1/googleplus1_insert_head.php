<?php

function googleplus1_insert_head($flux){
	
	/*
	$googleplus1_taille = lire_config('googleplus1/taille');

	if (!$googleplus1_taille || $googleplus1_taille == '_') {
		$flux .= '
		<script type="text/javascript" src="https://apis.google.com/js/plusone.js">
		  {lang: \'fr\'}
		</script>';
	}
	else {
		$flux .= '
		<script type="text/javascript" src="https://apis.google.com/js/plusone.js">
		  {lang: \'fr\'}
		</script>';
	return $flux;
	}
*/
	$flux .= '
		<script type="text/javascript" src="https://apis.google.com/js/plusone.js">
		</script>';
	return $flux;
}
?>
