<?php

/**
 * Fonctions utiles au plugin jQuery Vector Maps.
 *
 * @plugin     jQuery Vector Maps
 *
 * @copyright  2015
 * @author     Teddy Payet
 * @licence    GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}
/**
 * Cette fonction permet de format le texte selon la 'norme' javascript
 * attendu par la librairie jQVmap.
 *
 * @param string $texte La valeur qui doit être retranscrite.
 *
 * @return string Le texte est retourné formaté.
 */
function jqvmap_format($texte = '')
{
    if ($texte == '' or empty($texte)) {
        return false;
    }
    if ($texte == 'null') {
        return 'null';
    }
    if (preg_match('/,/', $texte)) {
        $texte = explode(',', $texte);
        foreach ($texte as $key => $hexa) {
            $texte[$key] = "'".trim($hexa)."'";
        }

        return implode(', ', $texte);
    }

    return "'$texte'";
}

/**
 * Lister les xml présents dans `jqvmap_xml/`
 * Le nom retourné sera le nom du xml sans l'extension.
 *
 * @return array Tableau contennant le nom des fichiers xml.
 */
function lister_maps_xml()
{
    $liste = find_all_in_path('jqvmap_xml/', '.xml$');
    if (is_array($liste) and count($liste) > 0) {
        foreach ($liste as $key => $value) {
            $liste[] = $key;
            unset($liste[$key]);
        }

        return $liste;
    }

    return array();
}

function map_importer_bdd($fichier_xml)
{
    include_spip('base/abstract_sql');
    $valeurs = array();
    $valeurs = map_xml_formater($fichier_xml);

    if (isset($valeurs['map']['titre']) and isset($valeurs['map']['code_map'])) {
        $deja = sql_countsel('spip_maps', array('titre' => $valeurs['map']['titre'], 'code_map' => $valeurs['map']['code_map']));
    }

    return $valeurs;
}
/**
 * On reformate le fichier xml passé.
 *
 * @param string $fichier_xml Nom du fichier
 *
 * @uses spip_xml_load()
 *
 * @return array
 *               Retourne le tableau contenant le xml formaté
 *               en array('map' => array(), 'vectors' => array())
 */
function map_xml_formater($fichier_xml)
{
    include_spip('inc/utils');
    include_spip('inc/xml');
    $contenu_xml = array('map' => array(),'vectors' => array());
    $chemin_fichier = find_all_in_path('jqvmap_xml/', $fichier_xml);
    $chemin_fichier = $chemin_fichier[$fichier_xml];
    $contenu_xml_tmp = spip_xml_load($chemin_fichier);
    foreach ($contenu_xml_tmp['map'][0] as $key => $value) {
        switch ($key) {
            case 'titre':
                if (!empty(trim($value[0]))) {
                    $contenu_xml['map']['titre'] = trim($value[0]);
                }
                break;
            case 'descriptif':
                if (!empty(trim($value[0]))) {
                    $contenu_xml['map']['descriptif'] = trim($value[0]);
                }
                break;
            case 'width':
                if (!empty(trim($value[0]))) {
                    $contenu_xml['map']['width'] = trim($value[0]);
                }
                break;
            case 'height':
                if (!empty(trim($value[0]))) {
                    $contenu_xml['map']['height'] = trim($value[0]);
                }
                break;
            case 'code_map':
                if (!empty(trim($value[0]))) {
                    $contenu_xml['map']['code_map'] = trim($value[0]);
                }
                break;
            case 'background_color':
                if (!empty(trim($value[0]))) {
                    $contenu_xml['map']['background_color'] = trim($value[0]);
                }
                break;
            case 'border_color':
                if (!empty(trim($value[0]))) {
                    $contenu_xml['map']['border_color'] = trim($value[0]);
                }
                break;
            case 'border_opacity':
                if (!empty(trim($value[0]))) {
                    $contenu_xml['map']['border_opacity'] = trim($value[0]);
                }
                break;
            case 'border_width':
                if (!empty(trim($value[0]))) {
                    $contenu_xml['map']['border_width'] = trim($value[0]);
                }
                break;
            case 'color':
                if (!empty(trim($value[0]))) {
                    $contenu_xml['map']['color'] = trim($value[0]);
                }
                break;
            case 'enable_zoom':
                if (!empty(trim($value[0]))) {
                    $contenu_xml['map']['enable_zoom'] = trim($value[0]);
                }
                break;
            case 'hover_color':
                if (!empty(trim($value[0]))) {
                    $contenu_xml['map']['hover_color'] = trim($value[0]);
                }
                break;
            case 'hover_opacity':
                if (!empty(trim($value[0]))) {
                    $contenu_xml['map']['hover_opacity'] = trim($value[0]);
                }
                break;
            case 'normalize_function':
                if (!empty(trim($value[0]))) {
                    $contenu_xml['map']['normalize_function'] = trim($value[0]);
                }
                break;
            case 'scale_colors':
                if (!empty(trim($value[0]))) {
                    $contenu_xml['map']['scale_colors'] = trim($value[0]);
                }
                break;
            case 'selected_color':
                if (!empty(trim($value[0]))) {
                    $contenu_xml['map']['selected_color'] = trim($value[0]);
                }
                break;
            case 'selected_region':
                if (!empty(trim($value[0]))) {
                    $contenu_xml['map']['selected_region'] = trim($value[0]);
                }
                break;
            case 'show_tooltip':
                if (!empty(trim($value[0]))) {
                    $contenu_xml['map']['show_tooltip'] = trim($value[0]);
                }
                break;
            case 'data_name':
                if (!empty(trim($value[0]))) {
                    $contenu_xml['map']['data_name'] = trim($value[0]);
                }
                break;
            case 'vector':
                foreach ($value as $key => $vector) {
                    $vector_tmp = array();
                    $number = $key;
                    foreach ($vector as $vector_champ => $vector_value) {
                        switch ($vector_champ) {
                            case 'titre':
                                if (!empty(trim($vector_value[0]))) {
                                    $vector_tmp[$number]['titre'] = trim($vector_value[0]);
                                }
                                break;
                            case 'descriptif':
                                if (!empty(trim($vector_value[0]))) {
                                    $vector_tmp[$number]['descriptif'] = trim($vector_value[0]);
                                }
                                break;
                            case 'code_vector':
                                if (!empty(trim($vector_value[0]))) {
                                    $vector_tmp[$number]['code_vector'] = trim($vector_value[0]);
                                }
                                break;
                            case 'data':
                                if (!empty(trim($vector_value[0]))) {
                                    $vector_tmp[$number]['data'] = trim($vector_value[0]);
                                }
                                break;
                            case 'path':
                                if (!empty(trim($vector_value[0]))) {
                                    $vector_tmp[$number]['path'] = trim($vector_value[0]);
                                }
                                break;
                            case 'url_site':
                                if (!empty(trim($vector_value[0]))) {
                                    $vector_tmp[$number]['url_site'] = trim($vector_value[0]);
                                }
                                break;
                            default:
                                break;
                        }
                    }
                    $contenu_xml['vectors'] = array_merge($contenu_xml['vectors'], $vector_tmp);
                }
                break;
            default:
                break;
        }
    }

    return $contenu_xml;
}

function map_bdd_verifier($champs, $where, $objet = 'map')
{
    $valeurs = array();
    $valeurs = map_xml_formater($fichier_xml);
    if (isset($valeurs['map']['titre']) and isset($valeurs['map']['code_map'])) {
        $deja = sql_countsel('spip_maps', array('titre' => $valeurs['map']['titre'], 'code_map' => $valeurs['map']['code_map']));

        return false;
    }

    // Il faut faire une vérification aussi sur les vectors
}
