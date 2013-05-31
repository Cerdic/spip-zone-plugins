<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function sidr_config($public=null){
	include_spip("inc/filtres");
	$config = @unserialize($GLOBALS['meta']['sidr']);
	if (!is_array($config))
		$config = array();
	$config = array_merge(array(
		'selecteur' => '#menu',
		'skin' => 'dark',
	), $config);

	
	return $config;	
}

function sidr_insert_head_css($flux){
	static $done = false;
	if (!$done) {
		$done = true;
		$config = sidr_config();
		if ($f = find_in_path("css/jquery.sidr.".$config['skin'].'.css'))
			$flux .= '<link rel="stylesheet" href="'.direction_css($f).'" type="text/css" media="all" />';
	}
	return $flux;
}


function sidr_timestamp($fichier){
	if ($m = filemtime($fichier))
		return "$fichier?$m";
	return $fichier;
}

function sidr_insert_head($flux){
	// Possibilite de faire sa propre insertion de sidr dans son squelette
	if (defined("_SIDR_PERSO")) return $flux;
	
	$config = sidr_config();

	$flux = sidr_insert_head_css($flux); // au cas ou il n'est pas implemente

	$flux .='<script src="'.sidr_timestamp(find_in_path('javascript/jquery.sidr.js')).'" type="text/javascript"></script>'."\n";

	$flux .='<script type="text/javascript">/* <![CDATA[ */
jQuery(document).ready(function() {
	jQuery("#responsive-menu-button").sidr({
	name: "sidr-main",
	source: "'.$config['selecteur'].'"
	});
});
/* ]]> */</script>'."\n";
	
	return $flux;
}

?>