<?php


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
 * http://www.paris-beyrouth.org/Une-palette-de-couleurs
 */
function couleur_chroma ($coul, $num) {

	$pos = substr($num, 0, strpos($num, "/")) -  1;
	$tot = substr($num, strpos($num, "/")+1, strlen($num));
	
	include_ecrire("filtres");
	$couleurs = couleur_hex_to_dec($coul);
	$r= $couleurs["red"];
	$g= $couleurs["green"];
	$b= $couleurs["blue"];

	$hsv = image_rgb2hsv($r,$g,$b);
	$h = $hsv["h"];
	$s = $hsv["s"];
	$v = $hsv["v"];
	
	$h = $h + (1/$tot)*$pos;
	if ($h > 1) $h = $h - 1;
					
	$rgb = image_hsv2rgb($h,$s,$v);
	$r = $rgb["r"];
	$g = $rgb["g"];
	$b = $rgb["b"];
	
	$couleurs = couleur_dec_to_hex($r, $g, $b);
	
	return $couleurs;
}


/*
 * http://www.paris-beyrouth.org/Des-titres-en-relief
 */
function image_estampage_alpha($im, $trait=1, $prof=1)
{
	$dec1 = floor($trait/2);
	$dec2 = ceil($trait/2);

	$image = image_valeurs_trans($im, "estampage-$trait-$prof");
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
		$image["fonction_image"]($im_, "$dest");
	}

	$class = $image["class"];
	if (strlen($class) > 1) $tags=" class='$class'";
	$tags = "$tags alt='".$image["alt"]."'";
	$style = $image["style"];
	if (strlen($style) > 1) $tags="$tags style='$style'";
	
	return "<img src='$dest'$tags />";
}



/*
 * http://www.paris-beyrouth.org/De-saturer-une-image-en-passant-en
 */
function image_saturer($im, $sat=1)
{
	$image = image_valeurs_trans($im, "saturer-$sat");
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
					
					$s = $s * $sat;									$s = min($s,1);
					
					$rgb = image_hsv2rgb($h,$s,$v);
					$r = $rgb["r"];
					$g = $rgb["g"];
					$b = $rgb["b"];

				}
				$color = ImageColorAllocateAlpha( $im_, $r, $g, $b , $a );
				imagesetpixel ($im_, $x, $y, $color);				}
		}		
		$image["fonction_image"]($im_, "$dest");		}

	$class = $image["class"];
	if (strlen($class) > 1) $tags=" class='$class'";
	$tags = "$tags alt='".$image["alt"]."'";
	$style = $image["style"];
	
	return "<img src='$dest'$tags />";
}


/*
 * http://www.paris-beyrouth.org/Corriger-les-niveaux-des-images
 */
function image_niveaux_gris_auto($im, $limite=1000) {

	// $limite=1000: les nuances min et max representent 0,1% du total
	
	$image = image_valeurs_trans($im, "niveaux_gris_auto-$limite");
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

		$image["fonction_image"]($im2, "$dest");
		imagedestroy($im2);
		imagedestroy($im);
	}

	$class = $image["class"];
	if (strlen($class) > 1) $tags=" class='$class'";
	$tags = "$tags alt='".$image["alt"]."'";
	$style = $image["style"];
	if (strlen($style) > 1) $tags="$tags style='$style'";
	
	return "<img src='$dest'$tags />";
}

/*
 * http://www.paris-beyrouth.org/Creer-automatiquement-une
 */
function image_podpod($im, $coul='000000', $deb=0, $fin=70)
{
	include_ecrire("filtres");
	$image = image_valeurs_trans($im, "podpod-$coul-$deb-$fin","png");
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
		$image["fonction_image"]($im_, "$dest");	
	}

	$class = $image["class"];
	if (strlen($class) > 1) $tags=" class='$class'";
	$tags = "$tags alt='".$image["alt"]."'";
	$style = $image["style"];
	
	return "<img src='$dest'$tags />";
}


/*
 * http://www.paris-beyrouth.org/Modifier-les-courbes-d-une-image
 */
function image_courbe($im, $couche, $courb="") {

	$numargs = func_num_args();
	$arg_list = func_get_args();
	$texte = $arg_list[0];
	for ($i = 1; $i < $numargs; $i++) {
		if (ereg("\=", $arg_list[$i])) {
			$nom_variable = substr($arg_list[$i], 0, strpos($arg_list[$i], "="));
			$val_variable = substr($arg_list[$i], strpos($arg_list[$i], "=")+1, strlen($arg_list[$i]));
			$courbe[$nom_variable] = $val_variable;
		}
	}

	$image = image_valeurs_trans($im, "courbe-$couche-".serialize($courbe));
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

		$image["fonction_image"]($im_, "$dest");
		imagedestroy($im_);
		imagedestroy($im);
	}

	$class = $image["class"];
	if (strlen($class) > 1) $tags=" class='$class'";
	$tags = "$tags alt='".$image["alt"]."'";
	$style = $image["style"];
	if (strlen($style) > 1) $tags="$tags style='$style'";
	return "<img src='$dest'$tags />";
}


/*
 * http://www.paris-beyrouth.org/Un-habillage-irregulier
 */
function image_float ($img, $align, $margin=10) {

	$image = image_valeurs_trans($img, "float-$align", "php");
	if (!$image) return("");

	$w = $image["largeur"];
	$h = $image["hauteur"];
	$precision = round($h / 5);
	
	$im = $image["fichier"];
	$dest = $image["fichier_dest"];
	$creer = $image["creer"];

	$ret .= "<div style='position: relative; float: $align; width: 0px; height: 0px;'><img src='$im' class='format_png' alt='' style='position: absolute; $align: 0px;' /></div>";

	if ($creer) {
		$nouveau = image_valeurs_trans(image_reduire($im, 0, $precision),"");
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
	$image = image_valeurs_trans($im, "contour-$coul-$trait", "png");
	if (!$image) return("");

	include_ecrire("filtres");
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
		$image["fonction_image"]($im_, "$dest");
	}

	$class = $image["class"];
	if (strlen($class) > 1) $tags=" class='$class'";
	$tags = "$tags alt='".$image["alt"]."'";
	$style = $image["style"];
	if (strlen($style) > 1) $tags="$tags style='$style'";
	
	return "<img src='$dest'$tags />";
}



/*
 * http://www.paris-beyrouth.org/Welcome-to-Spip-City
 */
function image_sincity($im)
{
	$image = image_valeurs_trans($im, "sincity");
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
		$image["fonction_image"]($im_, "$dest");		}

	$class = $image["class"];
	if (strlen($class) > 1) $tags=" class='$class'";
	$tags = "$tags alt='".$image["alt"]."'";
	$style = $image["style"];
	
	return "<img src='$dest'$tags />";
}




/* ------------------ Autres fonctions ---------------------------- */

/*
 * autorise les filtres images sur les chemins.
 * #CHEMIN{fichier}|en_image|image_sepia{14579c}
 */
function en_image($url, $alt=''){
	return 	"<img src='". $url ."' alt='". $alt ."' />";
}



/*
 * Sature (ou desature) une couleur
 */
function couleur_saturer ($coul, $val=1.2) {
	
	include_ecrire("filtres");
	$couleurs = couleur_hex_to_dec($coul);
	$r= $couleurs["red"];
	$g= $couleurs["green"];
	$b= $couleurs["blue"];

	$hsv = image_rgb2hsv($r,$g,$b);
	$h = $hsv["h"];
	$s = $hsv["s"];
	$v = $hsv["v"];
	
	$s = $s * $val;
	if ($s > 1) $s = 1;
					
	$rgb = image_hsv2rgb($h,$s,$v);
	$r = $rgb["r"];
	$g = $rgb["g"];
	$b = $rgb["b"];
	
	$couleurs = couleur_dec_to_hex($r, $g, $b);
	
	return $couleurs;
}


/*
 *
 * Affiche un tableau avec les couleurs de l'arbre chromatique
 * dont le d&eacute;coupage est pass&eacute; en param&egrave;tre.
 */
function couleur_tableau_chroma($coul, $tot=6, $debut=1, $fin=0){
	
	include_ecrire("filtres");
	if($fin==0) $fin = $tot;
	
	$retour = "<div style='width:300px;'>\n";
	$retour .= "<div style='background-color:#$coul; width:50px;float:left;'>$coul</div>\n";
	for($pos = $debut; $pos<=$fin; $pos++){
		$cc = couleur_chroma($coul, "$pos/$tot");
		$retour .= "<div style='background-color:#$cc; width:50px;float:left;'>$cc</div>\n";
	}
	$retour .= "</div>\n";
	
	return $retour;
} 
 
?>
