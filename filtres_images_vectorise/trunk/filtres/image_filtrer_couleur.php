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
 * @return string
 */
function image_filtrer_couleur($img, $couleur, $rayon=100, $operation='keep') {

	$fonction = "image_filtrer_couleur";
	$args = func_get_args();
	$cache = _image_valeurs_trans($img, "filtrer_couleur-$couleur-$rayon-$operation", false, [$fonction, $args]);
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

				if ($operation === 'remove') {
					$d = min(max($d-0.1,0),1);
					$d = 1 - $d;
					$a = min(round((127 - $a) * $d) + $a, 127);
					$r = max(0,min($r + round((255-$r) * $d),255));
					$g = max(0,min($g + round((255-$g) * $d),255));
					$b = max(0,min($b + round((255-$b) * $d),255));
					$color = ImageColorAllocateAlpha( $im_, $r, $g, $b , $a);
				}
				else {
					$d = round(min($d,1) * 255);
					$color = ImageColorAllocateAlpha( $im_, $d, $d, $d , $a );
				}
				imagesetpixel ($im_, $x, $y, $color);
			}
		}
		_image_gd_output($im_,$cache);
		imagedestroy($im_);
		imagedestroy($im);
	}

	return _image_ecrire_tag($cache,array('src'=>$dest));
}
