<?php

	function Splickr_insert_head($flux){
	$flux .= 	'<script type="text/javascript" src="'.find_in_path('splickrbox.js').'"></script>';

	return $flux;

	}

?>