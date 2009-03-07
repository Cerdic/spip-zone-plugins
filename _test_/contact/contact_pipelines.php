<?php

function contact_insert_head($flux){
	$style_public = generer_url_public('style_public_plugin_contact.css');
	$flux .= "\n<link rel='stylesheet' href='{$style_public}' type='text/css' media='projection, screen, tv' />";
	
	return $flux;
}
?>