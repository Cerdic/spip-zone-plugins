<?php
function tablos_insert_head_css($flux){
	$flux .= "<link rel='stylesheet' type='text/css' media='all' href='" . find_in_path('css/tablos.css') . "' />\n";
	return $flux;
}
?>