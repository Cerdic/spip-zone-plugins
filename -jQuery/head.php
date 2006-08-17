<?php

function JQuery_insert_head($flux){
		if (_request('jqdb')!==NULL)
			$flux .= '<script src="'.find_in_path('jquery.lite.213.js').'" type="text/javascript"></script>';
		else
			$flux .= '<script src="'.find_in_path('jquery.pack.213.js').'" type="text/javascript"></script>';
		
		return $flux;
	}

?>
