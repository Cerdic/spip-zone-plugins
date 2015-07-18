<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Formulaire d'upload en html5
 *
 * @param mixed $objet Objet cible
 * @param mixed $id_objet Id de l'objet cible
 * @param string $mode mode d'insertion des objets
 * @param string $ajaxReload Objet ajax à recharger quand une image est uploadé
 * @param mixed $args Tableau d'option
 *        "redirect" => Faire une redirection après l'upload de tout les éléménts.
 *        "acceptedFiles" => limiter les types de fichier accepter
 *
 * @access public
 * @return mixed
 */
function formulaires_uploadhtml5_charger_dist($objet, $id_objet, $mode = 'auto', $ajaxReload = '', $args = array()) {

    // Convertir les acceptedFiles
    if (!empty($args['acceptedFiles']))
        $args['acceptedFiles'] = trouver_mime_type($args['acceptedFiles']);

    // Contexte de base, qui pourra être surcharger par $args
    $contexte = array(
        'ajaxReload' => $ajaxReload,
        'paramName' => 'file',
        'id' => 'dropzonespip'
    );

    // Fusionner args avec le contexte
    $contexte = array_merge($contexte, $args);

    return $contexte;
}

function formulaires_uploadhtml5_traiter_dist($objet, $id_objet, $mode = 'auto', $ajaxReload = '', $args = array()) {

    // upload de la dropzone
    uploadhtml5_uploader_document($objet, $id_objet, $_FILES, 'new', $mode);

    // Donnée de retour.
    return array(
        'editable' => true
    );
}
