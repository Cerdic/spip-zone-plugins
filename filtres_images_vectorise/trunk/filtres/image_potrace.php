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
	$width_thumb = 256;
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
		'colors' => 16,
	];
	$potconfig = array_merge($potconfig, $options);

	$cache = _image_valeurs_trans($img, "image_potrace2-$width_thumb-".json_encode($potconfig), "svg");
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

		// temps maxi en secondes par iteration
		// si on a pas fini on renvoie une image incomplete et on finira au calcul suivant
		$start_time = time();
		$time_budget = 20;
		$time_out = $_SERVER['REQUEST_TIME']+25;
		if (time()>$time_out) {
			return $img;
		}

		if (!@file_exists($fichier)) {
			return false;
		}

		$thumb = image_reduire($img,$width_thumb);
		$width_thumb = largeur($thumb);

		$nb_colors = 16;
		if (is_numeric($potconfig['colors'])) {
			$nb_colors = max(1,intval($potconfig['colors']));
			$potconfig['colors'] = 'auto';
		}
		elseif(is_array($potconfig['colors'])) {
			$nb_colors = count($potconfig['colors']);
		}
		if ($potconfig['bgcolor'] === 'auto' or $potconfig['colors'] === 'auto') {

			$palette = extraire_palette_couleurs($img, max($nb_colors, 5), 32);
			$couleurs = [];
			while (count($couleurs) < $nb_colors and count($palette)>1) {
				$couleurs[] = array_shift($palette);
			}
			#$couleurs[] = '#ffffff';
		}
		if (is_array($potconfig['colors']) and count($potconfig['colors'])) {
			$couleurs = $potconfig['colors'];
		}

		$rounding = $potconfig['rounding'];
		unset($potconfig['rounding']);
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

		spip_timer('potrace');
		$img_bg = $thumb;
		if ($potconfig['bgcolor'] === 'auto'){
			$img_bg = image_reduire($img_bg, 128);
			$img_bg = filtrer('image_fond_transparent', $img_bg, 'ffffff');
		}
		$svg_layers = [];
		$offset_rayon = 50;
		//$couleurs = array_reverse($couleurs);
		foreach ($couleurs as $couleur) {
			$couleur = '#' . ltrim(couleur_html_to_hex($couleur), '#');
			//var_dump("<div style='display: inline-block;width: 30px;height: 15px;background-color:$couleur'></div>");

			$luminance = couleur_luminance_relative($couleur);
			$rayon = max(50,0.5 * round(255 * (1 - $luminance)) + $offset_rayon);
			$offset_rayon = max(0, $offset_rayon - 10);

			$thumbc = filtrer('image_filtrer_couleur', $thumb, $couleur,$rayon);
			if ($potconfig['bgcolor'] === 'auto'){
				$img_bg = filtrer('image_filtrer_couleur', $img_bg, $couleur, $rayon, 'remove');
			}

			$source = extraire_attribut($thumbc, 'src');
			$pot = new Potracio();
			$pot->loadImageFromFile($source);
			$pot->setParameter($potconfig);
			$pot->process();
			$svg_image = $pot->getSVG($coeffSize);

			$svg_image = explode('>', $svg_image, 2);
			$balise_svg = $svg_image[0] . '>';

			// optimize the size : round all points to integer
			if ($rounding !== false and $rounding !== 'off'){
				$svg_image[1] = preg_replace_callback(",\b(\d+\.\d+)\b,ims", "_svg_round_point", $svg_image[1]);
			}
			//$svg_image[1] = preg_replace(",(\s)\s+,", "\\1", $svg_image[1]);
			$svg_image[1] = str_replace("black", $couleur, $svg_image[1]);
			$svg_layers[] = str_replace("</svg>", "", $svg_image[1]);

			if (time()>$start_time+$time_budget or time()>$time_out){
				break;
			}
		}
		$svg_layers = array_reverse($svg_layers);

		$w = extraire_attribut($balise_svg, "width");
		$h = extraire_attribut($balise_svg, "height");

		$time_compute = spip_timer('potrace');

		$balise_svg = "<svg viewBox=\"0 0 $w $h\" xmlns=\"http://www.w3.org/2000/svg\">";

		if ($potconfig['bgcolor'] === 'auto'){
			//$img_bg = filtrer('image_fond_transparent', $img_bg,'00ff00');
			#var_dump($img_bg);
			$couleur_bg = _image_extraire_couleur_moyenne_restante($img_bg, 64);
			#var_dump($couleur_bg);
			$couleur_bg = couleur_eclaircir($couleur_bg);
			#var_dump($couleur_bg);
		}
		else {
			$couleur_bg = $potconfig['bgcolor'];
		}
		if ($couleur_bg !== 'transparent') {
			$couleur_bg = '#' . ltrim(couleur_html_to_hex($couleur_bg), '#');
			//$balise_svg .= "<rect width=\"100%\" height=\"100%\" fill=\"$couleur_bg\"/>";
			$balise_svg .= "<path d=\"M-1,-1V{$h}H{$w}V-1z\" fill=\"$couleur_bg\"/>";
		}

		$svg_image = $balise_svg . implode('', $svg_layers) . "</svg>";

		ecrire_fichier($dest, $svg_image);
		spip_log("FINISHED: $fichier t=$time_compute length:" . strlen($svg_image), 'image_potrace');
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


// A partir d'une image,
// calcule la couleur moyenne sur une version reduite de l'image
function _image_extraire_couleur_moyenne_restante($img, $seuil_opacite = 64){
	// valeur par defaut si l'image ne peut etre lue
	$defaut = "F26C4E";

	include_spip('inc/filtres_images_lib_mini');
	$cache = _image_valeurs_trans($img, "_image_extraire_couleur_moyenne_restante-$seuil_opacite", "txt");
	if (!$cache){
		return [$defaut];
	}


	$fichier = $cache["fichier"];
	$dest = $cache["fichier_dest"];

	if (true or $cache["creer"]){

		if (@file_exists($fichier)){
			$im = $cache["fonction_imagecreatefrom"]($fichier);
			$w = $cache["largeur"];
			$h = $cache["hauteur"];

			$m = ['red' => 0, 'green' => 0, 'blue' => 0];
			$nb = 0;
			for ($x = 0; $x < $w; $x++) {
				for ($y=0; $y < $h; $y++) {

					$rgb = ImageColorAt($im, $x, $y);
					$a = ($rgb >> 24) & 0xFF;
					if ($a < $seuil_opacite) {
						$m['red'] += ($rgb >> 16) & 0xFF;
						$m['green'] += ($rgb >> 8) & 0xFF;
						$m['blue'] += $rgb & 0xFF;
						$nb++;
					}
				}
			}

			$couleur = $defaut;
			if ($nb>0) {
				$m['red'] = round($m['red']/$nb);
				$m['green'] = round($m['green']/$nb);
				$m['blue'] = round($m['blue']/$nb);
				$couleur = _couleur_dec_to_hex($m['red'], $m['green'], $m['blue']);
			}

		} else {
			$couleur = $defaut;
		}

		// Mettre en cache le resultat
		ecrire_fichier($dest, $couleur);
	} else {
		lire_fichier($dest, $couleur);
		if (!$couleur) {
			$couleur = $defaut;
		}
	}

	return $couleur;
}