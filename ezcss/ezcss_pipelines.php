<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function ezcss_insert_head_css($flux) {
	$flux .= '<link rel="stylesheet" type="text/css" href="plugins/auto/ezcss/ez-plug.css" media="all" />';
	
	return $flux;
}

?>
