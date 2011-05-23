<?php
function wgalerie_inclurejs_css($flux) {
	$flux .= '<!-- plugin wgalerie -->'."\n";
	$flux .= '<link href="'.find_in_path('fancybox/jquery.fancybox-1.3.1.css').'" rel="stylesheet" type="text/css" media="screen" />'."\n";
	$flux .= '<script type="text/javascript" src="'.find_in_path('fancybox/jquery.mousewheel-3.0.2.pack.js').'"></script>'."\n";
	$flux .= '<script type="text/javascript" src="'.find_in_path('fancybox/jquery.fancybox-1.3.1.pack.js').'"></script>'."\n";
	$flux .= recuperer_fond('lien_css_fancybox_ie')."\n";
	$flux .= '<script type="text/javascript" src="'.find_in_path('js/wgalerie.js').'"></script>'."\n";
	return $flux;
}
?>