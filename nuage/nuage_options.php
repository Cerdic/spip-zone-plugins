<?php
function nuage_insert_head_css($flux) {
	$css = "<link rel='stylesheet' href='".find_in_path("css/nuage.css")."' type='text/css' />\n";
	static $done = false;
	if (!$done) {
		$done = true;
		$flux .= "\n".$css;
	}
	return $flux;
}
function nuage_insert_head($flux){
	$flux = nuage_insert_head_css($flux); // au cas ou il n'est pas implemente
	return $flux;
}
?>
