<?php
function wgalerie_inclure_js($flux) {
	$flux .= '<!-- plugin wgalerie -->'."\n";
	$flux .= '<script type="text/javascript" src="'.find_in_path('js/wgalerie.js').'"></script>'."\n";
	return $flux;
}

?>