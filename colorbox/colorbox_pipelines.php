<?php

function colorbox_config(){
	include_spip("inc/filtres");
	$config = @unserialize($GLOBALS['meta']['mediabox']);
	if (!is_array($config))
		$config = array();
	$config = array_merge(array(
		'traiter_toutes_images' => 'oui',
		'selecteur_galerie' => '#documents_portfolio a[type=\'image/jpeg\'],#documents_portfolio a[type=\'image/png\'],#documents_portfolio a[type=\'image/gif\']',
		'selecteur_commun' => '.mediabox',
		'skin' => 'black-striped',
		'transition' => 'elastic',
		'speed'=>'200',
		'maxWidth'=>'90%',
		'maxHeight'=>'90%',
	), $config);
	return $config;
}

function colorbox_insert_head_css($flux){
	static $done = false;
	if (!$done) {
		$done = true;
		$config = colorbox_config();
		$flux .= '<link rel="stylesheet" href="'.find_in_path("colorbox/".$config['skin'].'/colorbox.css').'" type="text/css" media="all" />';
	}
	return $flux;
}

function colorbox_insert_head($flux){
	$config = colorbox_config();

	$flux = colorbox_insert_head_css($flux); // au cas ou il n'est pas implemente

	$flux .='
<script src="'.find_in_path('javascript/jquery.colorbox.js').'" type="text/javascript"></script>
<script src="'.find_in_path('javascript/spip.colorbox.js').'" type="text/javascript"></script>';

	$flux .='
<script type="text/javascript">/* <![CDATA[ */
var box_settings = {
traiter_toutes_images:'.($config['traiter_toutes_images'] == 'oui'?'true':'false').',
selecteur_galerie:"'.$config['selecteur_galerie'].'",
selecteur_commun:"'.$config['selecteur_commun'].'",
transition:"'.$config['transition'].'",
speed:"'.$config['speed'].'",
maxWidth:"'.$config['maxWidth'].'",
maxHeight:"'.$config['maxHeight'].'",
str_slideshowStart:"'.unicode2charset(html2unicode(_T('colorbox:boxstr_slideshowStart'))).'",
str_slideshowStop:"'.unicode2charset(html2unicode(_T('colorbox:boxstr_slideshowStop'))).'",
str_current:"'._T('colorbox:boxstr_current').'",
str_previous:"'._T('colorbox:boxstr_previous').'",
str_next:"'._T('colorbox:boxstr_next').'",
str_close:"'._T('colorbox:boxstr_close').'"
};

//onAjaxLoad is defined in private area only

if (window.jQuery)
(function($){
//if(typeof onAjaxLoad == "function") onAjaxLoad(colorbox_init);
	$(colorbox_init);
 })(jQuery);

/* ]]> */</script>';

	return $flux;
}

?>
