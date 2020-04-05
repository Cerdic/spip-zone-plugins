<?php


/**
 * Tester si cette méthode d'extraction est disponible
 **/
function extraire_fallback_application_pdf_99_pdfexec_test_dist() {
	if (defined('_EXTRACT_PDF_EXEC')
	  and file_exists(_EXTRACT_PDF_EXEC)
	) {
		return true;
	}
	else {
		return false;
	}
}

/**
 * Extraire le contenu pour le mime type pdf
 *
 *
 * @param string $fichier le fichier à traiter
 * @return array Scontenu le contenu brut
 **/
function extraire_fallback_application_pdf_99_pdfexec_extraire_dist($fichier) {
  $infos = array('contenu' => false);
  $contenu = '';

	$exe = _EXTRACT_PDF_EXEC;
	if (defined('_EXTRACT_PDF_EXEC_CMD_OPTIONS') and _EXTRACT_PDF_EXEC_CMD_OPTIONS) {
		$options = ' ' . _EXTRACT_PDF_EXEC_CMD_OPTIONS . ' ';
	} else {
		$options = ' ';
	}
	$cmd = $exe . $options . $fichier;
	spip_log('Extraction PDF avec ' . $cmd, 'extrairedoc');

	$sortie = exec($cmd, $output, $return_var);
	if ($return_var != 0) {
		if ($return_var == 3) {
			$erreur = 'Le contenu de ce fichier PDF est protégé.';
			spip_log('Erreur extraction ' . $fichier . ' protege (code ' . $return_var . ') : ' . $erreur, 'extrairedoc');
			return '';
		} else {
			spip_log('Erreur extraction ' . $fichier . ' (code ' . $return_var . ')', 'extrairedoc');
			return '';
		}
	} else {
		// on ouvre et on lit le .txt
		$nouveaufichier = str_replace('.pdf', '.txt', $fichier);
		if (file_exists($nouveaufichier) && is_readable($nouveaufichier)) {

			// verifier la memoire disponible : on a besoin de 2 fois la taille du fichier texte (estimation)
			include_spip('inc/extrairedoc');
			if (!extrairedoc_verifier_memoire_disponible(2 * filesize($nouveaufichier))) {
				return '';
			}

			$contenu = file_get_contents($nouveaufichier);
			// TODO : comment connaitre l'encoding du fichier ?
			include_spip('inc/charsets');
			$contenu = importer_charset($contenu, 'iso-8859-1');
			unlink($nouveaufichier);
		} else {
			spip_log('Erreur extraction PDF : Le fichier texte n\'existe pas ou n\'est pas lisible.', 'extrairedoc');
			return '';
		}
	}

	// Si on a trouvé du texte
	if ($contenu) {
		$infos['contenu'] = $contenu;
	}

  return $infos;
}
