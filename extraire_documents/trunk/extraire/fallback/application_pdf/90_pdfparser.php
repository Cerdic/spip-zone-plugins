<?php

/**
 * Tester si cette méthode d'extraction est disponible
 **/
function extraire_fallback_application_pdf_90_pdfparser_test_dist() {
	if (
		is_dir(_DIR_RACINE . 'lib/TCPDF-6.2.6')
		and is_dir(_DIR_RACINE . 'lib/pdfparser-0.9.22/src')
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
 * @param $fichier le fichier à traiter
 * @return Scontenu le contenu brut
 **/
function extraire_fallback_application_pdf_90_pdfparser_extraire_dist($fichier) {
    $infos = array('contenu' => false);
    $contenu = '';

    // Bespoin de charger composer
    if (!class_exists('Composer\\Autoload\\ClassLoader')) {
		include_spip('lib/Composer/Autoload/ClassLoader');
	}
    include_spip('lib/TCPDF-6.2.6/tcpdf_parser');

    $loader = new \Composer\Autoload\ClassLoader();

    // register classes with namespaces
    $loader->add('Smalot\PdfParser', _DIR_RACINE . 'lib/pdfparser-0.9.22/src');
    $loader->register();

    $parser = new \Smalot\PdfParser\Parser();
    //Tenter de lire le pdf
    try {
        set_time_limit (0);
        $pdf = $parser->parseFile(_DIR_RACINE . $fichier);
    }
    catch (Exception $e) {
        //Pour toute exception on s'arrete et on retourne un contenu vide
        //Les cas de figure sont entre autre les fichiers mal formés ou signés
        return '';
    }

    // Parcourir les pages et extraire le contenu textuel
    try {
        foreach ($pdf->getPages() as $page) {
            $contenu .= $page->getText();
        }
    }
    catch (Exception $e) {
        //si on ne peut extraire le texte on passe à la page suivante
        $contenu .= '';
    }

    //Libérer les ressources
    unset($parser);
    unset($loader);
	
	// Si on a trouvé du texte
	if ($contenu) {
		$infos['contenu'] = $contenu;
	}
	
    return $infos;
}
