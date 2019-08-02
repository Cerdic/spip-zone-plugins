<?php
/**
 * Fonctions du plugin Filtres Images Vectorise
 *
 * @plugin     Filtres Images Vectorise
 * @copyright  2019
 * @author     Cedric
 * @licence    GNU/GPL
 * @package    SPIP\Filtres Images Vectorise\Filtres
 */

use \Cerdic\Geometrize\Bitmap;
use \Cerdic\Geometrize\Bitmap\DominantColours;
use \Cerdic\Geometrize\Shape\Rectangle;
use \Cerdic\Geometrize\Exporter\SvgExporter;

// A partir d'une image,
// calcule la couleur moyenne sur une version reduite de l'image
function _image_extraire_palette($img, $nb_couleurs=3, $width_thumb=32){
	// valeur par defaut si l'image ne peut etre lue
	$defaut = "F26C4E";

	include_spip('inc/filtres_images_lib_mini');
	$cache = _image_valeurs_trans($img, "_image_extraire_palette-$nb_couleurs-$width_thumb", "txt");
	if (!$cache){
		return [$defaut];
	}


	$fichier = $cache["fichier"];
	$dest = $cache["fichier_dest"];

	if ($cache["creer"]){
		#include_spip('filtres/couleurs');
		#include_spip('filtres/images_lib');
		include_spip('lib/geometrize/geometrize.init');

		$thumb = image_reduire($img, $width_thumb);
		$source = extraire_attribut($thumb, 'src');

		if (@file_exists($fichier)){
			$bitmap = Bitmap::createFromImageFile($source);
			$bg = new Rectangle($bitmap->width, $bitmap->height, 1, true);
			$dominantColours = DominantColours::dominantColours(max($nb_couleurs,3), $bitmap, $bg->rasterize());

			$palette = [];
			include_spip('filtres/images_geometrize');
			foreach ($dominantColours as $color) {
				$palette[] = SvgExporter::hexaForColor($color);
				if (count($palette) == $nb_couleurs) {
					break;
				}
			}
		} else {
			$palette = [$defaut];
		}

		// Mettre en cache le resultat
		ecrire_fichier($dest, json_encode($palette));
	} else {
		lire_fichier($dest, $json);
		if (!$palette = json_decode($json, true)) {
			$palette = [$defaut];
		}
	}

	return $palette;
}