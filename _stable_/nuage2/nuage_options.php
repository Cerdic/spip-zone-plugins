<?php

function nuage_insert_head($flux) {
	$css .= "<link rel='stylesheet' href='spip.php?page=nuage_style.css' type='text/css' media='all' />\n";
	if (strpos($flux,'<head')!==FALSE)
		return preg_replace('/(<head[^>]*>)/i', "\n\$1".$css, $flux, 1);
	else 
		return $flux.$css;
}
?>
