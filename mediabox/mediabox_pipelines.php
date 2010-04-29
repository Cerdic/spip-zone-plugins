<?php

function mediabox_config($public=null){
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
		'minWidth'=>'400px',
		'minHeight'=>'',
	), $config);

	if ((is_null($public) AND test_espace_prive()) OR !$public) {
		$config = array_merge($config,array(
		'selecteur_galerie' => '#portfolios a[type^=\'image/\']',
		'selecteur_commun' => '.mediabox:not(body), .iconifier a[href$=jpg],.iconifier a[href$=png],.iconifier a[href$=gif]',
		'skin' => 'white-shadow',
		'maxWidth'=>'90%',
		'maxHeight'=>'95%',
		'minWidth'=>'600px',
		'minHeight'=>'300px',
		));
	}
	return $config;
}

function mediabox_insert_head_css($flux){
	static $done = false;
	if (!$done) {
		$done = true;
		$config = mediabox_config();
		if ($f = find_in_path((test_espace_prive()?"prive/":"")."colorbox/".$config['skin'].'/colorbox.css'))
			$flux .= '<link rel="stylesheet" href="'.$f.'" type="text/css" media="all" />';
	}
	return $flux;
}

function mediabox_insert_head($flux){
	$config = mediabox_config();

	$flux = mediabox_insert_head_css($flux); // au cas ou il n'est pas implemente

	$flux .='
<script src="'.find_in_path('javascript/jquery.colorbox.js').'" type="text/javascript"></script>
<script src="'.find_in_path('javascript/spip.mediabox.js').'" type="text/javascript"></script>';

	$flux .='<script type="text/javascript">/* <![CDATA[ */
var box_settings = {traiter_toutes_images:'.($config['traiter_toutes_images'] == 'oui'?'true':'false')
.',selecteur_galerie:"'.$config['selecteur_galerie']
.'",selecteur_commun:"'.$config['selecteur_commun']
.'",transition:"'.$config['transition']
.'",speed:"'.$config['speed']
.'",maxWidth:"'.$config['maxWidth']
.'",maxHeight:"'.$config['maxHeight']
.'",minWidth:"'.$config['minWidth']
.'",minHeight:"'.$config['minHeight']
.'",str_slideshowStart:"'.unicode2charset(html2unicode(_T('mediabox:boxstr_slideshowStart')))
.'",str_slideshowStop:"'.unicode2charset(html2unicode(_T('mediabox:boxstr_slideshowStop')))
.'",str_current:"'._T('mediabox:boxstr_current')
.'",str_previous:"'._T('mediabox:boxstr_previous')
.'",str_next:"'._T('mediabox:boxstr_next')
.'",str_close:"'._T('mediabox:boxstr_close')
.'"};
if (window.jQuery) (function($){ if(typeof onAjaxLoad == "function") onAjaxLoad(mediabox_init); $(mediabox_init); })(jQuery);
/* ]]> */</script>'."\n";

	return $flux;
}

?>
