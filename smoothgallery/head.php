<?php

function Smoothgallery_insert_head($flux){
	$flux .= '
<link rel="stylesheet" href="'.url_absolue(find_in_path('css/jd.gallery.css')).'" type="text/css" media="screen" />
';
	$flux .= '
<style type="text/css">
	#myGallery{
		width: '.lire_config("smoothgallery/largeur").'px !important;
		height: '.lire_config("smoothgallery/hauteur").'px !important;
	}
</style>
';
	return $flux;
}
?>
