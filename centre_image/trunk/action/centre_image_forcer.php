<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function action_centre_image_forcer() {
	$fichier = $_GET["url"];

	include_spip('centre_image_fonctions');
	$fichier = centre_image_preparer_fichier($fichier);
	// pas de ../
	$fichier = str_replace('../', '', $fichier);

	// image uniquement présente dans _DIR_IMG
	if (strpos(_DIR_RACINE . $fichier, _DIR_IMG) === 0) {
		if (file_exists(_DIR_RACINE . $fichier)) {
			$md5 = md5($fichier);
			$forcer = sous_repertoire(_DIR_IMG, "cache-centre-image");

			$fichier_json = "$forcer$md5.json";
			$res = array("x" => $_GET["x"], "y" => $_GET["y"]);

			@touch(_DIR_RACINE . $fichier);
			file_put_contents($fichier_json, json_encode($res, TRUE));
			include_spip('inc/invalideur');
			suivre_invalideur('centre_image');
		}
	}
}
