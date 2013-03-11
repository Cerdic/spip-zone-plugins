<?php
// Reserve pour une utilisation future si besoin. Pour l'instant, pas de besoin en prive, donc pas de declaration dans plugin.xml
function rainette_header_prive($flux){
$flux .= '<link rel="stylesheet" href="'._DIR_PLUGIN_RAINETTE.'rainette.css" type="text/css" media="all" />';
return $flux;
}

// Insertion des css de Rainette
function rainette_insert_head_css($flux){
	static $done = false;
	if (!$done) {
		$done = true;
		$flux .= '<link rel="stylesheet" href="' . find_in_path('rainette.css') . '" type="text/css" media="all" />';
	}
	return $flux;
}
function rainette_insert_head($flux){
	$flux .= rainette_insert_head_css($flux); // au cas ou il n'est pas implemente
	return $flux;
}
?>
