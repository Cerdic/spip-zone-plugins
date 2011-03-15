<?php

function fullcalendar_jqueryui_forcer($scripts){
	$scripts[] = "jquery.ui.core";
	$scripts[] = "jquery.ui.all";
	$scripts[] = "jquery.ui.datepicker";
	$scripts[] = "jquery.effects.scale";
	$scripts[] = "jquery.ui.dialog";
	return $scripts;
}

function fullcalendar_insert_head_css($flux){
	static $done = false;
	if (!$done) {
		$done = true;
	$flux .= "<link rel='stylesheet' type='text/css' href='".find_in_path('plugins/fullcalendar/css/redmond/theme.css')."' />";
	$flux .= "<link rel='stylesheet' type='text/css' href='".find_in_path('plugins/fullcalendar/css/fullcalendar.css')."' />";
	$flux .= "<link rel='stylesheet' type='text/css' href='".url_absolue(generer_url_public('css_fullcalendar'))."' media='all' />";
	}
	return $flux;
}

function fullcalendar_insert_head($flux){
	$flux .= "<script type='text/javascript' src='".find_in_path('plugins/fullcalendar/js/fullcalendar.js')."'></script>";
	$flux .= "<script type='text/javascript' src='".find_in_path('plugins/fullcalendar/js/gcal.js')."'></script>";
	$flux .= fullcalendar_insert_head_css($flux); 
	return $flux;
}

function fullcalendar_header_prive($flux){
	$flux .= "<script type='text/javascript' src='".find_in_path('plugins/fullcalendar/js/jquery.ui.timepicker.js')."'></script>";
	$flux .= "<link rel='stylesheet' type='text/css' href='".find_in_path('plugins/fullcalendar/css/jquery-ui.css')."' />";
	$flux .= "<link rel='stylesheet' type='text/css' href='".find_in_path('plugins/fullcalendar/css/jquery-ui-timepicker.css')."' />";
	$flux .= "<link rel='stylesheet' type='text/css' href='".find_in_path('plugins/fullcalendar/css/redmond/theme.css')."' />";
	return $flux;
}

?>
