<?php

function formulaires_importer_map_charger_dist()
{
    // Contexte du formulaire.
    $contexte = array();
    $contexte['maps_xml'] = (_request('maps_xml')) ? _request('maps_xml') : '';

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
function formulaires_importer_map_verifier_dist()
{
    $erreurs = array();
    $maps_xml = _request('maps_xml');
    $upload_xml = $_FILES['upload_xml'];
    if (isset($maps_xml) and trim($maps_xml) == '' and empty($_FILES['upload_xml']['name'])) {
        $erreurs['maps_xml'] = _T('info_obligatoire');
    }

    echo '<pre>';
    var_dump($upload_xml);
    echo '</pre>';
    if (!empty($upload_xml['name']) and !preg_match('/\.xml$/', $upload_xml['name'])) {
        $erreurs['upload_xml'] = _T('map:erreur_upload_xml_type');
    } else {
        $upload_dir = _DIR_TMP.'jqvmap_xml/';
        // Vérifier que le répertoire d'upload est bien présent.
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir);
            chmod($upload_dir, _SPIP_CHMOD);
        }
        move_uploaded_file($upload_xml['tmp_name'], $upload_dir.$upload_xml['name']);
    }

    return $erreurs;
}

function formulaires_importer_map_traiter_dist()
{
    //Traitement du formulaire.
    include_spip('base/abstract_sql');
    $maps_xml = _request('maps_xml');
    if (isset($maps_xml) and trim($maps_xml) != '') {
        $max_xml_formater = map_xml_formater($maps_xml);
    }
    // Donnée de retour.
    return array(
            'editable' => true,
            'message_ok' => '',
            'redirect' => '',
    );
}
