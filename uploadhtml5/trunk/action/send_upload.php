<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_send_upload_dist($arg=null) {
    if (is_null($arg)){
        $securiser_action = charger_fonction('securiser_action', 'inc');
        $arg = $securiser_action();
    }

    // On va temporairement écrire les fichiers dans le cache.
    include_spip('inc/flock');
    include_spip('inc/session');
    spip_log($_FILES, 'uploadhtml5');

    foreach($_FILES as $key => $fichier) {

        $cache_fichier = sous_repertoire(_DIR_CACHE, 'uploadhtml5').'/'.$fichier['name'].uniqid();

        $contenu = spip_file_get_contents($fichier['tmp_name']);
        ecrire_fichier($cache_fichier, $contenu);

        /**
         * On va stocker en session le chemin du fichier
         * et les donnée relative à $_FILES. Cela simulera un upload multiple
         *
         * Cependant, on caviarde le tmp_name pour utiliser le cache
         */
        $file = session_get('file');
        $file[$key]['name'][] = $fichier['name'];
        $file[$key]['type'][] = $fichier['type'];
        $file[$key]['tmp_name'][] = $cache_fichier;
        $file[$key]['error'][] = $fichier['error'];
        $file[$key]['size'][] = $fichier['size'];

        session_set('file', $file);

        spip_log($file, 'uploadhtml');
    }
}
