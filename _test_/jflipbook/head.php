<?php
// Samuel Bocharel "PoongalOO" : sam@poongaloo.org
// Equipe EVA-WEB : http://eva-web.edres74.net
// jQuery jFlip : http://www.jquery.info/spip.php?article78
// corners = true for top corners, false for bottom ones
// scale = 	"noresize" images are not resized,
//				"fit" big images are resized to be completely visible
// 			"fill" all images are resized keeping aspect ratio to fill the canvas

	function album_jflip_insert_head($flux){
		
		$largeur = lire_config('album_jflip/largeur');
		$hauteur = lire_config('album_jflip/hauteur');
		$couleur = lire_config('album_jflip/couleur');
		$corners = lire_config('album_jflip/corners');
		$scale = lire_config('album_jflip/scale');	
		$flux = '<link rel="stylesheet" TYPE="text/css" href="'._DIR_PLUGIN_ALBUM_JFLIP.'css/jflip_style.css"></link>';
		$flux .= '<!--[if IE]><script type="text/javascript" src="'._DIR_PLUGIN_ALBUM_JFLIP.'javascript/excanvasX.js"></script><![endif]-->';
		$flux .= '<script type="text/javascript" src="'._DIR_PLUGIN_ALBUM_JFLIP.'javascript/jquery-1.2.6.min.js"></script>';
		$flux .= '<script type="text/javascript" src="'._DIR_PLUGIN_ALBUM_JFLIP.'javascript/jquery.jflip.js"></script>';
		$flux .= '<script type="text/javascript">
	  					(function($){    
	      				$(function(){
	      					$("#g1").jFlip('.$largeur.','.$hauteur.',{background:"'.$couleur.'",cornersTop:'.$corners.',scale:"'.$scale.'"});
	      				});
	  					})(jQuery);';
		$flux .= '</script>';
		$flux .= '<style type="text/css">
	  					.flip_gallery {margin:10px}
	  				</style>';
	  				
		return $flux;
	}
?>