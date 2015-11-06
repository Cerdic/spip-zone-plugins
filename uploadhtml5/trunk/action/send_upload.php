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

    foreach($_FILES as $fichier) {

        $cache_fichier = sous_repertoire(_DIR_CACHE, 'uploadhtml5').'/'.$fichier['name'];

        $contenu = spip_file_get_contents($fichier['tmp_name']);
        ecrire_fichier($cache_fichier, $contenu);

        // On va stocker en session le chemin du fichier
        $file = session_get('file');
        $file[] = $cache_fichier;
        session_set('file', $file);

        spip_log($file, 'uploadhtml');
    }
}
