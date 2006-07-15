<?php

	function Lightbox_v1_insert_head($flux){
		$flux .= 	'<script type="text/javascript" src="'
			.find_in_path('lightbox.js').'"></script>';
		$flux .= 	"<link rel='stylesheet' href='"
			.find_in_path('lightbox.css')."' type='text/css' media='all' />\n";
		$flux .='<script type="text/javascript"><!--
			var loadingImage = "'.url_absolue(find_in_path('loading.gif')).'";
			var closeButton = "'.url_absolue(find_in_path('close.gif')).'"; // -->
			</script>';
		return $flux;
	}

?>