<?php

function fancybox_insert_head($flux){
	include_spip("inc/filtres");
	// Initialisation des valeurs de config
	$config = @unserialize($GLOBALS['meta']['fancybox']);
	if (!is_array($config))
		$config = array();
	$config = array_merge(array(
		'selecteur_galerie' => '#documents_portfolio .fancybox',
		'selecteur_commun' => '.fancybox',
		'padding' => '10',
		'imagescale' => 'true',
		'overlayshow' => 'true',
		'overlayopacity' => '0.3',
		'hideoncontentclick' => 'true',
		'selecteur_frame' => '.iframe',
		'framewidth' => '600',
		'frameheight' => '700'		
	), $config);
	// Insertion des librairies js
	$flux .='<script src="'.url_absolue(find_in_path('javascript/jquery.fancybox-1.2.1.js')).'" type="text/javascript"></script>';
	$flux .='<script src="'.url_absolue(find_in_path('javascript/fancybox.js')).'" type="text/javascript"></script>';
	// Init de la fancybox suivant la configuration
	$flux .='
<script type="text/javascript">/* <![CDATA[ */
var fb_selecteur_galerie="'.$config['selecteur_galerie'].'";
var fb_selecteur_commun="'.$config['selecteur_commun'].'";
var fb_selecteur_frame="'.$config['selecteur_frame'].'";
var fb_framewidth='.$config['framewidth'].';
var fb_frameheight='.$config['frameheight'].';
var fb_padding='.$config['padding'].';
var fb_imagescale='.$config['imagescale'].';
var fb_overlayshow='.$config['overlayshow'].';
var fb_overlayopacity='.$config['overlayopacity'].';
var fb_hideoncontentclick='.$config['hideoncontentclick'].';
if (window.jQuery)
(function($){if(typeof onAjaxLoad == "function") onAjaxLoad(fancy_init);
	$(fancy_init);
 })(jQuery);
/* ]]> */</script>';
	// Inclusion des styles propres a fancybox
	$flux .='<link rel="stylesheet" href="'.url_absolue(find_in_path('styles/jquery.fancybox.css')).'" type="text/css" />';

	return $flux;
}

?>
