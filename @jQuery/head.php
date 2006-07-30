<?php

function JQuery_insert_head($flux){
		if (_request('jqdb')!==NULL)
			$flux .= '<script src="'.find_in_path('jquery_uncompressed.js').'" type="text/javascript"></script>';
		else
			$flux .= '<script src="'.find_in_path('jquery.js').'" type="text/javascript"></script>';
		$flux .= '<script type="text/javascript">
		$(document).ready(function(){$(".bloc_invisible").hide();})</script>';
		
		return $flux;
	}

?>