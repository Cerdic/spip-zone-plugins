<?php

/**
 * Tester si cette méthode d'extraction est disponible
 **/
function extraire_defaut_10_tika_server_test_dist($mime) {
	include_spip('inc/distant');
	
	// On cherche si le serveur Tika est bien lancé en local (valeurs peut-être à configurer…)
	$tika_version = recuperer_page('http://localhost:9998/version');
	
	if (
		strpos($tika_version, 'Apache Tika') !== false
		// pas de image/truc pour l'instant avec Tika,
		// sinon par défaut on va chercher à extraire des centaines ou des milliers d'images suivant les sites…
		and strpos($mime, 'image') !== 0
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
function extraire_defaut_10_tika_server_extraire_dist($fichier, $infos) {
    $infos = array('contenu' => false);
    $contenu = '';

    // Bespoin de charger composer
    if (!class_exists('Composer\\Autoload\\ClassLoader')) {
		include_spip('lib/Composer/Autoload/ClassLoader');
	}

    $loader = new \Composer\Autoload\ClassLoader();

    // On définit le bon chemin pour le namespace de la librairie nécessaire
    $loader->addPsr4('Vaites\\ApacheTika\\', _DIR_PLUGIN_EXTRAIREDOC . 'lib/vaites/php-apache-tika/src');
    $loader->register();
	
	// On récupère le client pour discuter avec Tika
	$client = \Vaites\ApacheTika\Client::make();
	
	// On tente de récupérer le texte brut du fichier
    try {
        set_time_limit (0);
        $contenu = $client->getText(_DIR_RACINE . $fichier);
    }
    catch (Exception $e) {
        //Pour toute exception on s'arrete et on retourne un contenu vide
        //Les cas de figure sont entre autre les fichiers mal formés ou signés
        return '';
    }
   
    //Libérer les ressources
    unset($client);
    unset($loader);
	
	// Si on a trouvé du texte
	if ($contenu) {
		$infos['contenu'] = $contenu;
	}
	
    return $infos;
}
