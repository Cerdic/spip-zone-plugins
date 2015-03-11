<?php

/**
 * Extraire le contenu pour le mime type pdf
 *
 *
 * @param $fichier le fichier à traiter
 * @return Scontenu le contenu brut
 */
function extraire_application_pdf($fichier) {
    $contenu = "";

    // Bespoin de charger composer
    include_spip('lib/Composer/Autoload/ClassLoader');
    include_spip('lib/TCPDF-6.2.6/tcpdf_parser');

    $loader = new \Composer\Autoload\ClassLoader();

    // register classes with namespaces
    $loader->add('Smalot\PdfParser', _DIR_RACINE . 'lib/pdfparser-0.9.22/src');
    $loader->register();

    $parser = new \Smalot\PdfParser\Parser();
    //Tenter de lire le pdf
    try {
        set_time_limit (0);
        $pdf = $parser->parseFile(_DIR_RACINE.$fichier);
    }
    catch (Exception $e) {
        //Pour toute exception on s'arrete et on retourne un contenu vide
        //Les cas de figure sont entre autre les fichiers mal formés ou signés
        return "";
    }

    // Parcourir les pages et extraire le contenu textuel
    foreach ($pdf->getPages() as $page) {
        $contenu .= $page->getText();
    }

    return $contenu;
}
