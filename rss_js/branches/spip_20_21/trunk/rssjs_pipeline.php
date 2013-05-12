<?php
function rssjs_inclure_js($flux) {
	$chemin_rssjs = find_in_path('js/rssjs.js');
	if ($chemin_rssjs){
		$flux .= '<!-- plugin rssjs -->'."\n";
		$flux .= '<script type="text/javascript" src="https://www.google.com/jsapi"></script>'."\n";
		$flux .= '<script type="text/javascript">google.load("feeds", "1");</script>'."\n";
		$flux .= '<script type="text/javascript" src="'.$chemin_rssjs.'"></script>'."\n";
	}
	return $flux;
}

function rssjs_inclure_css($flux) {
	$chemin_rsscss = find_in_path('css/rssjs.css');
	if ($chemin_rsscss){
		$flux .= '<!-- plugin rssjs -->'."\n";
		$flux .= '<link href="'.$chemin_rsscss.'" rel="stylesheet" type="text/css" media="projection, screen, tv" />'."\n";
	}
	return $flux;
}

?>