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
 * Filter une image bitmap selon une couleur
 * retourne une image N&B fonction de la distance a la couleur choisie : Noir si le pixel matche la couleur demandee, blanc si il en est trop eloigne
 *
 * @param string $img
 * @param string $couleur
 * @param int $rayon
 * @param string $mode
 *   masque|mask : cree un masque noir et blanc (pixels de la couleur cherchee sont en noir, pixels tres differents sont en blanc)
 *   couleur|color : créé un masque couleur/opacite (pixels de la couleur cherchee sont en opacite 1, pixels tres differents sont en blanc transparent) - opacite et clarete varient
 *   opacite|opacity : créé un masque opacite (pixels de la couleur cherchee sont en opacite 1, pixels tres differents sont transparent) - seule l'opacite varie
 *   retire|remove : les pixes de la couleur cherchee sont rendus transparents, on ne garde que les pixels tres eloignes en couleur
 * @param string $format
 * @return string
 */
function image_filtrer_couleur($img, $couleur, $rayon=100, $mode='masque', $format='png') {

	$fonction = "image_filtrer_couleur";
	$args = func_get_args();
	if ($format === 'auto') {
		$format = false;
	}
	$cache = _image_valeurs_trans($img, "filtrer_couleur-$couleur-$rayon-$mode", $format, [$fonction, $args]);

	if (!$cache) {
		return false;
	}

	$im = $cache["fichier"];
	$dest = $cache["fichier_dest"];
	if ($cache["creer"]) {

		$im = $cache["fonction_imagecreatefrom"]($im);
		$w = $cache["largeur"];
		$h = $cache["hauteur"];

		$im_ = imagecreatetruecolor($w, $h);
		@imagealphablending($im_, false);
		@imagesavealpha($im_,true);
		$color_t = ImageColorAllocateAlpha( $im_, 255, 255, 255 , 127 );
		imagefill ($im_, 0, 0, $color_t);

		$cc = _couleur_hex_to_dec($couleur);

		switch ($mode) {
			case 'retire':
			case 'remove':
				$filtre_pixel = 'pixel_filtrer_couleur_retire';
				break;
			case 'couleur':
			case 'color':
				$filtre_pixel = 'pixel_filtrer_couleur_couleur';
				break;
			case 'opacite':
			case 'opacity':
				$filtre_pixel = 'pixel_filtrer_couleur_opacite';
				break;
			case 'masque':
			case 'mask':
			default:
				$filtre_pixel = 'pixel_filtrer_couleur_masque';
				break;
		}

		for ($x = 0; $x < $w; $x++) {
			for ($y=0; $y < $h; $y++) {

				$rgb = ImageColorAt($im, $x, $y);
				$a = ($rgb >> 24) & 0xFF;
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;

				$d = ($cc['red'] - $r) * ($cc['red'] - $r)
					+ ($cc['green'] - $g) * ($cc['green'] - $g)
					+ ($cc['blue'] - $b) * ($cc['blue'] - $b);
				$d = sqrt($d) / $rayon;

				switch ($mode) {
					case 'retire':
					case 'remove':
				}
				list ($r, $g, $b, $a) = $filtre_pixel($d, $r , $g, $b, $a, $cc['red'], $cc['green'], $cc['blue']);
				$color = ImageColorAllocateAlpha( $im_, $r, $g, $b , $a);
				imagesetpixel ($im_, $x, $y, $color);
			}
		}
		_image_gd_output($im_,$cache);
		imagedestroy($im_);
		imagedestroy($im);
	}

	return _image_ecrire_tag($cache,array('src'=>$dest));
}

function pixel_filtrer_couleur_retire($d, $pixel_r, $pixel_g, $pixel_b, $pixel_a, $search_r, $search_g, $search_b) {

	$d = min(max($d-0.1,0),1);
	$d = 1 - $d;
	$a = min(round((127 - $pixel_a) * $d) + $pixel_a, 127);
	$r = max(0,min($pixel_r + round((255-$pixel_r) * $d),255));
	$g = max(0,min($pixel_g + round((255-$pixel_g) * $d),255));
	$b = max(0,min($pixel_b + round((255-$pixel_b) * $d),255));

	return array($r, $g, $b, $a);
}

function pixel_filtrer_couleur_masque($d, $pixel_r, $pixel_g, $pixel_b, $pixel_a, $search_r, $search_g, $search_b) {
	$d = round(min($d,1) * 255);
	return array($d, $d, $d, $pixel_a);
}

function pixel_filtrer_couleur_opacite($d, $pixel_r, $pixel_g, $pixel_b, $pixel_a, $search_r, $search_g, $search_b) {
	$d = min($d,1);
	$a = min(round((127 - $pixel_a) * $d) + $pixel_a, 127);

	return array($search_r, $search_g, $search_b, $a);
}

function pixel_filtrer_couleur_couleur($d, $pixel_r, $pixel_g, $pixel_b, $pixel_a, $search_r, $search_g, $search_b) {
	$d = min($d,1);
	$a = min(round((127 - $pixel_a) * $d) + $pixel_a, 127);

	$r = max(0,min($search_r + round((255-$search_r) * $d),255));
	$g = max(0,min($search_g + round((255-$search_g) * $d),255));
	$b = max(0,min($search_b + round((255-$search_b) * $d),255));

	return array($r, $g, $b, $a);
}