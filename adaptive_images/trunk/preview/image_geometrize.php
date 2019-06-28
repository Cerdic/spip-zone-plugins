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


/**
 * Generation d'une preview svg a base de geometrize
 *
 * @param string $img
 * @param array $options
 * @return string
 */
function preview_image_geometrize_dist($img, $options) {

	include_spip("inc/filtres_images_lib_mini");
	include_spip('filtres/couleurs');
	include_spip('lib/geometrize/geometrize.init');

	// dimension de la miniature a vectoriser
	$width_thumb = 128;
	$geometrize_options = [
		"shapeTypes" => [geometrize_shape_ShapeTypes::$TRIANGLE],
		"alpha" => 255,
		"candidateShapesPerStep" => 50,
		"shapeMutationsPerStep" => 50, // 100
		"steps" => 20,
	];

	$cache = _image_valeurs_trans($img, "svg-thumb-geometrize-$width_thumb-".json_encode($geometrize_options), "svg");
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

		// TODO : bitmap from $source
		list($w, $h) = getimagesize($source);
	  $image = imagecreatefromstring(file_get_contents($source));
		$bitmap = new geometrize_bitmap_Bitmap();
		$bitmap->width = $w;
		$bitmap->height = $h;

		for ($x=0;$x<$w;$x++){
			for ($y=0;$y<$h;$y++) {
				// get a color
				$color_index = imagecolorat($image, $x, $y);
				// make it human readable
				$c = imagecolorsforindex($image, $color_index);
				$bitmap->setPixel($x, $y, _couleur_to_geometrize($c));
			}
		}

		$couleur_bg = _image_couleur_moyenne($fichier);
		$runner = new geometrize_runner_ImageRunner($bitmap, _couleur_to_geometrize($couleur_bg));

		$hx_options = new _hx_array();
		$hx_options->shapeTypes = new _hx_array($geometrize_options['shapeTypes']);
		$hx_options->alpha = $geometrize_options['alpha'];
		$hx_options->candidateShapesPerStep = $geometrize_options['candidateShapesPerStep'];
		$hx_options->shapeMutationsPerStep = $geometrize_options['shapeMutationsPerStep'];
		$hx_options->steps = $geometrize_options['steps'];
		//var_dump($hx_options);
		$res = [];
		$shapes = new _hx_array();
		spip_timer('runner');
		for ($i = 0; $i < $geometrize_options['steps'];$i++) {
			$r = $runner->step($hx_options);
			$shapes->push($r->get(0));
		}
		var_dump(spip_timer('runner'));

		//var_dump($r,'<hr/>',$shapes);


		// TODO : export to SVG
		$svg_image = trim(geometrize_exporter_SvgExporter::export($shapes, $w, $h));

		$svg_image = explode('>', $svg_image, 2);
		if (strpos($svg_image[0], "<"."?xml") === 0) {
			$svg_image = explode('>', trim($svg_image[1]), 2);
		}

		$t = $svg_image[0] . '>';
		$w = extraire_attribut($t, "width");
		$h = extraire_attribut($t, "height");

		$svg_image[0] = "<svg viewBox=\"0 0 $w $h\" xmlns=\"http://www.w3.org/2000/svg\"><rect width=\"100%\" height=\"100%\" fill=\"#$couleur_bg\"/>";

		// optimize the size :
		$svg_image[1] = str_replace(' fill-opacity="1"/>','/>', $svg_image[1]);
		$svg_image[1] = preg_replace_callback("/rgb\((\d+),(\d+),(\d+)\)/ims", "svg_color_hexa", $svg_image[1]);
		$svg_image[1] = str_replace(">\n",">", $svg_image[1]);


		//$svg_image[1] = str_replace("black", "#".$couleur_dark, $svg_image[1]);
		$svg_image = $svg_image[0] . $svg_image[1];

		var_dump(strlen($svg_image));

		ecrire_fichier($dest, $svg_image);
	}

	if (!@file_exists($dest)) {
		return false;
	}

	return $dest;
}

function svg_color_hexa($m) {
	$c = _couleur_dec_to_hex($m[1],$m[2],$m[3]);
	return "#$c";
}

function _couleur_to_geometrize($c) {
	if (is_string($c)) {
		$c = _couleur_hex_to_dec($c);
	}
	if (isset($c['alpha'])) {
		// alpha definition is the opposite (255 is opaque, 0 is transparent)
		$c['alpha'] = round((127-$c['alpha']) * 255 / 127);
	}
	else {
		$c['alpha'] = 255;
	}
	$couleur = ($c['red'] << 24) + ($c['green'] << 16) + ($c['blue'] << 8) + $c['alpha'];
	return $couleur;
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
