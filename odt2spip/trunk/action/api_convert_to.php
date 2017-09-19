<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Convertit un fichier dans un autre format en utilisant libreoffice
 *
 * Si fichier en paramètre, retourne le chemin du fichier converti.
 * Si fichier posté, retourne un stream du fichier converti.
 *
 * @param null|string $arg Chemin du fichier source
 * @param string $format Format de sortie
 * @return bool|string
 */
function action_api_convert_to_dist($fichier_source = null, $format = 'odt') {

	include_spip('inc/odt2spip');
	include_spip('inc/convertir_avec_libreoffice');
	include_spip('inc/flock');

	if ($fichier_source) {
		$fichier = convertir_avec_libreoffice($fichier_source, $format);
		return $fichier;
	}

	// Tester l’autorisation d’accès
	$key = _request('api_key');
	if (!odt2spip_cle_autorisee($key)) {
		return false;
	}

	// Récupérer le fichier posté
	$fichier_source = odt2spip_deplacer_fichier_upload('file');
	if (!$fichier_source) {
		return false;
	}

	// Convertir dans le format demandé
	if (_request('arg')) {
		$format = (string)_request('arg');
	}

	spip_log('Conversion API : ' . $fichier_source, 'odtspip.' . _LOG_INFO);
	$fichier = convertir_avec_libreoffice($fichier_source, $format);
	supprimer_fichier($fichier_source);
	if (!$fichier) {
		return false;
	}
	spip_log('Conversion réussie API : ' . $fichier, 'odtspip.' . _LOG_INFO);

	// Envoyer le fichier
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="'.basename($fichier).'"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . filesize($fichier));
	readfile($fichier);
	supprimer_fichier($fichier);
	exit;
}