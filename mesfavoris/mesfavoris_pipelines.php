<?php

function mesfavoris_insert_head_css($flux){
	$config = "";
	if (isset($GLOBALS['meta']['mesfavoris']))
		$config = unserialize($GLOBALS['meta']['mesfavoris']);
	if ($config AND isset($config['style_formulaire']))
		$config = $config['style_formulaire'];

	if (!$config OR !$css=find_in_path("mesfavoris-$config.css"))
		$css = find_in_path("mesfavoris-32.css");
	$flux .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";
	return $flux;
}

?>