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
 * Generation d'une preview svg a base de gradients
 * https://seenthis.net/messages/660728#message661081
 * @param string $img
 * @param array $options
 * @return string
 */
function preview_image_gradients_dist($img, $options) {

	include_spip("inc/filtres_images_lib_mini");

	// etapes dans le gradient hor et vert
	$w = 20;
	$h = 20;
	$cache = _image_valeurs_trans($img, "svg-thumb-gradients-$w-$h", "svg");
	if (!$cache) {
		return false;
	}

	$fichier = $cache["fichier"];
	$dest = $cache["fichier_dest"];

	if (true or $cache["creer"]) {
		if (!@file_exists($fichier)) {
			return false;
		}

		$width = $cache["largeur"];
		$height = $cache["hauteur"];

		$newwidth = $w;
		$newheight = $h;
		$gradient_h = "";
		$gradient_v = "";

		$thumb = imagecreate($newwidth, $newheight);

		$source = $cache["fonction_imagecreatefrom"]($fichier);

		imagepalettetotruecolor($source);

		imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);


		// generer le gradient horizontal
		for ($x=0;$x<$w;$x+=4){
			$m = ['red' => 0, 'green' => 0, 'blue' => 0];
			$nb_points = 0;
			for ($y=round($h/4);$y<round(3*$h/4);$y++) {
				// get a color
				$color_index = imagecolorat($thumb, $x, $y);
				// make it human readable
				$c = imagecolorsforindex($thumb, $color_index);
				if ($c['alpha'] != 127) {
					$m['red'] += $c['red'];
					$m['green'] += $c['green'];
					$m['blue'] += $c['blue'];
					$nb_points++;
				}
			}
			if ($nb_points) {
				$m['red'] = round($m['red'] / $nb_points);
				$m['green'] = round($m['green'] / $nb_points);
				$m['blue'] = round($m['blue'] / $nb_points);
			}
			else {
				$color_index = imagecolorat($thumb, $x, round($h/2));
				// make it human readable
				$m = imagecolorsforindex($thumb, $color_index);
			}
			$couleur = _couleur_dec_to_hex($m["red"], $m["green"], $m["blue"]);
			$step = round($x * 100 / ($w - 1));
			$gradient_h .= "<stop offset=\"{$step}%\" stop-color=\"#$couleur\" />";
		}

		// generer le gradient vertical
		for ($y=0;$y<$h;$y+=4){
			$m = ['red' => 0, 'green' => 0, 'blue' => 0];
			$nb_points = 0;
			for ($x=round($w/4);$x<round(3*$w/4);$x++) {
				// get a color
				$color_index = imagecolorat($thumb, $x, $y);
				// make it human readable
				$c = imagecolorsforindex($thumb, $color_index);
				if ($c['alpha'] != 127) {
					$m['red'] += $c['red'];
					$m['green'] += $c['green'];
					$m['blue'] += $c['blue'];
					$nb_points++;
				}
			}
			if ($nb_points) {
				$m['red'] = round($m['red'] / $nb_points);
				$m['green'] = round($m['green'] / $nb_points);
				$m['blue'] = round($m['blue'] / $nb_points);
			}
			else {
				$color_index = imagecolorat($thumb, round($w/2), $y);
				// make it human readable
				$m = imagecolorsforindex($thumb, $color_index);
			}
			$couleur = _couleur_dec_to_hex($m["red"], $m["green"], $m["blue"]);
			$step = round($y * 100 / ($h - 1));
			$gradient_v .= "<stop offset=\"{$step}%\" stop-color=\"#$couleur\" />";
		}

		$svg_image = <<<SVG
<svg viewBox="0 0 $width $height" xmlns="http://www.w3.org/2000/svg">
<defs><linearGradient id="gh">$gradient_h</linearGradient><linearGradient id="gv" x1="0" x2="0" y1="0" y2="1">$gradient_v</linearGradient></defs>
<rect id="recth" x="0" y="0" width="$width" height="$height" fill="url(#gh)"/>
<rect id="rectv" x="0" y="0" width="$width" height="$height" fill="url(#gv)" opacity="0.66" style="mix-blend-mode: multiply" />
</svg>
SVG;

		ecrire_fichier($dest, $svg_image);
	}

	if (!@file_exists($dest)) {
		return false;
	}

	return $dest;
}