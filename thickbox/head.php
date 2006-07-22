<?php

function ThickBox_insert_head($flux){

	$flux .='
<style type="text/css" media="all">
@import "'.find_in_path('thickbox.css').'";
</style>

<script src="'.find_in_path('jquery.js').'" type="text/javascript"></script>
<script type="text/javascript"><!--
TB_chemin_animation = "'.find_in_path('circle_animation.gif').'";
// --></script>
<script src="'.find_in_path('thickbox.js').'" type="text/javascript"></script>
';
/*
$flux .= 	'<script type="text/javascript" src="'
			.generer_url_public('lightbox_js').'"></script>';
		$flux .= 	"<link rel='stylesheet' href='"
			.generer_url_public('lightbox_css')."' type='text/css' media='all' />\n";
*/
		return $flux;
	}

?>