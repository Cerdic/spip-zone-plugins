<?php

/**
 * Tester si cette méthode d'extraction est disponible
 **/
function extraire_fallback_application_pdf_90_pdfparser_test_dist(){
	if (
		find_in_path('lib/TCPDF-6.2.17')
		and find_in_path('lib/pdfparser-0.12.0/src')
	){
		return true;
	} else {
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
function extraire_fallback_application_pdf_90_pdfparser_extraire_dist($fichier){
	$infos = array('contenu' => false);
	$contenu = '';

	// Bespoin de charger composer
	if (!class_exists('Composer\\Autoload\\ClassLoader')){
		include_spip('lib/Composer/Autoload/ClassLoader');
	}
	include_spip('lib/TCPDF-6.2.17/tcpdf_parser');

	$loader = new \Composer\Autoload\ClassLoader();

	// register classes with namespaces
	$loader->add('Smalot\PdfParser', find_in_path('lib/pdfparser-0.12.0/src'));
	$loader->register();

	$parser = new \Smalot\PdfParser\Parser();

	// verifier la memoire disponible : on a besoin de 3 fois la taille du fichier (estimation)
	// TODO : verifier cette estimation pour ce parser
	include_spip('inc/extrairedoc');
	if (!extrairedoc_verifier_memoire_disponible(3 * filesize($fichier))) {
		return '';
	}

	//Tenter de lire le pdf
	try {
		set_time_limit(0);
		$pdf = $parser->parseFile($fichier);
	} catch (Exception $e) {
		//Pour toute exception on s'arrete et on retourne un contenu vide
		//Les cas de figure sont entre autre les fichiers mal formés ou signés
		return '';
	}

	// Parcourir les pages et extraire le contenu textuel
	try {
		foreach ($pdf->getPages() as $page){
			$contenu .= $page->getText();
		}
	} catch (Exception $e) {
		//si on ne peut extraire le texte on passe à la page suivante
		$contenu .= '';
	}

	//Libérer les ressources
	unset($parser);
	unset($loader);

	// Si on a trouvé du texte
	if ($contenu){
		$infos['contenu'] = $contenu;
	}

	return $infos;
}
