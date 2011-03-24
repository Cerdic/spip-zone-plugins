<?php
function nuage_insert_head_css($flux) {
	$css = "<link rel='stylesheet' href='spip.php?page=nuage_style.css' type='text/css' media='all' />\n";
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
