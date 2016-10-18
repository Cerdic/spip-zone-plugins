<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function inc_smush_image_dist($im) {
	if (!_IS_BOT) {
		if (defined('_SMUSH_API') && _SMUSH_API) {
			$image = _image_valeurs_trans($im, 'smush');
			$im = $image['fichier'];
			include_spip('inc/smush_php_compat');
			if (!file_exists($im)) {
				return $im;
			}

			// L'adresse de l'API que l'on utilise
			$url_smush = 'http://www.smushit.com/ysmush.it/ws.php';

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
	include_spip('inc/config');
	$fonction = array('smush', array());
	$image = _image_valeurs_trans($im, 'smush', false, $fonction);

	if (!$image) {
		return $im;
	}

	$im = $image['fichier'];
	$dest = $image['fichier_dest'];
	$creer = $image['creer'];

	// Methode precise
	// resultat plus beau, mais tres lourd
	// Et: indispensable pour preserver transparence!
	if ($creer) {
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
			$dest = $tmp.'.png';
			exec($magick.'convert '.$im.' '.$dest);
			$im = $dest;
			$format = 'PNG';
		}

		if ($format == 'PNG') {
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
			 * Conversion en PNG
			 */
			$nq = substr($im, 0, -4).'-nq8.png';
			exec('pngnq -f '.$im.' && optipng -o5 '.$nq.' -out '.$dest);

			/**
			 * Comparaison des version jpg et png si cela est le cas
			 */
			if ($dest_jpg && $file_size_jpg && filesize($dest) > $file_size_jpg) {
				spip_unlink($dest);
				$dest = $dest_jpg;
			} elseif ($dest_jpg && file_exists($dest_jpg)) {
				spip_unlink($dest_jpg);
			}
			if (file_exists($nq)) {
				spip_unlink($nq);
			}
		} elseif ($format == 'JPEG') {
			/**
			 * On est sur un JPEG
			 */
			if (lire_config('jpegoptim_casse', 'oui') != 'oui') {
				$compression = '';
				if (intval(lire_config('smush/jpeg_qualite')) > 0) {
					$compression = ' -m'.intval(lire_config('smush/jpeg_qualite'));
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
		if (!file_exists($dest) || (filesize($dest) > filesize($im))) {
			spip_unlink($dest);
			$dest = $im;
		}
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
