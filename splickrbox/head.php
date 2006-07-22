<?php

	function Splickr_insert_head($flux){
	$flux .= 	'<script type="text/javascript" src="'
			.generer_url_public('prototype_js').'"></script>';
		$flux .= 	'<script type="text/javascript" src="'
			.generer_url_public('rico_js').'"></script>';	
		return $flux;
	}

?>