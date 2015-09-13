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
        $resultat = trim($value[0]); // Quand PHP est en mode strict, il n'aime pas avoir un return d'une fonction
        switch ($key) {
            case 'titre':
                if (!empty($resultat)) {
                    $contenu_xml['map']['titre'] = $resultat;
                }
                break;
            case 'descriptif':
                if (!empty($resultat)) {
                    $contenu_xml['map']['descriptif'] = $resultat;
                }
                break;
            case 'width':
                if (!empty($resultat)) {
                    $contenu_xml['map']['width'] = $resultat;
                }
                break;
            case 'height':
                if (!empty($resultat)) {
                    $contenu_xml['map']['height'] = $resultat;
                }
                break;
            case 'code_map':
                if (!empty($resultat)) {
                    $contenu_xml['map']['code_map'] = $resultat;
                }
                break;
            case 'background_color':
                if (!empty($resultat)) {
                    $contenu_xml['map']['background_color'] = $resultat;
                }
                break;
            case 'border_color':
                if (!empty($resultat)) {
                    $contenu_xml['map']['border_color'] = $resultat;
                }
                break;
            case 'border_opacity':
                if (!empty($resultat)) {
                    $contenu_xml['map']['border_opacity'] = $resultat;
                }
                break;
            case 'border_width':
                if (!empty($resultat)) {
                    $contenu_xml['map']['border_width'] = $resultat;
                }
                break;
            case 'color':
                if (!empty($resultat)) {
                    $contenu_xml['map']['color'] = $resultat;
                }
                break;
            case 'enable_zoom':
                if (!empty($resultat)) {
                    $contenu_xml['map']['enable_zoom'] = $resultat;
                }
                break;
            case 'hover_color':
                if (!empty($resultat)) {
                    $contenu_xml['map']['hover_color'] = $resultat;
                }
                break;
            case 'hover_opacity':
                if (!empty($resultat)) {
                    $contenu_xml['map']['hover_opacity'] = $resultat;
                }
                break;
            case 'normalize_function':
                if (!empty($resultat)) {
                    $contenu_xml['map']['normalize_function'] = $resultat;
                }
                break;
            case 'scale_colors':
                if (!empty($resultat)) {
                    $contenu_xml['map']['scale_colors'] = $resultat;
                }
                break;
            case 'selected_color':
                if (!empty($resultat)) {
                    $contenu_xml['map']['selected_color'] = $resultat;
                }
                break;
            case 'selected_region':
                if (!empty($resultat)) {
                    $contenu_xml['map']['selected_region'] = $resultat;
                }
                break;
            case 'show_tooltip':
                if (!empty($resultat)) {
                    $contenu_xml['map']['show_tooltip'] = $resultat;
                }
                break;
            case 'data_name':
                if (!empty($resultat)) {
                    $contenu_xml['map']['data_name'] = $resultat;
                }
                break;
            case 'vector':
                foreach ($value as $key => $vector) {
                    $vector_tmp = array();
                    $number = $key;
                    foreach ($vector as $vector_champ => $vector_value) {
                        $resultat_vector = trim($vector_value[0]);
                        switch ($vector_champ) {
                            case 'titre':
                                if (!empty($resultat_vector)) {
                                    $vector_tmp[$number]['titre'] = $resultat_vector;
                                }
                                break;
                            case 'descriptif':
                                if (!empty($resultat_vector)) {
                                    $vector_tmp[$number]['descriptif'] = $resultat_vector;
                                }
                                break;
                            case 'code_vector':
                                if (!empty($resultat_vector)) {
                                    $vector_tmp[$number]['code_vector'] = $resultat_vector;
                                }
                                break;
                            case 'color':
                                if (!empty($resultat_vector)) {
                                    $vector_tmp[$number]['color'] = $resultat_vector;
                                }
                                break;
                            case 'data':
                                if (!empty($resultat_vector)) {
                                    $vector_tmp[$number]['data'] = $resultat_vector;
                                }
                                break;
                            case 'path':
                                if (!empty($resultat_vector)) {
                                    $vector_tmp[$number]['path'] = $resultat_vector;
                                }
                                break;
                            case 'url_site':
                                if (!empty($resultat_vector)) {
                                    $vector_tmp[$number]['url_site'] = $resultat_vector;
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
