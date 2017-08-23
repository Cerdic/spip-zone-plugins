<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

include_spip('inc/config');

function palette_insert_head($flux) {
	if (lire_config('palette/palette_public') == 'on') {
		$flux .= palette_header_common();
	}
	return $flux;
}

function palette_header_prive($flux) {
	if (lire_config('palette/palette_ecrire') == 'on') {
		$flux .= palette_header_common();
	}
	return $flux;
}

/**
 * Retourne le code html head pour la palette
 * Cette fonction peut être surchargée (cf doc SPIP)
 *
 * @return string
 */
function palette_header_common() {
	$ret = '<script type="text/javascript" src="'. find_in_path('lib/tinyColorPicker/colors.js') . '"></script>'."\n";
	$ret .= '<script type="text/javascript" src="'. find_in_path('lib/tinyColorPicker/jqColorPicker.js') . '"></script>'."\n";
	$ret .= '<script type="text/javascript" src="'. find_in_path('javascript/palette.js') . '"></script>'."\n";
	return $ret;
}
