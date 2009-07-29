<?php

function liens_sortants_ouvrants_insert_head($flux) {
	$flux .= '<script  src="'._DIR_PLUGIN_LIENS_SORTANTS_OUVRANTS.'liens_sortants_ouvrants.js" type="text/javascript"></script>';
	return $flux;
}

?>