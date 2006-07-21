<?php

	function Lightbox_v1_insert_head($flux){
		$flux .= 	'<script type="text/javascript" src="'
			.generer_url_public('lightbox_js').'"></script>';
		$flux .= 	"<link rel='stylesheet' href='"
			.generer_url_public('lightbox_css')."' type='text/css' media='all' />\n";
		return $flux;
	}

?>