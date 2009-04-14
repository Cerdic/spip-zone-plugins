<?php

function fancybox_insert_head($flux){
	include_spip("inc/filtres");
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

	$flux .='<script src="'.url_absolue(find_in_path('javascript/jquery.fancybox-1.2.1.js')).'" type="text/javascript"></script>';
	
	$flux .='
<script type="text/javascript">/* <![CDATA[ */
(function($){
	$(function(){
		$("a[type=\'image/jpeg\'],a[type=\'image/png\'],a[type=\'image/gif\']")
			.addClass("fancybox")
			.attr("onclick","")
			.fancybox();
		$("'.$config['selecteur_galerie'].'").attr("rel","galerie-portfolio");
		$("'.$config['selecteur_commun'].'")
			.fancybox({
				"padding": '.$config['padding'].',
				"imageScale": '.$config['imagescale'].',
				"overlayShow": '.$config['overlayshow'].',
				"overlayOpacity": '.$config['overlayopacity'].',
				"hideOnContentClick": '.$config['hideoncontentclick'].'
			});
		$("'.$config['selecteur_frame'].'")
			.fancybox({
				"frameWidth": '.$config['framewidth'].',
				"frameHeight": '.$config['frameheight'].',
				"padding": '.$config['padding'].',
				"imageScale": '.$config['imagescale'].',
				"overlayShow": '.$config['overlayshow'].',
				"overlayOpacity": '.$config['overlayopacity'].',
				"hideOnContentClick": '.$config['hideoncontentclick'].'
			});
	});
})(jQuery);
/* ]]> */</script>';

	$flux .='<link rel="stylesheet" href="'.url_absolue(find_in_path('styles/jquery.fancybox.css')).'" type="text/css" />';

	return $flux;
}

?>
