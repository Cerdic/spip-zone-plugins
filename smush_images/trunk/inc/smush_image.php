<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function inc_smush_image_dist($im) {
	if (!_IS_BOT) {
		if (defined('_SMUSH_API') && _SMUSH_API) {
			$image = _image_valeurs_trans($im, 'smush');
			$im = $image['fichier'];
			if (!file_exists($im)) {
				return $im;
			}

			// L'adresse de l'API que l'on utilise
			$url_smush = 'http://api.resmush.it/ws.php';

			// On ajoute les paramètres nécessaires pour l'API
			$url_smush_finale = parametre_url($url_smush, 'img', url_absolue($im));
			spip_log("SMUSH : recuperation du contenu de $url_smush_finale", 'smush');

			$content = file_get_contents($url_smush_finale);
			$newcontent = json_decode($content, true);
			if (!$newcontent['error']) {
				include_spip('inc/distant');
				$new_url = $newcontent['dest'];
				spip_log("SMUSH : recuperation du fichier $new_url", 'smush');
				$contenu = recuperer_page($new_url, false, false, _COPIE_LOCALE_MAX_SIZE);
				if ($contenu) {
					ecrire_fichier($im, $contenu);
				}
			} else {
				spip_log('SMUSH en erreur', 'smush.'._LOG_ERREUR);
				spip_log($newcontent['error'], 'smush.'._LOG_ERREUR);
			}
			return $im;
		} else {
			return image_smush($im);
		}
	}

	return $im;
}

/**
 * Fonction de réduction d'image
 * Nécessite que la fonction exec() soit utilisable
 * Nécessite certains binaires sur le serveur :
 * -* identify : apt-get install imagemagick
 * -* convert : apt-get install imagemagick
 * -* pngnq : apt-get install pngnq
 * -* pngoptim : apt-get install pngoptim
 * -* jpegtran : apt-get install libjpeg-turbo-progs
 * -* jpegoptim : apt-get install jpegoptim
 * -* gifsicle : apt-get install gifsicle
 *
 * @param string $im
 * 		Le tag image (<img src...>) à réduire
 * @return string
 * 		Le nouveau tag image
 */
function image_smush($im) {
	$fonction = array('smush', array());
	$image = _image_valeurs_trans($im, 'smush', false, $fonction);

	if (!$image) {
		return $im;
	}

	$creer = $image['creer'];

	// Methode precise
	// resultat plus beau, mais tres lourd
	// Et: indispensable pour preserver transparence!
	if (file_exists($image['fichier']) && $creer) {
		include_spip('inc/config');

		$im = $image['fichier'];
		$dest = $image['fichier_dest'];

		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$magick = 'magick ';
		} else {
			$magick = '';
		}
		$format = trim(exec($magick.'identify -format %m '.$im));
		/**
		 * On récupère le nom de fichier sans extension
		 */
		$tmp = explode('.', $dest);
		array_pop($tmp);
		$tmp = join('.', $tmp);

		/**
		 * Si on est sur un GIF, on le transforme en PNG
		 * On utilise la commande convert pour cela
		 */
		if ($format == 'GIF') {
			$dest_png = $tmp.'-convert.png';
			exec($magick.'convert '.$im.' '.$dest_png);
			$im_original = $im;
			$im = $dest_png;
			$format = 'PNG';
		}

		if ($format == 'PNG') {
			$dest_jpg = $file_size_jpg = false;
			/**
			 * On est sur un PNG
			 */
			$out_alpha = array();
			exec('identify -verbose '.$im.' |grep lpha', $out_alpha);
			/**
			 * Si l'image n'a pas de canal alpha, il est peut être intéressant de la convertir en jpg
			 */
			if (count($out_alpha) == 0) {
				$dest_jpg = $tmp.'.jpg';
				/**
				 * Conversion en jpg vers dest_jpg
				 */
				exec($magick.'convert '.$im.' '.$dest_jpg);
				if (lire_config('jpegoptim_casse', 'oui') != 'oui') {
					$compression = '';
					if (intval(lire_config('smush/jpeg_qualite')) > 0) {
						$compression = ' -m'.intval(lire_config('smush/jpeg_qualite'));
					}
					exec("jpegoptim$compression --strip-all $dest_jpg");
				}
				$fsize = filesize($dest_jpg);
				if ($fsize < 10*1024) {
					exec('jpegtran -copy none -optimize -outfile '.$dest_jpg.' '.$dest_jpg);
				} else {
					exec('jpegtran -copy none -optimize -progressive -outfile '.$dest_jpg.' '.$dest_jpg);
				}
				$file_size_jpg = filesize($dest_jpg);
			}
			/**
			 * Optimisation du PNG
			 */
			$nq = substr($im, 0, -4).'-nq8.png';
			if (file_exists($dest)) {
				spip_unlink($dest);
			}
			exec('pngnq -f '.$im.' && optipng -o5 '.$nq.' -out '.$dest);

			/**
			 * Comparaison des version jpg et png si cela est le cas
			 */
			if (isset($dest_jpg) AND $dest_jpg && $file_size_jpg && file_exists($dest) && filesize($dest) > $file_size_jpg) {
				/**
				 * Si on garde dest_jpg, on ne supprime pas dest pour ne pas la recalculer plus tard
				 */
				$im = $dest = $dest_jpg;
			} elseif (isset($dest_jpg) AND $dest_jpg && file_exists($dest_jpg)) {
				spip_unlink($dest_jpg);
			}

			if (isset($dest_png) AND $dest_png && file_exists($dest_png)) {
				spip_unlink($dest_png);
			}
			if (file_exists($nq)) {
				spip_unlink($nq);
			}
		} elseif ($format == 'JPEG') {
			/**
			 * On est sur un JPEG
			 * On utilise _IMG_QUALITE comme qualité de compression, sinon la valeur de
			 */
			if (lire_config('jpegoptim_casse', 'oui') != 'oui') {
				$compression = '';
				$compression_image = lire_config('smush/jpeg_qualite', defined('_IMG_QUALITE') ? _IMG_QUALITE : false);
				if ($compression_image && intval($compression_image) > 0 && intval($compression_image) < 100) {
					$compression = ' -m'.intval($compression_image);
				}
				exec("jpegoptim$compression --strip-all $im");
			}
			$fsize = filesize($im);
			$dest = $tmp.'.jpg';
			if ($fsize < 10*1024) {
				exec('jpegtran -copy none -optimize -outfile '.$dest.' '.$im);
			} else {
				exec('jpegtran -copy none -optimize -progressive -outfile '.$dest.' '.$im);
			}
		} elseif (preg_match('/^GIFGIF/', $format)) {
			/**
			 * On est sur un GIF animé
			 */
			$dest = $tmp.'.gif';
			exec('gifsicle -O3 '.$im.' -o '.$dest);
		}

		/**
		 * Si la taille du résultat est supérieure à l'original,
		 * on retourne l'original en supprimant le fichier temporaire créé
		 */
		$image_compare = isset($im_original) ? $im_original : $im;
		if (!file_exists($dest) || (filesize($dest) > filesize($image_compare))) {
			spip_unlink($dest);
			spip_log('Smush - Image de même taille - On retourne un vieux cache '.$image['fichier_dest'], 'images');
			@copy($image_compare, $dest);
		}
	} else {
		/**
		 * Si c'est un png et que l'on a encore la dernière version optimisée (on ne doit donc pas créer)
		 * On vérifie que l'on n'a pas un jpg plus léger qui traine,
		 * si oui, on utilise le jpg
		 */
		if ($image['format_source'] == 'png' && file_exists(rtrim($image['fichier_dest'],'.png').'.jpg')) {
			spip_log('Smush - On utilise un jpg ici à la place du jpg','images');
			$src = rtrim($image['fichier_dest'],'.png').'.jpg';
			$im = _image_ecrire_tag($image, array('src' => $src));
		}
		return $im;
	}
	return _image_ecrire_tag($image, array('src' => $dest));
}

function image_smush_debrayer($im) {
	$GLOBALS['Smush_Debraye'] = true;
	return $im;
}

function image_smush_embrayer($im) {
	$GLOBALS['Smush_Debraye'] = false;
	return $im;
}
