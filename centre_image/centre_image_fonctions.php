<?php


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
function centre_image($fichier) {
	static $spip_centre_image = array();

	// nettoyer le fichier (qui peut être dans un <img> ou qui peut être daté)
	if (preg_match("/src\=/", $fichier)) $fichier = extraire_attribut($fichier, "src");
	$fichier = preg_replace(",\?[0-9]*$,", "", $fichier);

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

		if (file_exists($fichier_forcer) and filemtime($fichier_forcer) > filemtime($fichier)) {
			$res = json_decode(file_get_contents($fichier_forcer),TRUE);
		}
		else if (file_exists($fichier_json) and filemtime($fichier_json) > filemtime($fichier)) {
			$res = json_decode(file_get_contents($fichier_json),TRUE);
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

			file_put_contents($fichier_json, json_encode($res,TRUE));
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
	return $res["x"];
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
	return $res["y"];
}


/*
 * Détection du visage (attention: super-lourd)
 */

function centre_image_visage ($fichier) {
	if (preg_match("/src\=/", $fichier)) $fichier = extraire_attribut($fichier, "src");
	$fichier = preg_replace(",\?[0-9]*$,", "", $fichier);

	// on mémorise le résultat -> don
	if ($spip_centre_image_visage["$fichier"]) return $spip_centre_image_visage["$fichier"];
	
	
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
				
		$fichier_json = "$cache$md5.json";


		 if (file_exists($fichier_json) and filemtime($fichier_json) > filemtime($fichier)) {
			$res = json_decode(file_get_contents($fichier_json),TRUE);
		} else {
		
			include_spip ("inc/FaceDetector");
			$detector = new svay\FaceDetector('detection.dat');
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
				
			file_put_contents($fichier_json, json_encode($res,TRUE));		
		}

		$spip_centre_image_visage["$fichier"] = $res;

		return $res;    
	
	
	}

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
	$flux .= "\n<script>var croix = '".find_in_path("imgs/croix.png")."'</script>";
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
