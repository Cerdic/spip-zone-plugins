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

/**
 * Generation d'une Image SVG a partir d'un bitmap
 * en utilisant a la fois geometrize pour avoir un background non uniforme
 * et PotRace pour creer un trace de details que l'on superpose en mixant avec la geometrization
 *
 * @param string $img
 * @param int|string $nb_shapes
 * @param array $geometrizeOptions
 * @param array $potraceOptions
 * @return string
 */
function image_geopotrize($img, $nb_shapes = 'auto', $opacite_trace = 0.75, $geometrizeOptions = [], $potraceOptions=[]) {

	$cache = _image_valeurs_trans($img, "image_vectorise-".json_encode([$nb_shapes, $opacite_trace, $geometrizeOptions, $potraceOptions]), "svg");
	if (!$cache) {
		return false;
	}
	// facile !
	if ($cache['format_source'] === 'svg'){
		return $img;
	}

	if ($cache["creer"]) {

		include_spip('filtres/image_geometrize');
		include_spip('filtres/image_potrace');

		if ($nb_shapes === 'auto' or !intval($nb_shapes)) {
			$coeff_quality = 0.4; // par rapport a la qualite auto de geometrize on applique un x0.2 = 20% pour le auto ici

			$auto = round($coeff_quality * sqrt($cache['largeur'] * $cache['hauteur']) / 2);
			$max_shapes = (isset($geometrizeOptions['maxShapes']) ? $geometrizeOptions['maxShapes'] : 600);
			$auto = min($auto, $max_shapes);
			if (strpos($nb_shapes, 'x') === 0 and is_numeric($coeff = substr($nb_shapes,1))) {
				$auto = round($auto * $coeff);
			}
			$nb_shapes = $auto;
		}

		// on commence par un background geometrize sans trop de details
		$thumbnail = filtrer('image_reduire', $img, 128);
		$img_svg_geo = image_geometrize($thumbnail, $nb_shapes, $geometrizeOptions);
		$file_svg_geo = supprimer_timestamp(extraire_attribut($img_svg_geo, 'src'));
		$svg_geo = file_get_contents($file_svg_geo);

		$svg_geo = explode('>', $svg_geo, 2);
		if (strpos($svg_geo[0], "<" . "?xml")===0){
			$svg_geo = explode('>', trim($svg_geo[1]), 2);
		}

		$t = $svg_geo[0] . '>';
		$viewbox = extraire_attribut($t, 'viewBox');
		$viewbox = explode(' ', $viewbox);
		$width_geo = $viewbox[2];


		// on genere ensuite un trace PotRace plus fin, sans background
		$potraceOptions['rounding'] = "width=$width_geo";
		$potraceOptions['bgcolor'] = 'transparent';

		$thumbnail = filtrer('image_reduire', $img, 512);
		$thumbnail = filtrer('image_renforcement', $thumbnail);
		$img_svg_pot = image_potrace($thumbnail, $potraceOptions);
		$file_svg_pot = supprimer_timestamp(extraire_attribut($img_svg_pot, 'src'));
		$svg_pot = file_get_contents($file_svg_pot);

		$svg_pot = explode('>', $svg_pot, 2);
		if (strpos($svg_pot[0], "<" . "?xml")===0){
			$svg_pot = explode('>', trim($svg_pot[1]), 2);
		}

		// et on superpose
		$opacity = max(min(floatval($opacite_trace),1),0);
		$svg_pot[1] = str_replace("fill=", 'opacity="'.$opacity.'" style="mix-blend-mode: multiply" fill=', $svg_pot[1]);

		$svg_geo[1] = str_replace('</svg>', $svg_pot[1], $svg_geo[1]);

		$svg_image = $svg_geo[0] . '>' . $svg_geo[1];

		$dest = $cache["fichier_dest"];
		ecrire_fichier($dest, $svg_image);
	}

	if (!@file_exists($cache["fichier_dest"])) {
		return false;
	}

	return _image_ecrire_tag($cache, array('src' => $cache["fichier_dest"]));
}
