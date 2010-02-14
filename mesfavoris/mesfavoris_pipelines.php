<?php

function mesfavoris_insert_head($flux){
	$css = generer_url_public('mesfavoris.css');
	$flux .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";
	return $flux;
}

?>
