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
function image_geopotrize($img, $nb_shapes = 'auto', $opacite_trace = 0.66, $mix_mode='multiply', $geometrizeOptions = [], $potraceOptions=[]) {

	if (!in_array($mix_mode, ['color', 'color-burn', 'color-dodge', 'darken', 'difference', 'exclusion', 'hard-ligt', 'hue', 'lighten', 'luminosity', 'multiply', 'normal', 'overlay', 'rever', 'saturation', 'screen', 'soft-light'])) {
		$mix_mode = 'soft-light';
	}
	$fonction = "image_geopotrize";
	$args = func_get_args();
	$cache = _image_valeurs_trans($img, "image_geopotrize-".json_encode([$nb_shapes, $opacite_trace, $mix_mode, $geometrizeOptions, $potraceOptions]), "svg", [$fonction, $args]);
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

		$time_out = $_SERVER['REQUEST_TIME']+25;
		if (time()>$time_out) {
			return $img;
		}

		$coeff_size=1;
		if ($nb_shapes === 'auto' or !intval($nb_shapes)) {
			$coeff_quality = 0.4; // par rapport a la qualite auto de geometrize on applique un x0.2 = 20% pour le auto ici

			$auto = round($coeff_quality * sqrt($cache['largeur'] * $cache['hauteur']) / 2);
			$max_shapes = (isset($geometrizeOptions['maxShapes']) ? $geometrizeOptions['maxShapes'] : 600);
			$auto = min($auto, $max_shapes);
			if (strpos($nb_shapes, 'x') === 0 and is_numeric($coeff = substr($nb_shapes,1))) {
				$auto = round($auto * $coeff);
				if ($coeff>1) {
					$coeff_size = $coeff;
				}
			}
			$nb_shapes = $auto;
		}

		// on commence par un background geometrize sans trop de details
		$thumbnail = filtrer('image_reduire', $img, round($coeff_size * 128));
		$img_svg_geo = image_geometrize($thumbnail, $nb_shapes, $geometrizeOptions);

		if (time()>$time_out) {
			return $img;
		}

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
		if (!isset($potraceOptions['background'])) {
			$potraceOptions['background'] = 'transparent';
		}
		if (!isset($potraceOptions['colors'])){
			$potraceOptions['colors'] = '8';
		}

		$img_svg_pot = image_potrace($img, $potraceOptions);
		$file_svg_pot = supprimer_timestamp(extraire_attribut($img_svg_pot, 'src'));
		$svg_pot = file_get_contents($file_svg_pot);

		$svg_pot = explode('>', $svg_pot, 2);
		if (strpos($svg_pot[0], "<" . "?xml")===0){
			$svg_pot = explode('>', trim($svg_pot[1]), 2);
		}
		$viewboxP = extraire_attribut($svg_pot[0] . '>', 'viewBox');
		$viewboxP = explode(' ', $viewboxP);
		$width_pot = $viewboxP[2];
		$scale = $width_geo / $width_pot;

		// et on superpose
		if ($potraceOptions['background'] != 'transparent') {
			$potrace = explode("fill=", $svg_pot[1], 2);
			$svg_pot[1] = $potrace[0] . "opacity=\"0.5\" fill=" . $potrace[1];
		}
		$opacity = max(min(floatval($opacite_trace),1),0);
		$svg_pot[1] = '<g transform="scale('.$scale.')" opacity="'.$opacity.'" style="mix-blend-mode: '.$mix_mode.'">'
		 . str_replace('</svg>', '</g></svg>', $svg_pot[1]);

		$svg_geo[1] = "<g>" . str_replace('</svg>', "</g>" . $svg_pot[1], $svg_geo[1]);

		$svg_image = $svg_geo[0] . '>' . $svg_geo[1];

		$dest = $cache["fichier_dest"];
		ecrire_fichier($dest, $svg_image);
	}

	if (!@file_exists($cache["fichier_dest"])) {
		return false;
	}

	return _image_ecrire_tag($cache, array('src' => $cache["fichier_dest"]));
}
