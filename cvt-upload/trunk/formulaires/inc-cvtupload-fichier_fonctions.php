<?php

include_spip('inc/filtres_images_mini');
include_spip('inc/filtres');
include_spip('inc/flock');
if (!function_exists('supprimer_timestamp')) { // compat 3.0
	function supprimer_timestamp($url) {
	    if (strpos($url, "?") === false) {
	            return $url;
	    }
	    return preg_replace(",\?[[:digit:]]+$,", "", $url);
	}
}

/**
 * Réduit une image, puis encode en base64, puis supprime la vignette qui a été créée
 * Notes: le filtre ne commence pas par image, car SPIP applique automatiquement un image_graver dans ce cas -> ca plante
 * @param string $img
 *     la balise img
 * @param int $taille
 *     la nouvelle taille
 *
 * @return string
 *     la balise img
 */

function _image_reduire_base64($img, $taille) {
	$image_reduite = image_reduire($img,$taille);
	$fichier_reduit = supprimer_timestamp(extraire_attribut($image_reduite,'src'));
	$finfo = finfo_open(FILEINFO_MIME_TYPE);
	$mime = finfo_file($finfo, $fichier_reduit);
	$base64_reduit = "data:$mime;base64,".base64_encode(file_get_contents($fichier_reduit));
	supprimer_fichier($fichier_reduit);
	$image_reduite = inserer_attribut($image_reduite,'src',$base64_reduit);
	return $image_reduite;
}
