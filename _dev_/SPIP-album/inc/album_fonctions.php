<?php
/*	*********************************************************************
	*
	* Copyright (c) 2007
	* Xavier Burot
	*
	* SPIP-ALBUM : Programme d'affichage de photos
	*
	* Fichier : album_fonctions.php
	*
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	*
	*********************************************************************
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

//
// -- fonction specifique pour afficher images locales ------------------
// Balise #IMGLOCAL - Merci à Triton-pointcentral pour ce code.
//
function balise_IMGLOCAL($p) {
	if ($p->param && !$p->param[0][0]) {
		$p->code = calculer_liste ($p->param[0][1],
						$p->descr,
						$p->boucles,
						$p->id_boucle);
		$alt =  calculer_liste ($p->param[0][2],
					$p->descr,
					$p->boucles,
					$p->id_boucle);
		// autres filtres
		array_shift($p->param);
	}

	// recherche du chemin de l'image (comme #CHEMIN)
	$p->code = 'find_in_path(' . $p->code . ')';
	// passage en image
	$p->code = '"<img src=\'".' . $p->code . '."\' alt=\'".' . $alt . '."\' />"';

	#$p->interdire_scripts = true;
	return $p;
}

//
// -- fonction specifique pour afficher un copyright dans une image -----
// Filtre |image_watermark - D'après le programme proposé par Visualight
// Cf. http://www.asp-php.net/scripts/asp-php/watermark-2.php
//
function image_watermark($im) {
	include_spip('inc/filtres_images');

	if (!function_exists('lire_config')) {
		tester_variable('watermarktype', 'none');
		tester_variable('watermarkalignh', 'center');
		tester_variable('watermarkalignv', 'center');
		tester_variable('watermarkmargin', '10');
		tester_variable('watermarkopacity', '20');
		tester_variable('watermarktext', 'Copyright (c) '.date('Y').' '.$GLOBALS['meta']['adresse_site']);
		tester_variable('watermarkfont', '3');
		tester_variable('watermarkshadow', 'yes');
		tester_variable('watermarkcolor', 'FFFFFF');
	} else {
		tester_variable('watermarktype', lire_config('album/watermarktype','none'));
		tester_variable('watermarkalignh', lire_config('album/watermarkalignh','center'));
		tester_variable('watermarkalignv', lire_config('album/watermarkalignv','center'));
		tester_variable('watermarkmargin', lire_config('album/watermarkmargin','10'));
		tester_variable('watermarkopacity', lire_config('album/watermarkopacity','20'));
		tester_variable('watermarktext', lire_config('album/watermarktext','Copyright (c) '.date('Y').' '.$GLOBALS['meta']['adresse_site']));
		tester_variable('watermarkfont', lire_config('album/watermarkfont','3'));
		tester_variable('watermarkshadow', lire_config('album/watermarkshadow','yes'));
		tester_variable('watermarkcolor', lire_config('album/watermarkcolor','FFFFFF'));
	}

	global $watermarktype;
	global $watermarkalignh;
	global $watermarkalignv;
	global $watermarkmargin;
	global $watermarkopacity;
	global $watermarktext;
	global $watermarkfont;
	global $watermarkshadow;
	global $watermarkcolor;

	if ($watermarktype == 'none') $mark = 'wmark';
	if ($watermarktype == 'text') $mark = 'wmarkt';
	if ($watermarktype == 'image') $mark = 'wmarki';

	$image = image_valeurs_trans($im, $mark);
	if (!$image) return("");

      $x_i = $image["largeur"];
      $y_i = $image["hauteur"];

	$im = $image["fichier"];
	$dest = $image["fichier_dest"];

	$creer = $image["creer"];

	if ($creer) {
		$im = $image["fonction_imagecreatefrom"]($im);

		if ($watermarktype == 'image') {
			// WATERMARK_IMAGE_FILE
			// Chemin vers le fichier image employé comme watermark (Valeur possible : chemin absolu ou relatif).
			if (!defined('WATERMARK_IMAGE_FILE')) { // Verifie si cette variable n'a pas ete deja definie.
				define('WATERMARK_IMAGE_FILE', _DIR_PLUGIN_ALBUM.'img_pack/copyright.png');
			}

			$masque = find_in_path(WATERMARK_IMAGE_FILE);
			$mask = image_valeurs_trans($masque,"");

			if (!is_array($mask)) return("");
			$im_m = $mask["fichier"];
			$x_m = $mask["largeur"];
			$y_m = $mask["hauteur"];

			$im1 = $mask["fonction_imagecreatefrom"]($masque);
			if ($mask["format_source"] == "gif" AND function_exists('ImageCopyResampled')) {
				$im1_ = imagecreatetruecolor($x_m, $y_m);
				// Si un GIF est transparent,
				// fabriquer un PNG transparent
				// Conserver la transparence
				if (function_exists("imageAntiAlias")) imageAntiAlias($im1_,true);
				@imagealphablending($im1_, false);
				@imagesavealpha($im1_,true);
				@ImageCopyResampled($im1_, $im1, 0, 0, 0, 0, $x_m, $y_m, $x_m, $y_m);
				imagedestroy($im1);
				$im1 = $im1_;
			}

		        if ($im1) {
				$watermark_y = $watermarkmargin;
				if ($watermarkalignv == 'top') {
					$watermark_y = $watermarkmargin;
				}
					elseif ($watermarkalignv == 'bottom') {
		                		$watermark_y = $y_i - $y_m - $watermarkmargin;
	        		    	}
		            			elseif ($watermarkalignv == 'center') {
			        	        	$watermark_y = (int)($y_i / 2 - $y_m / 2);
	        			    	}
	        	    	$watermark_x = $watermarkmargin;
				if ($watermarkalignh == 'left') {
	       	        		$watermark_x = $watermarkmargin;
				}
					elseif ($watermarkalignh == 'right') {
			                	$watermark_x = $x_i - $x_m - $watermarkmargin;
					}
	            			elseif ($watermarkalignh == 'center') {
	                			$watermark_x = (int)($x_i / 2 - $x_m / 2);
			            	}
			}
			imagecopymerge($im,$im1, $watermark_x, $watermark_y, 0, 0, $x_m, $y_m, $watermarkopacity);
			imagedestroy($im1);
		}
		elseif ($watermarktype == 'text') {

		      $color = $watermarkcolor;
		      $red = hexdec(substr($color, 0, 2));
		      $green = hexdec(substr($color, 2, 2));
		      $blue = hexdec(substr($color, 4, 2));
		      $text_color = imagecolorallocate($im, $red, $green, $blue);
		      $shadow_color = imagecolorallocate($im, 0, 0, 0);
		      $text_height = imagefontheight($watermarkfont);
		      $text_width = strlen($watermarktext) * imagefontwidth($watermarkfont);

		      $watermark_y = $watermarkmargin;
			if ($watermarkalignv == 'top') {
				$watermark_y = $watermarkmargin;
				}
        			elseif ($watermarkalignv == 'bottom') {
					$watermark_y = $y_i - $text_height - $watermarkmargin;
					}
					elseif ($watermarkalignv == 'center') {
						$watermark_y = (int)($y_i / 2 - $text_height / 2);
						}

		      $watermark_x = $watermarkmargin;
		      if ($watermarkalignh == 'left') {
				$watermark_x = $watermarkmargin;
		        	}
		        	elseif ($watermarkalignh == 'right') {
					$watermark_x = $x_i - $text_width - $watermarkmargin;
					}
		        		elseif ($watermarkalignh == 'center') {
		            			$watermark_x = (int)($x_i / 2 - $text_width / 2);
		        		}

		      if ($watermarkshadow == 'yes') {
				imagestring($im, $watermarkfont, $watermark_x + 1, $watermark_y + 1, $watermarktext, $shadow_color);
			}
			imagestring($im, $watermarkfont, $watermark_x, $watermark_y, $watermarktext, $text_color);
		}

		$image["fonction_image"]($im, "$dest");
		imagedestroy($im);
	}

	$x_dest = largeur($dest);
	$y_dest = hauteur($dest);

	return image_ecrire_tag($image,array('src'=>$dest,'width'=>$x_dest,'height'=>$y_dest));
}

?>