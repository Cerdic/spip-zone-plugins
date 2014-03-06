<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/* ---------- fonctions de Paris Beyrouth ------------------------- */

/*
 *
 * Fonctions pour roue chromatique
 * http://www.paris-beyrouth.org/Une-palette-de-couleurs
 *
 */
function image_rgb2hsv ($R,$G,$B) {
	$var_R = ( $R / 255 ) ;                    //Where RGB values = 0 to 255
	$var_G = ( $G / 255 );
	$var_B = ( $B / 255 );

	$var_Min = min( $var_R, $var_G, $var_B ) ;   //Min. value of RGB
	$var_Max = max( $var_R, $var_G, $var_B ) ;   //Max. value of RGB
	$del_Max = $var_Max - $var_Min  ;           //Delta RGB value

	$V = $var_Max;
	$L = ( $var_Max + $var_Min ) / 2;

	if ( $del_Max == 0 )                     //This is a gray, no chroma...
	{
	   $H = 0 ;                            //HSL results = 0 to 1
	   $S = 0 ;
	}
	else                                    //Chromatic data...
	{
	   $S = $del_Max / $var_Max;

	   $del_R = ( ( ( $var_Max - $var_R ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
	   $del_G = ( ( ( $var_Max - $var_G ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
	   $del_B = ( ( ( $var_Max - $var_B ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;

	   if      ( $var_R == $var_Max ) $H = $del_B - $del_G;
	   else if ( $var_G == $var_Max ) $H = ( 1 / 3 ) + $del_R - $del_B;
	   else if ( $var_B == $var_Max ) $H = ( 2 / 3 ) + $del_G - $del_R;

	   if ( $H < 0 )  $H =  $H + 1;
	   if ( $H > 1 )  $H = $H - 1;
	}

	$ret["h"] = $H;
	$ret["s"] = $S;
	$ret["v"] = $V;

	return $ret;
}


/*
 * http://www.paris-beyrouth.org/Une-palette-de-couleurs
 */
function image_hsv2rgb ($H,$S,$V) {
	if ( $S == 0 )                       //HSV values = 0 to 1
	{
	   $R = $V * 255;
	   $G = $V * 255;
	   $B = $V * 255;
	}
	else
	{
	   $var_h = $H * 6;
	   if ( $var_h == 6 ) $var_h = 0 ;     //H must be < 1
	   $var_i = floor( $var_h )  ;           //Or ... var_i = floor( var_h )
	   $var_1 = $V * ( 1 - $S );
	   $var_2 = $V * ( 1 - $S * ( $var_h - $var_i ) );
	   $var_3 = $V * ( 1 - $S * ( 1 - ( $var_h - $var_i ) ) );

	   if      ( $var_i == 0 ) { $var_r = $V     ; $var_g = $var_3 ; $var_b = $var_1 ; }
	   else if ( $var_i == 1 ) { $var_r = $var_2 ; $var_g = $V     ; $var_b = $var_1 ; }
	   else if ( $var_i == 2 ) { $var_r = $var_1 ; $var_g = $V     ; $var_b = $var_3 ; }
	   else if ( $var_i == 3 ) { $var_r = $var_1 ; $var_g = $var_2 ; $var_b = $V ;     }
	   else if ( $var_i == 4 ) { $var_r = $var_3 ; $var_g = $var_1 ; $var_b = $V ; }
	   else                   { $var_r = $V     ; $var_g = $var_1 ; $var_b = $var_2; }

	   $R = $var_r * 255;                  //RGB results = 0 to 255
	   $G = $var_g * 255;
	   $B = $var_b * 255;
	}
	$ret["r"] = floor($R);
	$ret["g"] = floor($G);
	$ret["b"] = floor($B);

	return $ret;
}

/*
 * http://www.paris-beyrouth.org/Des-titres-en-relief
 */
function image_estampage_alpha($im, $trait=1, $prof=1)
{
	$dec1 = floor($trait/2);
	$dec2 = ceil($trait/2);

	$image = _image_valeurs_trans($im, "estampage-$trait-$prof");
	if (!$image) return("");


	$x_i = $image["largeur"];
	$y_i = $image["hauteur"];
	$im = $image["fichier"];
	$dest = $image["fichier_dest"];
	$creer = $image["creer"];

	if ($creer) {
		$im = $image["fonction_imagecreatefrom"]($im);
		$im_ = imagecreatetruecolor($x_i, $y_i);
		@imagealphablending($im_, false);
		@imagesavealpha($im_,true);
		$color_t = ImageColorAllocateAlpha( $im_, 255, 255, 255 , 127 );
		imagefill ($im_, 0, 0, $color_t);

		for ($x = 0; $x < $x_i; $x++) {
			for ($y=0; $y < $y_i; $y++) {

				$rgb = ImageColorAt($im, $x, $y);
				$a = ($rgb >> 24) & 0xFF;

				$x1 = $x+$dec1;
				$y1 = $y+$dec1;
				if ($x1 < 0 OR $x1 >= $x_i) $x1 = $x;
				if ($y1 < 0 OR $y1 >= $y_i) $y1 = $y;
				$rgb1 = ImageColorAt($im, $x1, $y1);
				$a1 = ($rgb1 >> 24) & 0xFF;

				$x2 = $x-$dec2;
				$y2 = $y-$dec2;
				if ($x2 < 0 OR $x2 >= $x_i) $x2 = $x;
				if ($y2 < 0 OR $y2 >= $y_i) $y2 = $y;
				$rgb2 = ImageColorAt($im, $x2, $y2);
				$a2 = ($rgb2 >> 24) & 0xFF;

				$m = round((($a-$a1)+($a2-$a))*$prof);
				$m = max(min($m,127),-127);
				$m += 127;
				$color = ImageColorAllocateAlpha( $im_, $m, $m, $m , 0 );
				imagesetpixel ($im_, $x, $y, $color);
			}
		}
		_image_gd_output($im_,$image);
		imagedestroy($im_);
		imagedestroy($im);
	}

	return _image_ecrire_tag($image,array('src'=>$dest));
}



/**
 * http://www.paris-beyrouth.org/De-saturer-une-image-en-passant-en
 */
function image_saturer($im, $sat=1){
	$image = _image_valeurs_trans($im, "saturer-$sat");
	if (!$image) return("");

	$x_i = $image["largeur"];
	$y_i = $image["hauteur"];

	$im = $image["fichier"];
	$dest = $image["fichier_dest"];
	$creer = $image["creer"];

	if ($creer) {
		$im = $image["fonction_imagecreatefrom"]($im);

		$im_ = imagecreatetruecolor($x_i, $y_i);
		@imagealphablending($im_, false);
		@imagesavealpha($im_,true);
		$color_t = ImageColorAllocateAlpha( $im_, 255, 255, 255 , 127 );
		imagefill ($im_, 0, 0, $color_t);

		for ($x = 0; $x < $x_i; $x++) {
			for ($y=0; $y < $y_i; $y++) {

				$rgb = ImageColorAt($im, $x, $y);
				$a = ($rgb >> 24) & 0xFF;
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;

				if ($a < 127) {
					$hsv = image_rgb2hsv($r,$g,$b);
					$h = $hsv["h"];
					$s = $hsv["s"];
					$v = $hsv["v"];

					$s = $s * $sat;
					$s = min($s,1);

					$rgb = image_hsv2rgb($h,$s,$v);
					$r = $rgb["r"];
					$g = $rgb["g"];
					$b = $rgb["b"];

				}
				$color = ImageColorAllocateAlpha( $im_, $r, $g, $b , $a );
				imagesetpixel ($im_, $x, $y, $color);
			}
		}
		_image_gd_output($im_,$image);
		imagedestroy($im_);
		imagedestroy($im);
	}

	return _image_ecrire_tag($image,array('src'=>$dest));
}


/*
 * http://www.paris-beyrouth.org/Corriger-les-niveaux-des-images
 */
function image_niveaux_gris_auto($im, $limite=1000) {

	// $limite=1000: les nuances min et max representent 0,1% du total

	$image = _image_valeurs_trans($im, "niveaux_gris_auto-$limite");
	if (!$image) return("");

	$x_i = $image["largeur"];
	$y_i = $image["hauteur"];
	$im = $image["fichier"];
	$dest = $image["fichier_dest"];
	$creer = $image["creer"];

	if ($creer) {
		$im = $image["fonction_imagecreatefrom"]($im);

		// Calculer les poids des differentes nuances
		for ($x = 0; $x < $x_i; $x++) {
			for ($y=0; $y < $y_i; $y++) {

				$rgb = ImageColorAt($im, $x, $y);
				$a = ($rgb >> 24) & 0xFF;
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;

				$a = (127-$a) / 127;
				$a=1;

				$gris = round($a*($r+$g+$b) / 3);
				$r = round($a*$r);
				$g = round($a*$g);
				$b = round($a*$b);

				if (!isset($val_gris[$gris])) {
					$val_gris[$gris]=0;
				}
				$val_gris[$gris] ++;
			}
		}

		$total = $x_i * $y_i;

		for ($bas = 0; $somme_bas < $total/$limite; $bas++) {
			$somme_bas += $val_gris[$bas];
		}

		for ($haut = 255; $somme_haut < $total/$limite ; $haut--) {
			$somme_haut += $val_gris[$haut];
		}

		$courbe[0] = 0;
		$courbe[255] = 255;
		$courbe[$bas] = 0;
		$courbe[$haut] = 255;

		// Calculer le tableau des correspondances
		ksort($courbe);
		while (list($key, $val) = each($courbe)) {
			if ($key > 0) {
				$key1 = $key_old;
				$val1 = $val_old;
				$prop = ($val - $val1) / ($key-$key1);
				for ($i = $key1; $i < $key; $i++) {
					$valeur = round($prop * ($i - $key1) + $val1);
					$courbe[$i] = $valeur;
				}
				$key_old = $key;
				$val_old = $val;
			} else {
				$key_old = $key;
				$val_old = $val;
			}
		}

		// Appliquer les correspondances
		$im2 = imagecreatetruecolor($x_i, $y_i);
		@imagealphablending($im2, false);
		@imagesavealpha($im2,true);
		$color_t = ImageColorAllocateAlpha( $im2, 255, 255, 255 , 0 );
		imagefill ($im2, 0, 0, $color_t);

		for ($x = 0; $x < $x_i; $x++) {
			for ($y=0; $y < $y_i; $y++) {
				$rgb = ImageColorAt($im, $x, $y);
				$a = ($rgb >> 24) & 0xFF;
				$r = ($rgb >> 16) & 0xFF;
				$v = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;

				$r = $courbe[$r];
				$v = $courbe[$v];
				$b = $courbe[$b];

				$color = ImageColorAllocateAlpha( $im2, $r, $v, $b , $a );
				imagesetpixel ($im2, $x, $y, $color);
			}
		}
		_image_gd_output($im2,$image);
		imagedestroy($im2);
		imagedestroy($im);
	}

	return _image_ecrire_tag($image,array('src'=>$dest));
}

/*
 * http://www.paris-beyrouth.org/Creer-automatiquement-une
 */
function image_podpod($im, $coul='000000', $deb=0, $fin=70)
{
	include_spip("inc/filtres_images");
	$image = _image_valeurs_trans($im, "podpod-$coul-$deb-$fin","png");
	if (!$image) return("");

	$couleurs = couleur_hex_to_dec($coul);
	$dr= $couleurs["red"];
	$dv= $couleurs["green"];
	$db= $couleurs["blue"];

	$x_i = $image["largeur"];
	$y_i = $image["hauteur"];

	$im = $image["fichier"];
	$dest = $image["fichier_dest"];

	$creer = $image["creer"];

	if ($creer) {
		$im = $image["fonction_imagecreatefrom"]($im);

		$im_ = imagecreatetruecolor($x_i, $y_i);
		@imagealphablending($im_, false);
		@imagesavealpha($im_,true);
		$color_t = ImageColorAllocateAlpha( $im_, 255, 255, 255 , 127 );
		imagefill ($im_, 0, 0, $color_t);

		for ($x = 0; $x < $x_i; $x++) {
			for ($y=0; $y < $y_i; $y++) {

				$rgb = ImageColorAt($im, $x, $y);
				$a = ($rgb >> 24) & 0xFF;
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;

				$g = round(($r+$g+$b) / 3);

				if ($g >= $deb AND $g <= $fin) $color = ImageColorAllocateAlpha( $im_, $dr, $dv, $db , $a );
				else $color = ImageColorAllocateAlpha( $im_, 0, 0, 0 , 127 );

				imagesetpixel ($im_, $x, $y, $color);
			}
		}
		_image_gd_output($im2,$image);
		imagedestroy($im2);
		imagedestroy($im);
	}

	return _image_ecrire_tag($image,array('src'=>$dest));
}


/*
 * http://www.paris-beyrouth.org/Modifier-les-courbes-d-une-image
 */
function image_courbe($im, $couche, $courb="") {

	$numargs = func_num_args();
	$arg_list = func_get_args();
	$texte = $arg_list[0];
	for ($i = 1; $i < $numargs; $i++) {
		if (preg_match("#=#", $arg_list[$i])) {
			$nom_variable = substr($arg_list[$i], 0, strpos($arg_list[$i], "="));
			$val_variable = substr($arg_list[$i], strpos($arg_list[$i], "=")+1, strlen($arg_list[$i]));
			$courbe[$nom_variable] = $val_variable;
		}
	}

	$image = _image_valeurs_trans($im, "courbe-$couche-".serialize($courbe));
	if (!$image) return("");

	$x_i = $image["largeur"];
	$y_i = $image["hauteur"];

	$im = $image["fichier"];
	$dest = $image["fichier_dest"];
	$creer = $image["creer"];

	if ($creer) {
		$courbe[0] = 0;
		$courbe[255] = 255;

		ksort($courbe);
		while (list($key, $val) = each($courbe)) {
			if ($key > 0) {
				$key1 = $key_old;
				$val1 = $val_old;
				$prop = ($val - $val1) / ($key-$key1);
				for ($i = $key1; $i < $key; $i++) {
					$valeur = round($prop * ($i - $key1) + $val1);
					$courbe[$i] = $valeur;
				}
				$key_old = $key;
				$val_old = $val;
			} else {
				$key_old = $key;
				$val_old = $val;
			}
		}

		$im = $image["fonction_imagecreatefrom"]($im);
		$im_ = imagecreatetruecolor($x_i, $y_i);
		@imagealphablending($im_, false);
		@imagesavealpha($im_,true);
		$color_t = ImageColorAllocateAlpha( $im_, 255, 255, 255 , 0 );
		imagefill ($im_, 0, 0, $color_t);

		for ($x = 0; $x < $x_i; $x++) {
			for ($y=0; $y < $y_i; $y++) {
				$rgb = ImageColorAt($im, $x, $y);
				$a = ($rgb >> 24) & 0xFF;
				$r = ($rgb >> 16) & 0xFF;
				$v = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;

				if ($couche == "rvb" OR $couche == "r") $r = $courbe[$r];
				if ($couche == "rvb" OR $couche == "v") $v = $courbe[$v];
				if ($couche == "rvb" OR $couche == "b") $b = $courbe[$b];

				$color = ImageColorAllocateAlpha( $im_, $r, $v, $b , $a );
				imagesetpixel ($im_, $x, $y, $color);
			}
		}
		_image_gd_output($im_,$image);
		imagedestroy($im_);
		imagedestroy($im);
	}

	return _image_ecrire_tag($image,array('src'=>$dest));
}


/*
 * http://www.paris-beyrouth.org/Un-habillage-irregulier
 */
function image_float ($img, $align, $margin=10) {

	$image = _image_valeurs_trans($img, "float-$align", "php");
	if (!$image) return("");

	$w = $image["largeur"];
	$h = $image["hauteur"];
	$precision = round($h / 5);

	$im = $image["fichier"];
	$dest = $image["fichier_dest"];
	$creer = $image["creer"];

	$ret = "<div style='position: relative; float: $align; width: 0px; height: 0px;'><img src='$im' class='format_png' alt='' style='position: absolute; $align: 0px;' /></div>";

	if ($creer) {
		$nouveau = _image_valeurs_trans(image_reduire($im, 0, $precision),"");
		$im_n = $nouveau["fichier"];
		$x_i = $nouveau["largeur"];
		$y_i = $nouveau["hauteur"];
		$rapport = ($w / $x_i);

		$im_n = $image["fonction_imagecreatefrom"]($im_n);

		// une premiere passe
		// pour recuperer les valeurs
		for ($j = 0; $j < $y_i; $j++) {
			$transp = true;

			for ($i = 0; $i < $x_i && $transp; $i++) {

				if ($align == "right") $rgb = ImageColorAt($im_n, $i+1, $j);
				else $rgb = ImageColorAt($im_n, ($x_i - $i)-1, $j);
				$a = ($rgb >> 24) & 0xFF;

				if ($a > 125) $larg[$j] ++;
				else $transp = false;
				}
		}

		$larg[-1] = $w;
		$larg[$y_i] = $w;
		// une deuxieme passe
		// pour appliquer les valeurs
		// en utilisant les valeurs precedente et suivante
		for ($j = 0; $j < $y_i; $j++) {
			$reste = ($precision - $j);
			$haut_rest = $h - $haut_tot;
			$hauteur = round(($haut_rest) / $reste);
			$haut_tot = $haut_tot + $hauteur;
			$resultat = min($larg[$j-1],$larg[$j],$larg[$j+1]);

			$forme .= "\n<div style='float: $align; clear: $align; width: ".($margin+round(($w - ($resultat)*$rapport)))."px ; height: ".round($hauteur)."px; overflow: hidden;'></div>";
		}
		// Ajouter un div de plus en dessous
		$forme .= "\n<div style='float: $align; clear: $align; width: ".($margin+round(($w - ($resultat)*$rapport)))."px ; height: ".round($hauteur)."px; overflow: hidden;'></div>";

		// Sauvegarder le fichier
		$handle = fopen($dest, 'w');
		fwrite($handle, $forme);
		fclose($handle);

		$ret .= $forme;
	}
	else {
		$ret .= join(file($dest),"");
	}

	return $ret;
}


/*
 * http://www.paris-beyrouth.org/Tracer-les-contours-de
 */
function image_contour_alpha($im, $coul='000000', $trait=1)
{
	$image = _image_valeurs_trans($im, "contour-$coul-$trait", "png");
	if (!$image) return("");

	include_spip("inc/filtres_images");
	$couleurs = couleur_hex_to_dec($coul);
	$dr= $couleurs["red"];
	$dv= $couleurs["green"];
	$db= $couleurs["blue"];

	$x_i = $image["largeur"];
	$y_i = $image["hauteur"];

	$im = $image["fichier"];
	$dest = $image["fichier_dest"];

	$creer = $image["creer"];

	if ($creer) {
		$im = $image["fonction_imagecreatefrom"]($im);
		$im_ = imagecreatetruecolor($x_i, $y_i);
		@imagealphablending($im_, false);
		@imagesavealpha($im_,true);
		$color_t = ImageColorAllocateAlpha( $im_, 255, 255, 255 , 127 );
		imagefill ($im_, 0, 0, $color_t);

		for ($x = 0; $x < $x_i; $x++) {
			for ($y=0; $y < $y_i; $y++) {

				$rgb = ImageColorAt($im, $x, $y);
				$a = ($rgb >> 24) & 0xFF;

				$dif = false;
				$m = 0;
				$t = 0;
				for ($ix = -1*$trait/2; $ix <= $trait/2; $ix++) {
					for ($iy = -1*$trait/2; $iy <= $trait/2; $iy++) {
						$x2 = $x + $ix;
						$y2 = $y + $iy;
						if ($x2 >=0 AND $y2 >= 0 AND $x2 < $x_i AND $y2  < $y_i) {
						$t++;
							$rgb2 = ImageColorAt($im, $x2, $y2);
							$a2 = ($rgb2 >> 24) & 0xFF;
							$r2 = ($rgb2 >> 16) & 0xFF;
							$g2 = ($rgb2 >> 8) & 0xFF;
							$b2 = $rgb2 & 0xFF;

							if ($a != $a2) {
								$dx = min(abs($ix),abs($iy));
								$dy = max(abs($ix),abs($iy));
								if ($mem[$dx][$dy]) $d = $mem[$dx][$dy];
								else {
									$mem[$dx][$dy] = sqrt(($dx)*($dx)+($dy)*($dy));
									$d = $mem[$dx][$dy];
								}
								if ($d>0) {
									$m = $m + (abs($a2-$a) / $d);
								} else {
									$m = $m + 127;
								}
							}
						}
					}
				}
				$m = 127 - (($m / $t) * $trait);
				$m = min(max($m, 0), 127);

				$color = ImageColorAllocateAlpha( $im_, $dr, $dv, $db , round($m) );
				imagesetpixel ($im_, $x, $y, $color);
			}
		}
		_image_gd_output($im_,$image);
		imagedestroy($im_);
		imagedestroy($im);
	}

	return _image_ecrire_tag($image,array('src'=>$dest));
}



/*
 * http://www.paris-beyrouth.org/Welcome-to-Spip-City
 */
function image_sincity($im)
{
	$image = _image_valeurs_trans($im, "sincity");
	if (!$image) return("");

	$x_i = $image["largeur"];
	$y_i = $image["hauteur"];

	$im = $image["fichier"];
	$dest = $image["fichier_dest"];
	$creer = $image["creer"];

	if ($creer) {
		$im = $image["fonction_imagecreatefrom"]($im);

		$im_ = imagecreatetruecolor($x_i, $y_i);
		@imagealphablending($im_, false);
		@imagesavealpha($im_,true);
		$color_t = ImageColorAllocateAlpha( $im_, 255, 255, 255 , 127 );
		imagefill ($im_, 0, 0, $color_t);

		$tol = 0.05 ;
		for ($x = 0; $x < $x_i; $x++) {
			for ($y=0; $y < $y_i; $y++) {

				$rgb = ImageColorAt($im, $x, $y);
				$a = ($rgb >> 24) & 0xFF;
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;

				if ($a < 127) {
					$hsv = image_rgb2hsv($r,$g,$b);
					$h = $hsv["h"];
					$s = $hsv["s"];
					$v = $hsv["v"];

					if ($h < $tol OR $h > 1-$tol) {
						if ($h < $tol) {
							$dist = ($tol-$h);
						}
						else if ($h > 1-$tol) {
							$dist = ($h - (1-$tol));
						}
						$s = $s * ($dist/$tol);
						if ($s > 1) $s = 1;
						$h = 0;
					} else {
						$s = 0;
					}

					$v = 2*($v - 0.6) + 0.6;

					if ($v > 1) $v=1;
					if ($v < 0) $v =0;

					$rgb = image_hsv2rgb($h,$s,$v);
					$r = $rgb["r"];
					$g = $rgb["g"];
					$b = $rgb["b"];
				}
				$color = ImageColorAllocateAlpha( $im_, $r, $g, $b , $a );
				imagesetpixel ($im_, $x, $y, $color);				}
		}
		_image_gd_output($im_,$image);
		imagedestroy($im_);
		imagedestroy($im);
	}

	return _image_ecrire_tag($image,array('src'=>$dest));
}




/*
 * http://www.paris-beyrouth.org/Un-filtre-de-dispersion-graphique
 */
function image_dispersion($im, $masque, $h=5, $v=5, $pos="") {

	$nom = preg_replace("#\.(png|jpg|gif)$#", "", $masque);
	$nom = str_replace("/","-",$nom);

	$numargs = func_num_args();
	$arg_list = func_get_args();
	$texte = $arg_list[0];
	for ($i = 1; $i < $numargs; $i++) {
		if (preg_match("#=#", $arg_list[$i])) {
			$nom_variable = substr($arg_list[$i], 0, strpos($arg_list[$i], "="));
			$val_variable = substr($arg_list[$i], strpos($arg_list[$i], "=")+1, strlen($arg_list[$i]));
			$variable["$nom_variable"] = $val_variable;
			$defini["$nom_variable"] = 1;
		}
	}

	$image = valeurs_image_trans($im, "disp$nom-$h-$v$pos", "png");
	if (!$image) return("");

	$x_i = $image["largeur"];
	$y_i = $image["hauteur"];
	$im = $image["fichier"];
	$dest = $image["fichier_dest"];
	$creer = $image["creer"];

	if (strlen($pos) > 0) {
		$placer = true;
	}
	else $placer = false;

	if ($creer) {
		include_spip('inc/logos'); // bicoz presence image_reduire

		$masque = find_in_path($masque);
		$mask = valeurs_image_trans($masque,"");
		$im_m = $mask["fichier"];
		$x_m = $mask["largeur"];
		$y_m = $mask["hauteur"];

		$im2 = $mask["fonction_imagecreatefrom"]($masque);

		if ($placer) {
			// On fabriquer une version "agrandie" du masque,
			// aux dimensions de l'image source
			// et on "installe" le masque dans cette image
			// ainsi: aucun redimensionnement

			$dx = 0;
			$dy = 0;

			if ($defini["right"]) {
				$right = $variable["right"];
				$dx = ($x_i - $x_m) - $right;
			}
			if ($defini["bottom"]) {
				$bottom = $variable["bottom"];
				$dy = ($y_i - $y_m) - $bottom;
				}
			if ($defini["top"]) {
				$top = $variable["top"];
				$dy = $top;
			}
			if ($defini["left"]) {
				$left = $variable["left"];
				$dx = $left;
			}

			$im3 = imagecreatetruecolor($x_i, $y_i);
			@imagealphablending($im3, false);
			@imagesavealpha($im3,true);
			$color_t = ImageColorAllocateAlpha( $im3, 128, 128, 128 , 0 );
			imagefill ($im3, 0, 0, $color_t);

			imagecopy ( $im3, $im2, $dx, $dy, 0, 0, $x_m, $y_m);

			imagedestroy($im2);
			$im2 = imagecreatetruecolor($x_i, $y_i);
			@imagealphablending($im2, false);
			@imagesavealpha($im2,true);

			imagecopy ( $im2, $im3, 0, 0, 0, 0, $x_i, $y_i);
			imagedestroy($im3);
			$x_m = $x_i;
			$y_m = $y_i;
		}

		$rapport = $x_i / $x_m;
		if (($y_i / $y_m) < $rapport ) {
			$rapport = $y_i / $y_m;
		}

		$x_d = ceil($x_i / $rapport);
		$y_d = ceil($y_i / $rapport);

		if ($x_i < $x_m OR $y_i < $y_m) {
			$x_dest = $x_i;
			$y_dest = $y_i;
			$x_dec = 0;
			$y_dec = 0;
		} else {
			$x_dest = $x_m;
			$y_dest = $y_m;
			$x_dec = round(($x_d - $x_m) /2);
			$y_dec = round(($y_d - $y_m) /2);
		}

		$nouveau = valeurs_image_trans(image_reduire($im, $x_d, $y_d),"");
		$im_n = $nouveau["fichier"];
		$im = $nouveau["fonction_imagecreatefrom"]($im_n);
		$im_ = imagecreatetruecolor($x_dest, $y_dest);
		@imagealphablending($im_, false);
		@imagesavealpha($im_,true);
		$color_t = ImageColorAllocateAlpha( $im_, 255, 255, 255 , 127 );
		imagefill ($im_, 0, 0, $color_t);

		for ($x = 0; $x < $x_dest; $x++) {
			for ($y=0; $y < $y_dest; $y++) {

				$rgb2 = ImageColorAt($im2, $x+$x_dec, $y+$y_dec);
				$a2 = ($rgb2 >> 24) & 0xFF;
				$r2 = ($rgb2 >> 16) & 0xFF;
				$g2 = ($rgb2 >> 8) & 0xFF;
				$b2 = $rgb2 & 0xFF;

				$g2 = ($r2+$g2+$b2)/3;
				$val = ($g2-127)/127;
				$xd = $x - ($val*$h);
				$yd = $y + ($val*$v);

				$xd = max(0,$xd);
				$yd = max(0,$yd);
				if ($xd > $x_dest - $x_dec - 1) $xd = $x_dest - $x_dec - 1;
				if ($yd > $y_dest - $y_dec - 1) $yd = $y_dest - $y_dec - 1;

				$rgb = ImageColorAt($im, $xd+$x_dec, $yd+$y_dec);
				$a = ($rgb >> 24) & 0xFF;
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;

				$color = ImageColorAllocateAlpha( $im_, $r, $g, $b, $a );
				imagesetpixel ($im_, $x, $y, $color);				}
		}
		_image_gd_output($im_,$image);
		imagedestroy($im2);
		imagedestroy($im_);
		imagedestroy($im);
	}

	return _image_ecrire_tag($image,array('src'=>$dest));
}

/* ------------------ Autres fonctions ---------------------------- */

/*
 *
 * D'apres http://www.easyrgb.com/math.php
 */
function image_rgb2hsl($R,$G,$B) {
	$var_R = ( $R / 255 );                     //Where RGB values = 0 � 255
	$var_G = ( $G / 255 );
	$var_B = ( $B / 255 );

	$var_Min = min( $var_R, $var_G, $var_B );    //Min. value of RGB
	$var_Max = max( $var_R, $var_G, $var_B );    //Max. value of RGB
	$del_Max = $var_Max - $var_Min ;            //Delta RGB value

	$L = ( $var_Max + $var_Min ) / 2;

	if ( $del_Max == 0 )                     //This is a gray, no chroma...
		{
		   $H = 0;                                //HSL results = 0 � 1
		   $S = 0;
		}
	else                                    //Chromatic data...
		{
		   if ( $L < 0.5 ) $S = $del_Max / ( $var_Max + $var_Min );
		   else $S = $del_Max / ( 2 - $var_Max - $var_Min );

		   $del_R = ( ( ( $var_Max - $var_R ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
		   $del_G = ( ( ( $var_Max - $var_G ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
		   $del_B = ( ( ( $var_Max - $var_B ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;

		   if      ( $var_R == $var_Max ) $H = $del_B - $del_G;
		   else if ( $var_G == $var_Max ) $H = ( 1 / 3 ) + $del_R - $del_B;
		   else if ( $var_B == $var_Max ) $H = ( 2 / 3 ) + $del_G - $del_R;

		   if ( $H < 0 ) ; $H += 1;
		   if ( $H > 1 ) ; $H -= 1;
		}

	$ret["h"] = $H;
	$ret["s"] = $S;
	$ret["l"] = $L;

	return $ret;
}

function image_hue2rgb($v1, $v2, $vH) {
   if ( $vH < 0 ) $vH += 1;
   if ( $vH > 1 ) $vH -= 1;
   if ( ( 6 * $vH ) < 1 ) return ( $v1 + ( $v2 - $v1 ) * 6 * $vH );
   if ( ( 2 * $vH ) < 1 ) return ( $v2 );
   if ( ( 3 * $vH ) < 2 ) return ( $v1 + ( $v2 - $v1 ) * ( ( 2 / 3 ) - $vH ) * 6 );
   return ( $v1 );
}

function image_hsl2rgb($H,$S,$L) {
	if ( $S == 0 )                       //HSL values = 0 � 1
		{
		   $R = $L * 255;                      //RGB results = 0 � 255
		   $G = $L * 255;
		   $B = $L * 255;
		}
	else
		{
		   if ( $L < 0.5 ) $var_2 = $L * ( 1 + $S );
		   else           $var_2 = ( $L + $S ) - ( $S * $L );

		   $var_1 = 2 * $L - $var_2;

		   $R = 255 * image_hue2rgb( $var_1, $var_2, $H + ( 1 / 3 ) );
		   $G = 255 * image_hue2rgb( $var_1, $var_2, $H );
		   $B = 255 * image_hue2rgb( $var_1, $var_2, $H - ( 1 / 3 ) );
		}

	$ret["r"] = floor($R);
	$ret["g"] = floor($G);
	$ret["b"] = floor($B);

	return $ret;
}

//
// filtre tire de http://reflection.corephp.co.uk
// adapte par Yohann Prigent
//
function image_reflechir($im, $hauteur=0.5, $alphastart=80, $alphaend=0, $red=127, $green=127, $blue=127){
	include_spip("inc/filtres_images");
	//if(!function_exists('imagelayereffect'))
	//	return;
	$image = _image_valeurs_trans($im, "relechir-$hauteur-$alphastart-$alphaend", "png");
	if (!$image) return("");
	$width = $image["largeur"];
	$height = $image["hauteur"];
	$im = $image["fichier"];
	$dest = $image["fichier_dest"];
	$creer = $image["creer"];
	$new_height = $height * $hauteur;
	if($creer) {
		if($hauteur > $height)
			$hauteur = $height;
		$source = $image["fonction_imagecreatefrom"]($im);
		//	We'll store the final reflection in $output. $buffer is for internal use.
		$resultat = imagecreatetruecolor($width, $new_height + $height);
		$output = imagecreatetruecolor($width, $new_height);
		$buffer = imagecreatetruecolor($width, $new_height);
		//  Save any alpha data that might have existed in the source image and disable blending
		imagesavealpha($source, true);
		// alpha for output
		imagesavealpha($output, true);
		imagealphablending($output, false);
		// alpha for the buffer
		imagesavealpha($buffer, true);
		imagealphablending($buffer, false);
		// and alpha for the final result
		imagesavealpha($resultat, true);
		imagealphablending($resultat, false);
		//	Copy the bottom-most part of the source image into the output
		imagecopy($output, $source, 0, 0, 0, $height - $new_height, $width, $new_height);
		//	Rotate and flip it (strip flip method)
		for ($y = 0; $y < $new_height; $y++)
		{
			imagecopy($buffer, $output, 0, $y, 0, $new_height - $y - 1, $width, 1);
		}
		$output = $buffer;
		$alpha_length = abs($alphastart - $alphaend);
		imagelayereffect($output, IMG_EFFECT_OVERLAY);
		for ($y = 0; $y <= $new_height; $y++)
		{
			//  Get % of reflection height
			$pct = $y / $new_height;
			//  Get % of alpha
			if ($alphastart > $alphaend)
			{
				$alpha = (int) ($alphastart - ($pct * $alpha_length));
			}
			else
			{
				$alpha = (int) ($alphastart + ($pct * $alpha_length));
			}
			//  Rejig it because of the way in which the image effect overlay works
			$final_alpha = 127 - $alpha;
			imagefilledrectangle($output, 0, $y, $width, $y, imagecolorallocatealpha($output, $red, $green, $blue, $final_alpha));
		}
		// now, source with effect
		imagecopy($resultat, $source, 0, 0, 0, 0, $width, $height);
		imagecopy($resultat, $output, 0, $height,0, 0, $width, $height);
		$image["fonction_image"]($resultat, "$dest");
		imagedestroy($buffer);
		imagedestroy($resultat);
	}
	$class = $image["class"];
	if (strlen($class) > 1) $tags=" class='$class'";
	$tags = "$tags alt='".$image["alt"]."'";
	$style = $image["style"];
	if (strlen($style) > 1) $tags="$tags style='$style'";
	return "<img src='$dest'$tags />";
}

/**
 * Filtre négatif
 * @param unknown_type $im
 */
function image_negatif($im){

	$image = _image_valeurs_trans($im, "negate-");

	if (!$image) return("");

	$x_i = $image["largeur"];
	$y_i = $image["hauteur"];

	$im = $image["fichier"];
	$dest = $image["fichier_dest"];
	$creer = $image["creer"];

	if ($creer){
		// on definit les images sources et destination
		$im = $image["fonction_imagecreatefrom"]($im);
		$im_ = imagecreatetruecolor($x_i, $y_i);
		@imagealphablending($im_, false);
		@imagesavealpha($im_,true);
		$color_t = imageColorAllocateAlpha( $im_, 128, 128, 128 , 0 );
		imagefill($im_, 0, 0, $color_t);
		imagecopy($im_, $im, 0, 0, 0, 0, $x_i, $y_i);

		/**
		 * on donne l'effet negatif
		 * Si on a une version récente de PHP on passe par imagefilter directement
		 * Sinon on procède à l'analyse pixel par pixel
		 */
		if(function_exists('imagefilter')){
	        imagefilter($im_, IMG_FILTER_NEGATE);
	    }else{
		    for($x = 0; $x < imagesx($im_); ++$x){
		        for($y = 0; $y < imagesy($im_); ++$y){
		            $index = imagecolorat($im_, $x, $y);
		            $rgb = imagecolorsforindex($im_,$index);
		            $color = imagecolorallocatealpha($im_, 255 - $rgb['red'], 255 - $rgb['green'], 255 - $rgb['blue'],$rgb['alpha']);
		            imagesetpixel($im_, $x, $y, $color);
		        }
		    }
	    }
		_image_gd_output($im_,$image);
		imagedestroy($im_);
		imagedestroy($im);
	}

	return _image_ecrire_tag($image,array('src'=>$dest));
}

/**
 * Un filtre permettant de dire à peu près si une image est monochrome
 * On réduit l'image de départ à un carré de 20x20 px et on analyse chaque couleur de chaque pixel
 * Si on a moins de 13 couleurs différentes, on considère que l'image est plus ou moins monochrome
 * 
 * @param unknown_type $img
 * @param int $largeur
 * @param int $seuil le nombre de couleur minimum d'une image non considérée comme monochrome
 * @return bool true|false
 */
function image_monochrome($img,$largeur=20,$seuil=13){
	$cache = _image_valeurs_trans($img, "coul-monochrome-$largeur", "txt");
	if (!$cache) return("");
	
	$x_i = $cache["largeur"];
	$y_i = $cache["hauteur"];
	
	$creer = $cache["creer"];
	
	$fichier = $cache["fichier"];	
	$dest = $cache["fichier_dest"];
	
	if ($creer) {
		if (@file_exists($fichier)) {
			$thumb = imagecreate($largeur, $largeur);
			$source = $cache["fonction_imagecreatefrom"]($fichier);
			imagepalettetotruecolor($source);
			imagecopyresized($thumb, $source, 0, 0, 0, 0, $largeur, $largeur, $x_i, $y_i);
			for ($x = 0; $x < $largeur; $x++) {
				for ($y=0; $y < $largeur; $y++) {
					$rgb[] = imagecolorat($thumb, $x, $y);
				}
			}
			$rgb = array_unique($rgb);
		}
	}
	if(count($rgb) < $seuil){
		return false;
	}
	else 
		return true;
}

/* une fonction importee du plugin fotoremix par Yohann Prigent 
*
* remplace aussi cette mise contribution :
* http://www.spip-contrib.net/Filtre-image_superpose
* 
* 
* @param unknown_type $img
* @param unknown_type $masque
* @param string $h alignement horizontal
* @param string $v alignement vertical
* @return 
*/
function image_merge($im, $masque, $h='left', $v='top'){
	$image = _image_valeurs_trans($im, "merge-$masque-$h-$v");

	if (!$image) return("");

	$x_i = $image["largeur"];
	$y_i = $image["hauteur"];

	$im = $image["fichier"];
	$dest = $image["fichier_dest"];
	$creer = $image["creer"];

	if ($creer){
		// init
		$enfoncement = 2/3;
    $masque = find_in_path($masque);

		// on definit l'image de masque
		$im_masque = @imagecreatefrompng($masque);
		//imagealphablending($im_masque, false);
		//imagesavealpha($im_masque, true);
		$x_masque = 0;
		$y_masque = 0;

		// on definit les images sources et destination
		// l'image de destination est plus grande de la moitie du masque
		$im = $image["fonction_imagecreatefrom"]($im);
		$im_ = @imagecreatetruecolor($x_i + $x_masque, $y_i + $y_masque);

		// reglage des options de transparence
		imagealphablending($im_, false);
		imagesavealpha($im_, true);
		
		// on remplit l'image avec la couleur transparente
		imagefilledrectangle($im_, 0, 0, $x_i + $x_masque, $y_i + $y_masque, imagecolorallocatealpha($im_, 255, 255, 255, 127));

		// estimation du decalage en fonction de $h et $hori
		if ($h == 'center'){
			$dest_x = 0;
			$dest_x_masque = (imagesx($im_) - imagesx($im_masque))/2;
		}
		elseif ($h == 'right'){
			$dest_x = 0;
			$dest_x_masque = imagesx($im_) - imagesx($im_masque);
		}
		else { // left par defaut
			$dest_x = $x_masque;
			$dest_x_masque = 0;
		}

		if ($v == 'middle'){
			$dest_y = 0;
			$dest_y_masque = ($dest_y_masque = imagesy($im_) - imagesy($im_masque))/2;
		}
		elseif ($v == 'bottom'){
			$dest_y = 0;
			$dest_y_masque = imagesy($im_) - imagesy($im_masque);
		}
		else { // top par defaut
			$dest_y = $y_masque;
			$dest_y_masque = 0;
		}

		// on copie l'image source
		imagecopymerge($im_, $im, 0, 0, 0, 0, $x_i, $y_i, 100);
		
		// on copie le masque
		imagealphablending($im_, true);
		imagecopy($im_, $im_masque, $dest_x_masque, $dest_y_masque, 0, 0, imagesx($im_masque), imagesy($im_masque));

		_image_gd_output($im_,$image);
		imagedestroy($im_masque);
		imagedestroy($im_);
		imagedestroy($im);
	}

	return _image_ecrire_tag($image,array('src'=>$dest));
}

/**
 * Remplacer l'alpha d'une image avec une autre couleur
 */
function image_remplacer_alpha($im, $couleur, $qualite=85){
	$image = _image_valeurs_trans($im, "remplacer_alpha-$couleur");
	if (!$image) return("");
	
	$x_i = $image["largeur"];
	$y_i = $image["hauteur"];

	$im = $image["fichier"];
	$dest = $image["fichier_dest"];
	$creer = $image["creer"];

	if ($creer) {
		$im = $image["fonction_imagecreatefrom"]($im);
		
		@imagealphablending($im, true);
		@imagesavealpha($im,true);
		
		$im_ = imagecreatetruecolor($x_i, $y_i);
		
		include_spip("inc/filtres_images");
		$couleurs = couleur_hex_to_dec($couleur);
		
		$dr = $couleurs["red"];
		$dv = $couleurs["green"];
		$db = $couleurs["blue"];
		$color = imagecolorallocatealpha($im_, $dr, $dv, $db, 0);
		
		imagefilledrectangle($im_, 0, 0, $x_i, $y_i, $color);
		@imagealphablending($im_, true);
		@imagesavealpha($im_,true);
		
		imagecopy($im_, $im, 0, 0, 0, 0, $x_i, $y_i);
		_image_gd_output($im_,$image, $qualite);
	}

	return _image_ecrire_tag($image,array('src'=>$dest));
}
?>
