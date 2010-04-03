<?php

function fancybox_insert_head($flux){
	include_spip("inc/filtres");
	// Initialisation des valeurs de config
	$config = @unserialize($GLOBALS['meta']['fancybox']);
	if (!is_array($config))
		$config = array();
	$config = array_merge(array(
		'selecteur_galerie' => '#documents_portfolio .fancybox, .documents_portfolio .fancybox',
		'selecteur_commun' => '.fancybox',
		'padding' => '10',
		'imagescale' => 'true',
		'overlayshow' => 'true',
		'overlayopacity' => '0.3',
		'enableescapebutton' => 'true',
		'showclosebutton' => 'true',
		'hideonoverlayclick' => 'true',
		'hideoncontentclick' => 'true',
		'centeronscroll' => 'true',
		'selecteur_frame' => '.iframe',
		'communwidth' => '425',
		'communheight' => '355',
		'framewidth' => '600',
		'frameheight' => '700'	
	), $config);
	// Insertion des librairies js
	$flux .='<script src="'.url_absolue(find_in_path('spip20/javascript/jquery.fancybox-1.2.6.js')).'" type="text/javascript"></script>';
	$flux .='<script src="'.url_absolue(find_in_path('javascript/fancybox.js')).'" type="text/javascript"></script>';
	// Init de la fancybox suivant la configuration
	$flux .='
<script type="text/javascript">/* <![CDATA[ */
// fontion callback lancee a l affichage de la box
var fancyonshow=function() {
	showlongdesc(this);
	hideembed();
}
// fontion callback lancee a la fermeture de la box
var fancyonclose=function() {
	showembed();
}
var fb_selecteur_galerie="'.$config['selecteur_galerie'].'";
var fb_selecteur_commun="'.$config['selecteur_commun'].'";
var fb_selecteur_frame="'.$config['selecteur_frame'].'";
var fb_commun_options = {
	"padding": '.$config['padding'].',
	"imageScale": '.$config['imagescale'].',
	"overlayShow": '.$config['overlayshow'].',
	"overlayOpacity": '.$config['overlayopacity'].',
	"enableEscapeButton": '.$config['enableescapebutton'].',
	"showCloseButton": '.$config['showclosebutton'].',
	"hideOnOverlayClick": '.$config['hideonoverlayclick'].',
	"hideOnContentClick": '.$config['hideoncontentclick'].',
	"centerOnScroll": '.$config['centeronscroll'].',
	"frameWidth": '.$config['communwidth'].',
	"frameHeight": '.$config['communheight'].',
	"callbackOnShow": fancyonshow,
	"callbackOnClose": fancyonclose
};
var fb_frame_options = {
	"frameWidth": '.$config['framewidth'].',
	"frameHeight": '.$config['frameheight'].',
	"padding": '.$config['padding'].',
	"imageScale": '.$config['imagescale'].',
	"overlayShow": '.$config['overlayshow'].',
	"overlayOpacity": '.$config['overlayopacity'].',
	"centerOnScroll": '.$config['centeronscroll'].',
	"hideOnContentClick": '.$config['hideoncontentclick'].'
};
if (window.jQuery)
(function($){if(typeof onAjaxLoad == "function") onAjaxLoad(fancy_init);
	$(fancy_init);
 })(jQuery);
/* ]]> */</script>';
	// Inclusion des styles propres a fancybox
	$flux .='<link rel="stylesheet" href="'.url_absolue(find_in_path('styles/jquery.fancybox.css')).'" type="text/css" media="all" />';

	return $flux;
}

?>
