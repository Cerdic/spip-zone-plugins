<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_uploadhtml5_logo_saisies_dist($objet, $id_objet, $ajaxReload = '') {
    $saisies = array(
        array(
            'saisie' => 'input',
            'options' => array(
                'nom' => 'files_logo[]',
                'label' => _T('uploadhtml5:upload'),
                'id' => 'fileupload_logo',
                'type' => 'file',
                'attributs' => 'multiple'
            )
        )
    );
    return $saisies;
}

function formulaires_uploadhtml5_logo_charger_dist($objet, $id_objet, $ajaxReload = '') {
    return array('ajaxReload' => $ajaxReload);
}

function formulaires_uploadhtml5_logo_traiter_dist($objet, $id_objet, $ajaxReload = '') {
    // upload de la dropzone
    uploadhtml5_uploader_logo($objet, $id_objet, $_FILES['file_logo']['tmp_name']);

    // Donnée de retour.
    return array(
        'editable' => true
    );
}