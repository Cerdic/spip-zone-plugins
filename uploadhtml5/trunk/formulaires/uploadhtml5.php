<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_uploadhtml5_saisies_dist($objet, $id_objet, $mode = 'auto', $ajaxReload = '') {
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

function formulaires_uploadhtml5_charger_dist($objet, $id_objet, $mode = 'auto', $ajaxReload = '') {
    return array('ajaxReload' => $ajaxReload);
}

function formulaires_uploadhtml5_traiter_dist($objet, $id_objet, $mode = 'auto', $ajaxReload = '') {

    // upload de la dropzone
    uploadhtml5_uploader_document($objet, $id_objet, $_FILES, 'new', $mode);

    // Donnée de retour.
    return array(
        'editable' => true
    );
}
