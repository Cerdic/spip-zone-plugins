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
    $messsage_log = array();
    if (isset($maps_xml) and trim($maps_xml) == '' and empty($_FILES['upload_xml']['name'])) {
        $erreurs['maps_xml'] = _T('info_obligatoire');
        $messsage_log[] = "Aucun template de carte n'a été sélectionné.\n------";
    }

    if (!empty($upload_xml['name']) and !preg_match('/\.xml$/', $upload_xml['name'])) {
        $erreurs['upload_xml'] = _T('map:erreur_upload_xml_type');
        $messsage_log[] = 'Le fichier '.$upload_xml['name']." n'est pas au bon format.\n------";
    } else {
        $upload_dir = _DIR_TMP.'jqvmap_xml/';
        // Vérifier que le répertoire d'upload est bien présent.
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir);
            chmod($upload_dir, _SPIP_CHMOD);
        }
        move_uploaded_file($upload_xml['tmp_name'], $upload_dir.$upload_xml['name']);
        $messsage_log[] = "Le fichier $upload_xml a été enregistré dans ".$upload_dir.".\n------";
        set_request('upload_xml', $upload_dir.$upload_xml['name']);
    }
    if (count($messsage_log) > 0) {
        spip_log(implode("\n", $messsage_log), 'jqvmap');
    }

    return $erreurs;
}

function formulaires_importer_map_traiter_dist()
{
    //Traitement du formulaire.
    include_spip('base/abstract_sql');
    $fichier_importer = $maps_xml = _request('maps_xml');
    $upload_xml = _request('upload_xml');
    $messsage_log = array();
    $messsage_ok = array();

    if (preg_match('/.xml$/', $upload_xml)) {
        $_tmp = explode('/', $upload_xml);
        $fichier_importer = end($_tmp);
        $_tmp = '';
    }
    $_id_map = null;
    if (isset($fichier_importer) and !empty($fichier_importer)) {
        $map_xml_formater = map_xml_formater($fichier_importer);
        if (count($map_xml_formater['map']) > 0) {
            $where = 'titre='.sql_quote($map_xml_formater['map']['titre'])
                .' AND width='.sql_quote($map_xml_formater['map']['width'])
                .' AND height='.sql_quote($map_xml_formater['map']['height'])
                .' AND code_map='.sql_quote($map_xml_formater['map']['code_map']);
            $deja = sql_fetsel('id_map', 'spip_maps', $where);
            $messsage_log[] = "------\nMap\n".print_r($where, true)."\n------";
            if ($deja) {
                $_id_map = $deja['id_map'];
                sql_updateq('spip_maps', $map_xml_formater['map'], 'id_map='.$_id_map);
                $message_ok[] = _T('map:carte_maj');
            } else {
                // La carte aura un statut 'prepa'
                $map_xml_formater['map']['statut'] = 'prepa';
                $_id_map = sql_insertq('spip_maps', $map_xml_formater['map']);
                $messsage_log[] = "La carte $_id_map a été insérée en base : ".print_r($map_xml_formater['map'], true)."\n------";
                $message_ok[] = _T('map:carte_importee');
            }
            if (count($map_xml_formater['vectors']) > 0 and intval($_id_map) > 0) {
                foreach ($map_xml_formater['vectors'] as $key => $vector) {
                    $vector['id_map'] = $_id_map;
                    $where = 'titre='.sql_quote($vector['titre'])
                    .' AND code_vector='.sql_quote($vector['code_vector'])
                    .' AND id_map='.sql_quote($vector['id_map']);
                    $deja = sql_fetsel('id_vector', 'spip_vectors', $where);
                    $messsage_log[] = "------\nVector\n".print_r($where, true)."\n------";
                    if ($deja) {
                        // Le vecteur pour cette carte existe,
                        // alors on met à jour les infos.
                        $_id_vector = $deja['id_vector'];
                        sql_updateq('spip_vectors', $vector, 'id_vector='.$_id_vector);
                        $messsage_log[] = "Le vecteur $_id_vector a été mis à jour : ".print_r($vector, true)."\n------";
                    } else {
                        // Ce vecteur pour cette carte n'existe pas,
                        // alors on l'insère en BDD.
                        $_id_vector = sql_insertq('spip_vectors', $vector);
                        $messsage_log[] = "Le vecteur $_id_vector a été inséré en base : ".print_r($vector, true)."\n------";
                    }
                }
            }
        }
    }
    if (preg_match('/.xml$/', $upload_xml)) {
        include_spip('inc/flock');
        supprimer_fichier($upload_xml);
        $messsage_log[] = "Le fichier uploadé $upload_xml a été supprimé.\n------";
    }
    spip_log(implode("\n", $messsage_log), 'jqvmap');
    // Donnée de retour.
    return array(
            'editable' => true,
            'message_ok' => implode('"\n', $message_ok),
            'redirect' => generer_url_ecrire('maps'),
    );
}
