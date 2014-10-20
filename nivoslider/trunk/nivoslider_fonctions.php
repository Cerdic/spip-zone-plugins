<?php
/**
 * Plugin NivoSlider pour Spip 3.0
 * Licence GPL (c)
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


function nivoslider_insert_head_css($flux){
	include_spip('inc/filtres');
	$css = produire_fond_statique("css/nivoslider.css");
	$flux .= '<link rel="stylesheet" href="'.$css.'" type="text/css" media="all" />';
	return $flux;
}


/**
 * Mise en forme de la balise img : src direct ou gif transparent selon le cas
 * evite de charger toutes les images directement
 * @param string $img
 * @param int $compteur
 * @param bool|string $nolazy
 *   don't use lazy load if true
 * @return string
 */
function nivoslider_img_display_first_only($img, $compteur, $nolazy){
	// charger l'image directement si c'est la premiere ou si on a active la navigation par vignette
	if ($compteur==1 OR ($nolazy!=false AND $nolazy!=="false")) return $img;

	$src = extraire_attribut($img,"src");
	$img = inserer_attribut($img,"data-src",$src);
	// gif transparent 1px
	// http://proger.i-forge.net/The_smallest_transparent_pixel/eBQ
	$img = inserer_attribut($img,"src","data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==");
	return $img;
}

?>
