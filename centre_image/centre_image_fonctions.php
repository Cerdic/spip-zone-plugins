<?php


function centre_image($fichier) {
	static $spip_centre_image = array();
	
	// nettoyer le fichier (qui peut être dans un <img> ou qui peut être daté)
	if (preg_match("/src\=/", $fichier)) $fichier = extraire_attribut($fichier, "src");
	$fichier = preg_replace(",\?[0-9]*$,", "", $fichier);

	// on mémorise le résultat -> don
	if ($spip_centre_image["$fichier"]) return $spip_centre_image["$fichier"];

	if (function_exists("imagefilter") && file_exists($fichier)) {
		$md5 = md5($fichier);
		$l1 = substr($md5, 0, 1 );
		$l2 = substr($md5, 1, 1);

		$cache = sous_repertoire(_DIR_VAR, "cache-centre-image");
		$cache = sous_repertoire($cache, $l1);
		$cache = sous_repertoire($cache, $l2);
		
		$forcer = sous_repertoire(_DIR_IMG, "cache-centre-image");
		
		$fichier_json = "$cache$md5.json";
		
		if (file_exists($fichier_json) and filemtime($fichier_json) > filemtime($fichier) and 1==2) {
			$res = json_decode(file_get_contents($fichier_json),TRUE);
		} else {
	
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
			
			file_put_contents($fichier_json, json_encode($res,TRUE));
		}
    } else {
    	$res = array("x" => 0.5, "y" => 0.5);
    }
    
    $spip_centre_image["$fichier"] = $res;
	return $res;    
}

function centre_image_x($fichier) {
	$res = centre_image($fichier);
	return $res["x"];
}
function centre_image_y($fichier) {
	$res = centre_image($fichier);
	return $res["y"];
}