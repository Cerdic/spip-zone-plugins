<?php

if (!defined('_DIR_PLUGIN_FULLCALENDAR')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_FULLCALENDAR',(_DIR_PLUGINS.end($p)));
}

function fullcalendar_insert_head($flux){
	# CSS
	$flux .= "<link rel='stylesheet' type='text/css' href='".find_in_path(_DIR_PLUGIN_FULLCALENDAR.'css/redmond/theme.css')."' />\n";
	$flux .= "<link rel='stylesheet' type='text/css' href='".find_in_path(_DIR_PLUGIN_FULLCALENDAR.'css/fullcalendar.css')."' />\n";
	$flux .= "<link rel='stylesheet' type='text/css' href='spip.php?page="._DIR_PLUGIN_FULLCALENDAR."css/calendar_style.css' />\n";
	# JQUERY UI
	$flux .= "<script type='text/javascript' src='".find_in_path(_DIR_PLUGIN_FULLCALENDAR.'js/jquery-ui-1.8.6.custom.min.js')."'></script>\n";
	# FULLCALENDAR
	$flux .= "<script type='text/javascript' src='".find_in_path(_DIR_PLUGIN_FULLCALENDAR.'js/fullcalendar.min.js')."'></script>\n";
	# Google Calendar
	$flux .= "<script type='text/javascript' src='".find_in_path(_DIR_PLUGIN_FULLCALENDAR.'js/gcal.js')."'></script>\n";
	return $flux;
}

function fullcalendar_header_prive($flux){
	# CSS et JQUERY UI pour la partie privée
	$flux .= "<link rel='stylesheet' type='text/css' href='".find_in_path(_DIR_PLUGIN_FULLCALENDAR.'css/jquery-ui.css')."' />\n";
	$flux .= "<link rel='stylesheet' type='text/css' href='".find_in_path(_DIR_PLUGIN_FULLCALENDAR.'css/jquery-ui-timepicker.css')."' />\n";
	$flux .= "<link rel='stylesheet' type='text/css' href='".find_in_path(_DIR_PLUGIN_FULLCALENDAR.'css/redmond/theme.css')."' />\n";
	
	$flux .= "<script type='text/javascript' src='".find_in_path(_DIR_PLUGIN_FULLCALENDAR.'js/jquery.ui.core.js')."'></script>\n";
	$flux .= "<script type='text/javascript' src='".find_in_path(_DIR_PLUGIN_FULLCALENDAR.'js/jquery.ui.all.js')."'></script>\n";
	$flux .= "<script type='text/javascript' src='".find_in_path(_DIR_PLUGIN_FULLCALENDAR.'js/jquery.ui.timepicker.js')."'></script>\n";
	return $flux;
}

?>
