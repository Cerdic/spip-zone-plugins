<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_uploadhtml5_saisies_dist($objet, $id_objet) {
    $saisies = array(
        array(
            'saisie' => 'input',
            'options' => array(
                'nom' => 'files[]',
                'label' => _T('uploadhtml5:upload'),
                'id' => 'fileupload',
                'type' => 'file',
                'attributs' => 'multiple'
            )
        )
    );
    return $saisies;
}

function formulaires_uploadhtml5_traiter_dist($objet, $id_objet) {

    // upload de la dropzone
    uploadhtml5_uploader_document($_FILES, $objet, $id_objet);

    // Donnée de retour.
    return array(
        'editable' => true
    );
}