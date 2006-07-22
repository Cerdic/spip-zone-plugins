<?php

	function Splickr_insert_head($flux){
	//$flux .= 	'<script src="'.find_in_path('jquery.js').'" type="text/javascript"></script>';	

		$flux .= 	'<script type="text/javascript" src="'
			.generer_url_public('prototype_js').'"></script>';
		$flux .= 	'<script type="text/javascript" src="'
			.generer_url_public('rico_js').'"></script>';	
		return $flux;
	}

?>