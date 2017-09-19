<?php

/**
 * Convertit un fichier en utilisant l’application libreoffice.
 *
 * @note : nécessite que libreoffice soit installée sur le serveur.
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Convertit un document transmis dans le format spécifié.
 *
 * @param string $fichier (chemin)
 * @param string $format_destination
 * @return bool|string
 */
function convertir_avec_libreoffice($fichier, $format_destination = 'odt') {
	if (!file_exists($fichier) or !is_readable($fichier)) {
		return false;
	}

	include_spip('inc/libreoffice');
	$destination = odt2spip_get_repertoire_temporaire();

	try {
		$libreoffice = (new LibreOffice($fichier))
			->setConvertTo($format_destination)
			->setOutputDir($destination)
			->execute();
	} catch (\Exception $e) {
		spip_log($e->getMessage(), 'odtspip.' . _LOG_ERREUR);
		return false;
	}

	if ($libreoffice->getErrors()) {
		return false;
	}

	return $libreoffice->getConvertedFile();
}