<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/session');


/**
 * Fonction qui renvoie les documents uploader dans un tableau
 * utilisable par objet_associer
 *
 * @access public
 * @return mixed
 */
function saisie_upload_get() {
    // récupérer les documents en session
    $documents = session_get('upload');
    // On va renvoyer un tableau formaté pour passer dans objet_associer
    return array('document' => $documents);
}

/**
 * Détruire la session d'upload quand on à terminé
 *
 * @access public
 */
function saisie_upload_terminer() {
    session_set('upload', null);
}

/**
 * Traiter une saisie upload.
 * Basiquement, on associe les documents à un objet spécifique
 * Ensuite on nettoye la session
 *
 * @param mixed $objet
 * @param mixed $id_objet
 * @access public
 */
function saisie_upload_traiter($objet, $id_objet) {

    include_spip('action/editer_objet');
    include_spip('action/editer_liens');

    // Récupérer les documents et associer à l'objet
    $documents = saisie_upload_get();
    objet_associer(
        $documents,
        array($objet => $id_objet)
    );

    // Le lien est fait, les documents ne doivent plus être en mode temporaire
    foreach($documents['document'] as $id_document) {
        objet_instituer('document', $id_document, array('statut' => 'publie'));
    }

    // Terminer l'upload en nettoyant la session
    saisie_upload_terminer();
}