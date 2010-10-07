<?php
function seances_inclurejs_css($flux) {
	$css = find_in_path('prive/seances_styles.css');
	if ($css) {
		$flux .= '<!-- plugin seances -->'."\n";
		$flux .= '<link href="'.$css.'" rel="stylesheet" type="text/css" />'."\n";
	}
	return $flux;
}
?>