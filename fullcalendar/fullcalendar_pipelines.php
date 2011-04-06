<?php

/**********
 * PUBLIC *
 **********/

function fullcalendar_insert_head_css($flux_ = '', $prive = false){
	static $done = false;
	if($done) return $flux_;
	$done = true;
	$flux  = "<!-- FULLCALENDAR INSERT HEAD CSS START -->
";
	$flux .= "<link rel='stylesheet' type='text/css' href='".find_in_path('css/redmond/theme.css')."' />
";
	$flux .= "<link rel='stylesheet' type='text/css' href='".find_in_path('css/fullcalendar.css')."' />
";
	$flux .= "<link rel='stylesheet' type='text/css' href='".url_absolue(generer_url_public('css_fullcalendar'))."' media='all' />
";
	$flux .= "<!-- FULLCALENDAR INSERT HEAD CSS END -->
";
	return $flux_ . $flux;
}

function fullcalendar_insert_head($flux_){
	$flux  = "<!-- FULLCALENDAR INSERT HEAD START -->
";
	$flux .= "<script type='text/javascript' src='".find_in_path('js/fullcalendar.js')."'></script>
";
	$flux .= "<script type='text/javascript' src='".find_in_path('js/gcal.js')."'></script>
";
	$flux .= "<!-- FULLCALENDAR INSERT HEAD END -->
";
	return $flux_ . fullcalendar_insert_head_css() . $flux;
}

/*********
 * PRIVE *
 *********/

function fullcalendar_header_prive($flux_){
	$flux  = "<!-- FULLCALENDAR HEADER PRIVE START -->
";
	$flux .= "<script type='text/javascript' src='".url_absolue(find_in_path('lib/jquery-ui-1.8.9/ui/jquery-ui.js'))."'></script>
";
	$flux .= "<script type='text/javascript' src='".url_absolue(find_in_path('lib/jquery-ui-1.8.9/ui/jquery.ui.core.js'))."'></script>
";
	$flux .= "<script type='text/javascript' src='".url_absolue(find_in_path('lib/jquery-ui-1.8.9/ui/jquery.ui.datepicker.js'))."'></script>
";
	$flux .= "<script type='text/javascript' src='".url_absolue(find_in_path('lib/jquery-ui-1.8.9/ui/jquery.effects.scale.js'))."'></script>
";
	$flux .= "<script type='text/javascript' src='".url_absolue(find_in_path('js/jquery.ui.timepicker.js'))."'></script>
";
	$flux .= "<script type='text/javascript' src='".url_absolue(find_in_path('js/fullcalendar.js'))."'></script>
";
	$flux .= "<script type='text/javascript' src='".url_absolue(find_in_path('js/gcal.js'))."'></script>
";
	$flux .= "<link rel='stylesheet' type='text/css' href='".url_absolue(find_in_path('css/jquery-ui.css'))."' />
";
	$flux .= "<link rel='stylesheet' type='text/css' href='".url_absolue(find_in_path('css/jquery-ui-timepicker.css'))."' />
";
	$flux .= "<link rel='stylesheet' type='text/css' href='".url_absolue(find_in_path('css/redmond/theme.css'))."' />
";
	$flux .= "<link rel='stylesheet' type='text/css' href='".url_absolue(find_in_path('css/fullcalendar.css'))."' />
";
	$flux .= "<!-- FULLCALENDAR HEADER PRIVE FIN -->
";
	return $flux_ . $flux;
}

/*************
 * JQUERY UI *
 *************/

function fullcalendar_jqueryui_forcer($scripts){
	$scripts[] = "jquery.ui.core";
	$scripts[] = "jquery.ui.all";
	#$scripts[] = "jquery.ui.timepicker.js";
	$scripts[] = "jquery.ui.datepicker";
	$scripts[] = "jquery.effects.scale";
	$scripts[] = "jquery.ui.dialog";
	return $scripts;
}

?>
