<?php
// Samuel Bocharel "PoongalOO" : sam@poongaloo.org
// Equipe EVA-WEB : http://eva-web.edres74.net
// jQuery jFlip : http://www.jquery.info/spip.php?article78
// corners = true for top corners, false for bottom ones
// scale = 	"noresize" images are not resized,
//				"fit" big images are resized to be completely visible
// 			"fill" all images are resized keeping aspect ratio to fill the canvas

function jflipbook_insert_head($flux){
	
	$largeur = lire_config('jflipbook/largeur');
	$hauteur = lire_config('jflipbook/hauteur');
	$couleur = lire_config('jflipbook/couleur');
	$corners = lire_config('jflipbook/corners');
	$scale = lire_config('jflipbook/scale');
	$flux = '<link rel="stylesheet" TYPE="text/css" href="'._DIR_PLUGIN_JFLIPBOOK.'css/jflip_style.css"></link>';
	$flux .= '<!--[if IE]><script type="text/javascript" src="'._DIR_PLUGIN_JFLIPBOOK.'javascript/excanvasX.js"></script><![endif]-->';
	$flux .= '<script type="text/javascript" src="prive/javascript/jquery.js"></script>';
	$flux .= '<script type="text/javascript" src="'._DIR_PLUGIN_JFLIPBOOK.'javascript/jquery.jflip-0.3.min.js"></script>';
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