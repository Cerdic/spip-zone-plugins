<?php

/**
 * Définit l'entête par défaut pour le plugin Palette
 */
function inc_palette_header_dist() {
	return '<link rel="stylesheet" href="'.find_in_path('palette.css').'" type="text/css" />'."\n".
				'<link rel="stylesheet" href="'.find_in_path('lib/farbtastic/farbtastic.css').'" type="text/css" />'."\n".
				'<script type="text/javascript" src="'._DIR_RACINE.'spip.php?page=palette.js"></script>'."\n";
}
?>