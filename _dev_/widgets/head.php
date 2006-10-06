<?php

function Widgets_insert_head($flux){
	$js = '<script src="'.find_in_path('widgets.js').'" type="text/javascript"></script>';
	return $flux.$js;
}

?>