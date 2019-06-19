<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!function_exists('centre_image_x')) {
	function centre_image_x($img) { return 0.5; }
	function centre_image_y($img) { return 0.5; }
}

// Fabrique une image avec couche alpha
// en fonction du détourage de l'image
function image_detourer_alpha($im) {
	include_spip("inc/filtres_images");

	$fonction = array('image_alpha', func_get_args());
	$image = _image_valeurs_trans($im, "detourer_alpha", "png", $fonction);
	if (!$image) return("");
	
	$x_i = $image["largeur"];
	$y_i = $image["hauteur"];
	
	$im = $image["fichier"];
	$dest = $image["fichier_dest"];
	
	$creer = $image["creer"];
	
	
	if ($creer) {
		// Creation de l'image en deux temps
		// de facon a conserver les GIF transparents
		$im = $image["fonction_imagecreatefrom"]($im);
		imagepalettetotruecolor($im);
		imagealphablending($im, false);
		imagesavealpha($im, true);

		imagefilter($im, IMG_FILTER_EMBOSS);
		imagefilter($im, IMG_FILTER_GRAYSCALE);
		imagefilter($im, IMG_FILTER_GAUSSIAN_BLUR);
		
		$col_pleine = imageColorAllocateAlpha($im, 0, 0, 0, 0);
		$col_vide = imageColorAllocateAlpha($im, 0, 0, 0, 127);
		
		for ($x = 0; $x < $x_i; $x++) {
			for ($y = 0; $y < $y_i; $y++) {
				$rgb=imagecolorat($im,$x,$y);
				$b = $rgb & 0xFF;
								 
				 
				 if (abs($b - 127) < 10) imagesetpixel($im, $x, $y, $col_vide);
				 else  imagesetpixel($im, $x, $y, $col_pleine);
			}
		}	

		_image_gd_output($im,$image);
		imagedestroy($im);
	}
	

	return _image_ecrire_tag($image,array('src'=>$dest));
	
}


// Fabrique une image avec couche alpha
// en fonction du détourage de l'image
function image_detourer_polygon($im) {
	include_spip("inc/filtres_images");

	$fonction = array('image_detourer_polygon', func_get_args());
	$image = _image_valeurs_trans($im, "detourer_polygon", "txt", $fonction);
	if (!$image) return("");
		
	$x_i = $image["largeur"];
	$y_i = $image["hauteur"];
	
	$im = $image["fichier"];
	$dest = $image["fichier_dest"];
	
	$creer = $image["creer"];
	
	
	if ($creer) {
		// Creation de l'image en deux temps
		// de facon a conserver les GIF transparents
		$im = $image["fonction_imagecreatefrom"]($im);
		imagepalettetotruecolor($im);
		imagealphablending($im, false);
		imagesavealpha($im, true);

		imagefilter($im, IMG_FILTER_EMBOSS);
		imagefilter($im, IMG_FILTER_GRAYSCALE);
		imagefilter($im, IMG_FILTER_CONTRAST, -100);
		imagefilter($im, IMG_FILTER_CONTRAST, -100);

		$im2 = imagecreatetruecolor(100, 100);
		imagecopyresampled($im2, $im, 0, 0, 0, 0, 100, 100, $x_i, $y_i);

		$couples = array();

		$rgb = imagecolorat($im2, 0, 0);
		$b_ref = $rgb & 0xFF;

		for ($y = 0; $y < 100; $y++) {
			$boucle = true;
			for ($x = 0; $x < 100 && $boucle; $x++) {
				$rgb = imagecolorat($im2,$x,$y);
				$b = $rgb & 0xFF;
				if (abs($b - $b_ref) > 20) {
					$couples [] = "$x% $y%";
					$boucle = false;
				}
			}
		}
	
		$rgb = imagecolorat($im2, 99, 99);
		$b_ref = $rgb & 0xFF;

		
		for ($y = 99; $y >= 0; $y--) {
			$boucle = true;
			for ($x = 100; $x > 0 && $boucle; $x--) {
				$rgb = imagecolorat($im2,$x-1,$y);
				$b = $rgb & 0xFF;
				if (abs($b - $b_ref) > 20) {
					$couples [] = "$x% $y%";
					$boucle = false;
				}
			}
		}
		
		$couples = "polygon(".join($couples, ",").")";
		
		imagedestroy($im2);
		ecrire_fichier($dest, $couples);
	}
	
	$ret = "";
	lire_fichier($dest, $ret);

	return $ret;
	
}
