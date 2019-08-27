<?php
/**
 * Fonctions utiles au plugin Centre image
 *
 * @plugin     Centre image
 * @copyright  2015
 * @author     ARNO*
 * @licence    GNU/GPL
 * @package    SPIP\Centre_image\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Retourne les coordonnées du point d'intérêt de l'image transmise
 *
 * Retourne les coordonnées `[0.5, 0.5]` par défaut (si le calcul échoue par exemple).
 *
 * @uses centre_image_visage() Si la constante `_SPIP_CENTRE_IMAGE` définie à `visage`
 * @uses centre_image_densite() sinon
 *
 * @param string $fichier
 *     Chemin du fichier ou balise `<img>`
 * @return float[]
 *     Tableau (x, y) des coordonnées du point d'intéret ;
 *     - x entre 0 (à gauche) et 1 (à droite)
 *     - y entre 0 (en haut) et 1 (en bas)
**/
function centre_image($fichier) {
	// Gérer le plugin mutualisation si on est pas dans le prive

	if (defined('_DIR_SITE') and (false === strpos($fichier, _DIR_SITE))){
		$fichier = _DIR_SITE.$fichier;
	}

	if (defined('_SPIP_CENTRE_IMAGE') AND _SPIP_CENTRE_IMAGE == "visage") {
		return centre_image_visage($fichier);
	} else {
		return centre_image_densite($fichier);
	}
}

/**
 * Calcule le chemin correct théorique du fichier
 *
 * - extrait l'URL d'une éventuel attribut 'src' d'une balise
 * - passe en url relative si c'était en absolu
 * - enlève un timestamp ou un token éventuel (accès restreint)
 *
 * @param string $fichier
 * return string
 */
function centre_image_preparer_fichier($fichier) {
	// nettoyer le fichier (qui peut être dans un <img>)
	if (preg_match("/src\=/", $fichier)) {
		$fichier = extraire_attribut($fichier, "src");
	}

	// Enlever timestamp ou token
	$fichier = explode('?', $fichier, 2);
	$fichier = array_shift($fichier);

	// si URL absolue de l'image, on passe en relatif
	if (tester_url_absolue($fichier)) {
		$url_site = url_de_base();
		if (strpos($fichier, $url_site) === 0) {
			$fichier = substr($fichier, strlen($url_site));
		}
	}

	return $fichier;
}

/**
 * Retourne les coordonnées du point d'intérêt de l'image transmise
 *
 * Retourne les coordonnées `[0.5, 0.5]` par défaut (si le calcul échoue par exemple).
 *
 * @param string $fichier
 *     Chemin du fichier ou balise `<img>`
 * @return float[]
 *     Tableau (x, y) des coordonnées du point d'intéret ;
 *     - x entre 0 (à gauche) et 1 (à droite)
 *     - y entre 0 (en haut) et 1 (en bas)
**/
function centre_image_densite($fichier) {
	static $spip_centre_image = array();

	$fichier = centre_image_preparer_fichier($fichier);

	// on mémorise le résultat -> don
	if (isset($spip_centre_image[$fichier])) {
		return $spip_centre_image[$fichier];
	}

	if (file_exists($fichier)) {

		$md5 = $fichier;
		if (test_espace_prive()) {
			$md5 = preg_replace(",^\.\.\/,", "", $md5);
		}
		$md5 = md5($md5);
		$l1 = substr($md5, 0, 1 );
		$l2 = substr($md5, 1, 1);

		$cache = sous_repertoire(_DIR_VAR, "cache-centre-image");
		$cache = sous_repertoire($cache, $l1);
		$cache = sous_repertoire($cache, $l2);

		$forcer = sous_repertoire(_DIR_IMG, "cache-centre-image");

		$fichier_json = "$cache$md5.json";
		$fichier_forcer = "$forcer$md5.json";

		// éviter plusieurs accès successifs
		$mtime_source = filemtime($fichier);

		if (file_exists($fichier_forcer) and filemtime($fichier_forcer) >= $mtime_source) {
			$res = json_decode(file_get_contents($fichier_forcer), TRUE);
		} elseif (file_exists($fichier_json) and filemtime($fichier_json) > $mtime_source) {
			$res = json_decode(file_get_contents($fichier_json), TRUE);
		} else {
			if (function_exists("imagefilter")) {
				if (preg_match(",\.(gif|jpe?g|png)($|[?]),i", $fichier, $regs)) {
					include_spip('inc/centre_image_lib');
					include_spip('inc/filtres_images_lib_mini');
					$terminaison = strtolower($regs[1]);
					$terminaison = str_replace("jpg", "jpeg", $terminaison);
					$fonction_imagecreatefrom = "_imagecreatefrom".$terminaison;

					$img     = $fonction_imagecreatefrom($fichier);
					$cropper = new _centre_image($img);
					$res = $cropper->find_focus();
					imagedestroy($img);
				}
			} else {
				$res = array("x" => 0.5, "y" => 0.5);
			}

			file_put_contents($fichier_json, json_encode($res));
		}
	} else {
		$res = array("x" => 0.5, "y" => 0.5);
	}

	$spip_centre_image["$fichier"] = $res;
	return $res;
}

/**
 * Retourne la coordonnée x du point d'intérêt de l'image transmise
 *
 * @uses centre_image()
 * @param string $fichier
 *     Chemin du fichier ou balise `<img>`
 * @return float
 *     Coordonnée x du point d'intéret, entre 0 (à gauche) et 1 (à droite)
**/
function centre_image_x($fichier) {
	$res = centre_image($fichier);
	return $res['x'];
}

/**
 * Retourne la coordonnée y du point d'intérêt de l'image transmise
 *
 * @uses centre_image()
 * @param string $fichier
 *     Chemin du fichier ou balise `<img>`
 * @return float
 *     Coordonnée y du point d'intéret, entre 0 (en haut) et 1 (en bas)
**/
function centre_image_y($fichier) {
	$res = centre_image($fichier);
	return $res['y'];
}


/**
 * Détection du visage (attention: super-lourd)
 *
 * Retourne les coordonnées du point d'intérêt de l'image transmise
 * en s'appuyant sur une (lourde) fonction de détection de visage
 *
 * Retourne les coordonnées `[0.5, 0.5]` par défaut (si le calcul échoue par exemple).
 *
 * @param string $fichier
 *     Chemin du fichier ou balise `<img>`
 * @return float[]
 *     Tableau (x, y) des coordonnées du point d'intéret ;
 *     - x entre 0 (à gauche) et 1 (à droite)
 *     - y entre 0 (en haut) et 1 (en bas)
**/
function centre_image_visage($fichier) {
	static $spip_centre_image_visage = array();

	$fichier = centre_image_preparer_fichier($fichier);

	// on mémorise le résultat -> don
	if (isset($spip_centre_image_visage["$fichier"]) AND $spip_centre_image_visage["$fichier"]) {
		return $spip_centre_image_visage["$fichier"];
	}

	if (file_exists($fichier)) {

		$md5 = $fichier;
		if (_DIR_RACINE == "../") {
			$md5 = preg_replace(",^\.\.\/,", "", $md5);
		}
		$md5 = md5($md5);
		$l1 = substr($md5, 0, 1 );
		$l2 = substr($md5, 1, 1);

		$cache = sous_repertoire(_DIR_VAR, "cache-centre-image-visage");
		$cache = sous_repertoire($cache, $l1);
		$cache = sous_repertoire($cache, $l2);
		$forcer = sous_repertoire(_DIR_IMG, "cache-centre-image");

		$fichier_json = "$cache$md5.json";
		$fichier_forcer = "$forcer$md5.json";

		// éviter plusieurs accès successifs
		$mtime_source = filemtime($fichier);

		if (file_exists($fichier_forcer) and filemtime($fichier_forcer) >= $mtime_source) {
			$res = json_decode(file_get_contents($fichier_forcer), TRUE);
		} elseif (file_exists($fichier_json) and filemtime($fichier_json) > $mtime_source) {
			$res = json_decode(file_get_contents($fichier_json), TRUE);
		} else {
			include_spip ("inc/FaceDetector");
			$detector = new FaceDetector('detection.dat');
			$detector->faceDetect($fichier);
			$face = $detector->getFace();

			if ($face) {
				$l = largeur($fichier);
				$h = hauteur($fichier);

				$x = ($face["x"] + ($face["w"] / 2)) / $l ;
				$y = ($face["y"] + ($face["w"] / 2)) / $h;

				$res = array("x" => $x, "y" => $y);
			} else {
				$res = array("x" => 0.5, "y" => 0.33);
			}

			file_put_contents($fichier_json, json_encode($res));
		}
	} else {
		$res = array("x" => 0.5, "y" => 0.5);
	}

	$spip_centre_image_visage["$fichier"] = $res;
	return $res;
}


/**
 * Ajoute les scripts nécessaires dans l'espace privé
 *
 * @pipeline header_prive
 * @param string $flux Texte dans le head HTML
 * @return string
**/
function centre_image_header_prive($flux) {
	$flux .= "\n<script type='text/javascript' src='".find_in_path("centre_image_gestion.js")."'></script>\n";
	$flux .= "\n<script>var croix = '".find_in_path("imgs/croix-centre-image.png")."'</script>";
	return $flux;
}

/**
 * Ajoute les plugins jquery ui nécessaires dans l'espace privé
 *
 * @pipeline jqueryui_plugins
 * @param string[] $plugins
 * @return string[]
**/
function centre_image_jqueryui_plugins($plugins) {
	if (test_espace_prive()) {
		$plugins[] = "jquery.ui.core";
		$plugins[] = "jquery.ui.draggable";
	}
	return $plugins;
}
