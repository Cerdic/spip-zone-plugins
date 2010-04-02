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
		'margin' => '20',
		'opacity' => 'false',
		'modal' => 'false',
		'cyclic' => 'false',
		'scrolling' => 'auto',
		'width' => '560',
		'height' => '340',
		'autoscale' => 'true',
		'autodimensions' => 'true',
		'centeronscroll' => 'true',
		'hideonoverlayclick' => 'true',
		'hideoncontentclick' => 'true',
		'overlayshow' => 'true',
		'overlayopacity' => '0.3',
		'overlaycolor' => '#666',
		'titleshow' => 'true',
		'titleposition' => 'outside',
		'transitionin' => 'fade',
		'transitionout' => 'fade',
		'speedin' => '300',
		'speedout' => '300',
		'changespeed' => '300',
		'changefade' => 'fast',
		'easingin' => 'swing',
		'easingout' => 'swing',
		'showclosebutton' => 'true',
		'shownavarrows' => 'true',
		'enableescapebutton' => 'true',
		'selecteur_frame' => '.iframe',
		'communwidth' => '425',
		'communheight' => '355'
	), $config);
	// Insertion des librairies js
	$flux .='<script src="'._DIR_LIB_FANCYBOX.'fancybox/jquery.fancybox-1.3.1.js" type="text/javascript"></script>';
	if ($GLOBALS['meta']['fancybox']['molette']=='oui')
		$flux .='<script src="'._DIR_LIB_FANCYBOX.'fancybox/jquery.mousewheel-3.0.2.pack.js" type="text/javascript"></script>';
	$flux .='<script src="'.url_absolue(find_in_path('javascript/fancybox.js')).'" type="text/javascript"></script>';
	// Init de la fancybox suivant la configuration
	$flux .='
<script type="text/javascript">/* <![CDATA[ */
// fontion callback lancee a l affichage de la box
var fancyonshow=function() {
	//showlongdesc(this);
	hideembed();
}
// fontion callback lancee a la fermeture de la box
var fancyonclose=function() {
	showembed();
}
var fb_selecteur_galerie="'.$config['selecteur_galerie'].'";
var fb_selecteur_commun="'.$config['selecteur_commun'].'";
var fb_selecteur_frame="'.$config['selecteur_frame'].'";
var fb_options = {
	"padding": '.$config['padding'].',
	"margin": '.$config['margin'].',
	"opacity": '.$config['opacity'].',
	"modal": '.$config['modal'].',
	"cyclic": '.$config['cyclic'].',
	"scrolling": "'.$config['scrolling'].'",
	"width": '.$config['width'].',
	"height": '.$config['height'].',
	"autoScale": '.$config['autoscale'].',
	"autoDimensions": '.$config['autodimensions'].',
	"centerOnScroll": '.$config['centeronscroll'].',
	"hideOnOverlayClick": '.$config['hideonoverlayclick'].',
	"hideOnContentClick": '.$config['hideoncontentclick'].',
	"overlayShow": '.$config['overlayshow'].',
	"overlayOpacity": '.$config['overlayopacity'].',
	"overlayColor": "'.$config['overlaycolor'].'",
	"titleShow": '.$config['titleshow'].',
	"titlePosition": "'.$config['titleposition'].'",
	"transitionIn": "'.$config['transitionin'].'",
	"transitionOut": "'.$config['transitionout'].'",
	"speedIn": '.$config['speedin'].',
	"speedOut": '.$config['speedout'].',
	"changeSpeed": '.$config['changespeed'].',
	"changeFade": "'.$config['changefade'].'",
	"easingIn": "'.$config['easingin'].'",
	"easingOut": "'.$config['easingout'].'",
	"showCloseButton": '.$config['showclosebutton'].',
	"showNavArrows": '.$config['shownavarrows'].',
	"enableEscapeButton": '.$config['enableescapebutton'].',
	"onStart": fancyonshow,
	"onClosed": fancyonclose
};
var fb_commun_options = fb_options;
var fb_frame_options = fb_options;
if (window.jQuery)
(function($){if(typeof onAjaxLoad == "function") onAjaxLoad(fancy_init);
	$(fancy_init);
 })(jQuery);
/* ]]> */</script>';
	// Inclusion des styles propres a fancybox
	$flux .='<link rel="stylesheet" href="'._DIR_LIB_FANCYBOX.'fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="all" />';

	return $flux;
}

?>
