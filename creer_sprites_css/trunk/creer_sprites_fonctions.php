<?php

$GLOBALS['sprites'] = false;


/**
 * @param $img string           image à intégrer au sprite
 * @param $nom string           nom du fichier sprite
 * @return string|void          source de la balise <img> dont le src est vide
 *                              et dont le background du style fait référence au sprite, avec le bon offset
 *                              et un marqueur pour le timestamp
 */
function sprite($img, $nom) {
	// Extraire le nom du fichier, soit directement soit dans <img src>
	if (@file_exists($img)) {
		$src = $img;
	} else {
		$src = extraire_attribut($img, 'src');
		$src = preg_replace(',\?[0-9]*$,', '', $src);
		// Si pas de fichier, ignorer
		if (!@file_exists($src)) {
			return;
		}
	}

	if (!in_array($src, $GLOBALS['sprites'][$nom]['fichiers'])) {
		$GLOBALS['sprites'][$nom]['fichiers'][] = $src;

		$largeur = largeur($img);
		$hauteur = hauteur($img);
		if ($largeur > $GLOBALS['sprites'][$nom]['largeur']) {
			$GLOBALS['sprites'][$nom]['largeur'] = $largeur;
		}
		$hauteur_old = max(0, $GLOBALS['sprites'][$nom]['hauteur']);
		$GLOBALS['sprites'][$nom]['hauteur'] += $hauteur;

		$alt = extraire_attribut($img, 'alt');
		$class = extraire_attribut($img, 'class');
		$fichier = sous_repertoire(_DIR_VAR, 'cache-sprites') . $nom;

		// On pose un marqueur pour le timestamp du futur fichier sprite
		// qui garantira le raffraîchissement de l'affichage en cas de mise à jour du sprite
		$fichier .= "?m=spiprempdate[$fichier]";

		$date_src = @filemtime($src);
		if ($date_src > $GLOBALS['sprites'][$nom]['date']) {
			$GLOBALS['sprites'][$nom]['date'] = $date_src;
		}

		$GLOBALS['sprites'][$nom]['tags'][$src] = "<img src='" . find_in_path('rien.gif') . "' width='" . $largeur . "px' height='" . $hauteur . "px' style='width: " . $largeur . 'px; height: ' . $hauteur . "px; background: url($fichier) 0px -" . $hauteur_old . "px;' alt='$alt' class='$class' />";
	}

	return $GLOBALS['sprites'][$nom]['tags'][$src];
}

/**
 * @param string $fichier       un nom de fichier  gif jpg ou png
 * @return bool|mixed           l'extension du fichier, ou false si l'extension n'est pas gif jpg ou png
 */
function creer_sprites_terminaison_fichier_image($fichier) {
	if (preg_match(',^(?>.*)(?<=\.(gif|jpg|png)),', $fichier, $regs)) {
		$terminaison = $regs[1];
		return $terminaison;
	} else {
		return false;
	}
}

/**
 * @param array $flux
 * @return mixed
 *
 * Inutile désormais, gardé pour compatibilité si jamais c'était utilisé dans un code perso
 */
function creer_sprites($flux) {
	$flux['data']['texte'] = filtre_creer_sprites($flux['data']['texte']);
	return $flux;
}

/**
 * @param string $page
 * @return string           la page modifiée
 *
 * Crée les fichiers sprites à partir des informations collectées par les appels de |sprite
 * et remplace les marqueurs de datage par le timestamp du fichier sprite
 *
 * @used-by     creer_sprites_affichage_final
 * Appelé via le pipeline affichage_final
 * Peut aussi être appelé par un #FILTRE si on veut forcer au niveau d'un squelette
 */
function filtre_creer_sprites($page) {
	if (!count($GLOBALS['sprites'])) {
		return $page;
	}
	$sprites = $GLOBALS['sprites'];

	foreach ($sprites as $key => $sprite) {
		$fichier_sprite = sous_repertoire(_DIR_VAR, 'cache-sprites').$key;
		$nom_fichier_sprite = substr($fichier_sprite, 0, strlen($fichier_sprite) - 4);

		$date_max = $sprite['date'];
		$date_src = @filemtime($fichier_sprite);
		$largeur = $sprite['largeur'];
		$hauteur = $sprite['hauteur'];

		$creer = false;

		// On recalcule le sprite si l'un des fichiers qui le compose est plus récent que le sprite
		if ($date_src < $date_max) {
			$creer = true;
		}
		if ($largeur != largeur($fichier_sprite) || $hauteur != hauteur($fichier_sprite)) {
			$creer = true;
		}

		if (!empty($_GET['var_mode'])
			and in_array($_GET['var_mode'], array('recalcul', 'debug'))) {
			$creer = true;
		}

		if ($creer) {
			include_spip('inc/filtres_images');

			$im = imagecreatetruecolor($largeur, $hauteur);
			imagepalettetotruecolor($im);
			@imagealphablending($im, false);
			@imagesavealpha($im, true);
			$color_t = imagecolorallocatealpha($im, 0, 0, 0, 127);
			imagefill($im, 0, 0, $color_t);

			$y_total = 0;
			foreach ($sprite['fichiers'] as $img) {
				$f = 'imagecreatefrom'.str_replace('jpg', 'jpeg', creer_sprites_terminaison_fichier_image($img));
				$im_tmp = $f($img);
				@imagepalettetotruecolor($im_tmp);

				$x = imagesx($im_tmp);
				$y = imagesy($im_tmp);

				@ImageCopy($im, $im_tmp, 0, $y_total, 0, 0, $x, $y);
				$y_total += $y;
			}

			_image_imagepng($im, "$nom_fichier_sprite.png");

			$ext = creer_sprites_terminaison_fichier_image($fichier_sprite);
			if ($ext != 'png') {
				$new = extraire_attribut(image_aplatir("$nom_fichier_sprite.png", $ext, 'ffffff'), 'src');
				copy($new, $fichier_sprite);
			}
			imagedestroy($im);
			imagedestroy($im_tmp);
		}
	}
	// Mettre les dates des fichiers en variable de chaque appel
	$page = preg_replace_callback(',spiprempdate\[([^\]]*)\],', 'creer_sprites_remplacer_date', $page);

	$GLOBALS['sprites'] = false;

	return $page;
}

/**
 * @param array $regs       tableau dont l'élément d'index 1 est un chemin de fichier
 * @return string mixed     timestamp de la création du fichier
 */
function creer_sprites_remplacer_date($regs) {
static $date_fichier=array();
	$fichier = $regs[1];
	if ($date_fichier[$fichier] > 0) {
		return $date_fichier[$fichier];
	} else {
		$date_fichier[$fichier] = @filemtime($fichier);
		return $date_fichier[$fichier];
	}
}

/**
 * @param $page
 * @return string
 *
 * Pipeline pour calculer les sprites et les timestamp
 * @uses filtre_creer_sprites
 */
function creer_sprites_affichage_final($page) {
	return filtre_creer_sprites($page);
}
