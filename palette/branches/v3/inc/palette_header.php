<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Définit l'entête par défaut pour le plugin Palette
 */
function inc_palette_header_dist($type='') {
	$js = generer_url_public('palette.js');
	$css = find_in_path(_DIR_LIB_PALETTE.'farbtastic.css');

	$ret =  '<link rel="stylesheet" href="'.$css.'" type="text/css" media="all" />'."\n";
	$ret .= '<script type="text/javascript" src="'.$js.'"></script>'."\n";

	if($type=='public'){
		// insertion de la feuille de style uniquement pour le public
		$css_publique = generer_url_public('palette.css');
		$ret .=  '<link rel="stylesheet" href="'.$css_publique.'" type="text/css" media="all" />'."\n";
	}
	return $ret;
}
?>
