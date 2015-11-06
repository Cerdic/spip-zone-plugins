<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_send_upload_dist($arg=null) {
    if (is_null($arg)){
        $securiser_action = charger_fonction('securiser_action', 'inc');
        $arg = $securiser_action();
    }

    // On va uploader les documents avec un mode "tmp"
    // Cela permettra plus tard de faire un nettoyage de la base de donnée
    include_spip('uploadhtml5_fonctions');
    $documents = uploadhtml5_uploader_document('', 0, $_FILES, 'new', 'auto');

    // Les document ne sont uploader que 1 par 1
    $id_document = intval($documents[0]);

    // On force le passage en statut tmp.
    // On ne passe pas par l'API pour contourner les autorisations
    sql_update('spip_documents', array('statut' => sql_quote('tmp')), 'id_document='.$id_document);

    // On stock l'upload en session
    include_spip('inc/session');
    $uploads = session_get('upload') ?: array();
    $uploads[] = $id_document;
    session_set('upload', $uploads);
}
