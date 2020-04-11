<?php
/**
 * Generation d'une preview svg
 *
 * @plugin     Adaptive Images
 * @copyright  2013-2019
 * @author     Cedric
 * @licence    GNU/GPL
 * @package    SPIP\Adaptive_Images\Preview
 */

use Potracio\Potracio as Potracio;

/**
 * Generation d'une preview svg a base de Potracio PHP port of PotRace
 * https://seenthis.net/messages/645575
 * @param string $img
 * @param array $options
 * @return string
 */
function preview_image_potrace_dist($img, $options) {

	include_spip("inc/filtres_images_lib_mini");
	include_spip('lib/potracio/Potracio');
	include_spip('filtres/couleurs');

	// dimension de la miniature a vectoriser
	$width_thumb = 128;
	$potconfig = [
		'turnpolicy' => "majority",
		'turdsize' => 100,
		'optcurve' => false,
		'alphamax' => 0.5,
		'opttolerance' => 0.2
	];

	$cache = _image_valeurs_trans($img, "svg-thumb-potrace-$width_thumb-".json_encode($potconfig), "svg");
	if (!$cache) {
		return false;
	}

	$fichier = $cache["fichier"];
	$dest = $cache["fichier_dest"];

	if (true or $cache["creer"]) {
		if (!@file_exists($fichier)) {
			return false;
		}
		$thumb = image_reduire($img,$width_thumb);
		$source = extraire_attribut($thumb, 'src');

		$couleur = _image_couleur_moyenne($fichier);
		$couleur_dark = couleur_foncer_si_claire($couleur);
		$couleur_light = couleur_eclaircir_si_foncee($couleur);
		$couleur_light = _couleur_hex_to_dec($couleur_light);
		$couleur_light['red'] = min($couleur_light['red'],238);
		$couleur_light['green'] = min($couleur_light['green'],238);
		$couleur_light['blue'] = min($couleur_light['blue'],238);
		$couleur_light = _couleur_dec_to_hex($couleur_light['red'],$couleur_light['green'],$couleur_light['blue']);

		$pot = new Potracio();
		$pot->loadImageFromFile($source);
		$pot->setParameter($potconfig);

		$pot->process();
		$svg_image = $pot->getSVG(1);

		$svg_image = explode('>', $svg_image, 2);

		$t = $svg_image[0] . '>';
		$w = extraire_attribut($t, "width");
		$h = extraire_attribut($t, "height");

		$svg_image[0] = "<svg viewBox=\"0 0 $w $h\" xmlns=\"http://www.w3.org/2000/svg\"><rect width=\"100%\" height=\"100%\" fill=\"#$couleur_light\"/>";

		// optimize the size : round all points to integer
		$svg_image[1] = preg_replace_callback(",\b(\d+\.\d+)\b,ims", "svg_round_point", $svg_image[1]);
		$svg_image[1] = str_replace("black", "#".$couleur_dark, $svg_image[1]);
		$svg_image = $svg_image[0] . $svg_image[1];

		ecrire_fichier($dest, $svg_image);
	}

	if (!@file_exists($dest)) {
		return false;
	}

	return $dest;
}

function svg_round_point($m) {
	return round($m[0]);
}


if (!function_exists('_image_couleur_moyenne')) {

// A partir d'une image,
// calcule la couleur moyenne sur une version reduire de l'image
function _image_couleur_moyenne($img, $w = 20, $h = 20) {
	static $couleur_moyenne = array();

	if (isset($couleur_moyenne["$img-$w-$h"])) {
		return $couleur_moyenne["$img-$w-$h"];
	}

	// valeur par defaut si l'image ne peut etre lue
	$defaut = "F26C4E";

	$cache = _image_valeurs_trans($img, "coul-moyenne-$w-$h", "txt");
	if (!$cache) {
		return $couleur_moyenne["$img-$w-$h"] = $defaut;
	}


	$fichier = $cache["fichier"];
	$dest = $cache["fichier_dest"];

	if (isset($couleur_moyenne["$fichier-$w-$h"])) {
		return $couleur_moyenne["$fichier-$w-$h"];
	}

	$creer = $cache["creer"];

	if ($creer) {
		if (@file_exists($fichier)) {
			$width = $cache["largeur"];
			$height = $cache["hauteur"];

			$newwidth = $w;
			$newheight = $h;

			$thumb = imagecreate($newwidth, $newheight);

			$source = $cache["fonction_imagecreatefrom"]($fichier);

			imagepalettetotruecolor($source);

			imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

			$moyenne = null;
			$nb_points = 0;
			for ($x=0;$x<$w;$x++) {
				for ($y=0;$y<$h;$y++) {
					// get a color
					$color_index = imagecolorat($thumb, $x, $y);
					// make it human readable
					$color_tran = imagecolorsforindex($thumb, $color_index);
					if ($color_tran['alpha'] != 127) {
						if (is_null($moyenne)) {
							$moyenne = $color_tran;
						}
						else {
							$moyenne['red'] += $color_tran['red'];
							$moyenne['green'] += $color_tran['green'];
							$moyenne['blue'] += $color_tran['blue'];
						}
						$nb_points++;
					}
				}
			}
			if (is_null($moyenne)) {
				$couleur = $defaut;
			}
			else {
				if ($nb_points > 1) {
					$moyenne['red'] = round($moyenne['red'] / $nb_points);
					$moyenne['green'] = round($moyenne['green'] / $nb_points);
					$moyenne['blue'] = round($moyenne['blue'] / $nb_points);
				}

				$couleur = _couleur_dec_to_hex($moyenne["red"], $moyenne["green"], $moyenne["blue"]);
			}
		} else {
			$couleur = $defaut;
		}

		// Mettre en cache le resultat
		$couleur_moyenne["$fichier-$w-$h"] = $couleur;
		ecrire_fichier($dest, $couleur_moyenne["$fichier-$w-$h"]);
	} else {
		lire_fichier($dest, $couleur_moyenne["$fichier-$w-$h"]);
	}

	return $couleur_moyenne["$img-$w-$h"] = $couleur_moyenne["$fichier-$w-$h"];
}
}
