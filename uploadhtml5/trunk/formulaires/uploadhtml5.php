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

function formulaires_uploadhtml5_charger_dist($objet, $id_objet) {
    // Contexte du formulaire.
    $contexte = array(
        '' => '',
    );

    return $contexte;
}

/*
 *   Fonction de vérification, cela fonction avec un tableau d'erreur.
 *   Le tableau est formater de la sorte:
 *   if (!_request('NomErreur')) {
 *       $erreurs['message_erreur'] = '';
 *       $erreurs['NomErreur'] = '';
 *   }
 *   Pensez à utiliser _T('info_obligatoire'); pour les éléments obligatoire.
 */
function formulaires_uploadhtml5_verifier_dist($objet, $id_objet) {
    $erreurs = array();

    return $erreurs;
}

function formulaires_uploadhtml5_traiter_dist($objet, $id_objet) {

    // upload de la dropzone
    uploader_document($_FILES, $objet, $id_objet);

    // Donnée de retour.
    return array(
        'editable' => true
    );
}