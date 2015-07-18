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

    // Si l'option logo est activée, on renvoie un contexte figé qui correspond
    if (isset($args['logo']) and $args['logo'] == 'oui') {

        $contexte = array(
            'paramName' => 'file_logo',
            'ajaxReload' => $ajaxReload, // Le bloc ajax à rafraichir
            'maxFiles' => 1, // un seul fichier
            'acceptedFiles' => trouver_mime_type('logo'), // N'accepter que les logo défini par spip
            'id' => 'dropzonespip_logo' // Un ID spécifique pour les logo
        );

        // On ajoute le reste du contexte
        // Dans ce cas si, c'est $contexte qui supplante les informations
        // de $args car on force ces options pour les logos
        $contexte = array_merge($args, $contexte);

        return $contexte;
    }

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

    if (isset($args['logo']) and $args['logo'] == 'oui')
        uploadhtml5_uploader_logo($objet, $id_objet, $_FILES['file_logo']['tmp_name']);
    else
        uploadhtml5_uploader_document($objet, $id_objet, $_FILES, 'new', $mode);

    // Donnée de retour.
    return array(
        'editable' => true
    );
}
