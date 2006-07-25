<?php

function JQuery_insert_head($flux){

		$flux .= '<script src="'.find_in_path('jquery.js').'" type="text/javascript"></script>';
		
		return $flux;
	}

?>