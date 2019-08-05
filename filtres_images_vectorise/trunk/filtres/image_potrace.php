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

use Potracio\Potracio;

/**
 * Generation d'une Image SVG a partir d'un bitmap
 * en utilisant Potracio PHP qui est un portage PHP de PotRace
 * https://seenthis.net/messages/645575
 *
 * @param string $img
 * @param array $options
 * @return string
 */
function image_potrace($img, $options=[]) {

	include_spip("inc/filtres_images_lib_mini");
	include_spip('lib/potracio/Potracio');
	include_spip('filtres/couleurs');

	// dimension de la miniature a vectoriser
	$width_thumb = 512;
	if (isset($options['width_thumb'])) {
		$width_thumb = $options['width_thumb'];
		unset($options['width_thumb']);
	}
	$potconfig = [
		'turnpolicy' => "minority", // "black" / "white" / "left" / "right" / "minority" / "majority"
		'turdsize' => 2,
		'optcurve' => true,
		'alphamax' => 0.75,
		'opttolerance' => 0.2,
		'rounding' => 1,
		'bgcolor' => 'auto',
		'color' => 'auto'
	];
	$potconfig = array_merge($potconfig, $options);

	$cache = _image_valeurs_trans($img, "image_potrace-$width_thumb-".json_encode($potconfig), "svg");
	if (!$cache) {
		return false;
	}
	// facile !
	if ($cache['format_source'] === 'svg'){
		return $img;
	}

	if ($cache["creer"]) {
		$fichier = $cache["fichier"];
		$dest = $cache["fichier_dest"];

		if (!@file_exists($fichier)) {
			return false;
		}

		$thumb = image_reduire($img,$width_thumb);
		$width_thumb = largeur($img);
		$source = extraire_attribut($thumb, 'src');

		if ($potconfig['bgcolor'] === 'auto' or $potconfig['color'] === 'auto') {
			$palette = extraire_palette_couleurs($img, 5, 32);

			$couleur_bg = array_shift($palette);
			$couleur_1 = reset($palette);
			if (couleur_luminance_relative($couleur_bg) < couleur_luminance_relative($couleur_1)) {
				$couleur_1 = $couleur_bg;
				$couleur_bg = reset($palette);
			}
			$couleur_bg = '#'.ltrim(couleur_eclaircir_si_foncee($couleur_bg),'#');
			$couleur_1 = '#'.ltrim(couleur_foncer_si_claire($couleur_1),'#');
		}
		if ($potconfig['bgcolor'] !== 'auto') {
			$couleur_bg = $potconfig['bgcolor'];
		}
		if ($potconfig['color'] !== 'auto') {
			$couleur_1 = $potconfig['color'];
		}

		$rounding = $potconfig['rounding'];
		unset($potconfig['rounding']);

		$pot = new Potracio();
		$pot->loadImageFromFile($source);
		$pot->setParameter($potconfig);

		$pot->process();
		$coeffSize = 1;
		if ($rounding !== false and $rounding !== 'off') {
			if (strpos($rounding, 'width=') === 0) {
				$coeffSize = intval(substr($rounding,6)) / $width_thumb;
				$rounding = false;
			}
			else {
				$rounding = intval($rounding);
				while ($rounding-->0) {
					$coeffSize *= 10;
				}
			}
		}
		$svg_image = $pot->getSVG($coeffSize);

		$svg_image = explode('>', $svg_image, 2);

		$t = $svg_image[0] . '>';
		$w = extraire_attribut($t, "width");
		$h = extraire_attribut($t, "height");

		$svg_image[0] = "<svg viewBox=\"0 0 $w $h\" xmlns=\"http://www.w3.org/2000/svg\">";
		if ($couleur_bg !== 'transparent') {
			$svg_image[0] .= "<rect width=\"100%\" height=\"100%\" fill=\"$couleur_bg\"/>";
		}

		// optimize the size : round all points to integer
		if ($rounding !== false and $rounding !== 'off'){
			$svg_image[1] = preg_replace_callback(",\b(\d+\.\d+)\b,ims", "_svg_round_point", $svg_image[1]);
		}
		$svg_image[1] = preg_replace(",(\s)\s+,", "\\1", $svg_image[1]);
		$svg_image[1] = str_replace("black", $couleur_1, $svg_image[1]);
		$svg_image = $svg_image[0] . $svg_image[1];

		ecrire_fichier($dest, $svg_image);
	}

	if (!@file_exists($cache["fichier_dest"])) {
		return false;
	}

	return _image_ecrire_tag($cache, array('src' => $cache["fichier_dest"]));
}

if (!function_exists('_svg_round_point')) {
	function _svg_round_point($m) {
		return round($m[0]);
	}
}