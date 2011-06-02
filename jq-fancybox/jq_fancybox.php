<?php
function jq_fancybox_inclurejs_css($flux) {
	$flux .= '<!-- plugin fancybox -->'."\n";
	$flux .= '<script type="text/javascript" src="'.find_in_path('fancybox/jquery.fancybox-1.3.4.pack.js').'"></script>'."\n";
	$flux .= '<script type="text/javascript" src="'.find_in_path('fancybox/jquery.mousewheel-3.0.4.pack.js').'"></script>'."\n";
	$flux .= '<link href="'.find_in_path('fancybox/jquery.fancybox-1.3.4.css').'" rel="stylesheet" type="text/css" media="screen" />'."\n";
	$flux .= recuperer_fond('lien_css_fancybox_ie')."\n";
	return $flux;
}
?>