<?php

function fancybox_insert_head($flux){
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
		'molette' => '',
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
		'titleposition' => 'float',
		'titleformat' => 'null',
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
	$flux .="\n".'<script src="'._DIR_LIB_FANCYBOX.'fancybox/jquery.fancybox-1.3.4.js" type="text/javascript"></script>'."\n";
	if ($config['molette'])
		$flux .='<script src="'._DIR_LIB_FANCYBOX.'fancybox/jquery.mousewheel-3.0.4.pack.js" type="text/javascript"></script>'."\n";
	$flux .='<script src="'.url_absolue(find_in_path('javascript/fancybox.js')).'" type="text/javascript"></script>'."\n";
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
	"titleFormat": '.$config['titleformat'].',
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
	$flux .="\n".'<link rel="stylesheet" href="'._DIR_LIB_FANCYBOX.'fancybox/jquery.fancybox-1.3.4.css" type="text/css" media="all" />'."\n";

	return $flux;
}

?>
