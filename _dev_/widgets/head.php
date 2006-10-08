<?php

function Widgets_insert_head($flux){
	$js = '<script src="'.find_in_path('widgets.js').'" type="text/javascript"></script>';
	$js .= '<style>img.widget-edit {visibility: hidden;} .widget:hover img.widget-edit, .widget-hover img.widget-edit {visibility: visible;} .widget:hover, .widget-hover {background-color: #e3eeee;}</style>';
	return $flux.$js;
}

?>