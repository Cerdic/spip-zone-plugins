<?php

if (!defined("_ECRIRE_INC_VERSION")) {
    return;
}

/**
 * On analyse le xml issu du plugin Info SPIP
 * ou respectant cette norme xml et on retourne le tableau correspondant
 * au formulaire de création d'un site de projets.
 *
 * @uses recuperer_page
 * @uses charger_fonction
 * @param string $url
 *          URL du webservice
 * @return array $valeurs
 */
function inc_analyser_webservice_dist($url)
{
    include_spip('iterateur/data');
    include_spip('inc/distant');
    $valeurs = array();
    $page = recuperer_page($url);
    $convertir = charger_fonction('xml_to_array', 'inc');
    $xml = $convertir($page);
    $valeurs['webservice'] = $url;
    $parse_url = parse_url($url);
    parse_str($parse_url['query'], $query);
    // echo "<pre>";
    // var_dump($xml);
    // echo "</pre>";

    if (isset($query['cle'])) {
        $valeurs['uniqid'] = $query['cle'];
    }

    if (is_array($xml)) {
        foreach ($xml as $key => $value) {
            switch ($key) {
                case 'nom_site':
                    $valeurs['titre'] = $xml[$key]['value'];
                    break;
                case 'logiciel':
                    $valeurs['logiciel_nom'] = $xml[$key]['nom'];
                    $valeurs['logiciel_version'] = $xml[$key]['version'];
                    $valeurs['logiciel_revision'] = $xml[$key]['revision'];
                    break;
                case 'date_creation':
                    $valeurs['date_creation'] = $xml[$key]['value'];
                    break;
                case 'fo':
                    $valeurs['fo_url'] = $xml[$key]['url'];
                    break;
                case 'type_site':
                    $valeurs['type_site'] = $xml[$key]['value'];
                    break;
                case 'applicatif':
                    $valeurs['serveur_nom'] = $xml[$key]['nom'];
                    $valeurs['serveur_path'] = $xml[$key]['path'];
                    $valeurs['serveur_port'] = $xml[$key]['port'];
                    $valeurs['serveur_logiciel'] = $xml[$key]['logiciel'];
                    break;
                case 'apache':
                    foreach ($xml[$key][0] as $key_module => $value_module) {
                        $valeurs['apache_modules'][] = $value_module['nom'];
                    }
                    $valeurs['apache_modules'] = implode(', ', $valeurs['apache_modules']);
                    break;
                case 'php':
                    foreach ($xml[$key][0] as $key_extension => $value_extension) {
                        $valeurs['php_extensions'][] = $value_extension['nom'];
                    }
                    $valeurs['php_extensions'] = implode(', ', $valeurs['php_extensions']);
                    $valeurs['php_version'] = $xml[$key]['version'];
                    $valeurs['php_memory'] = $xml[$key]['memory'];
                    break;
                case 'administrateurs':
                    foreach ($xml[$key][0] as $key => $value) {
                        $value = array_filter($value);
                        $valeurs['auteurs_admin'][] = implode('|', $value);
                    }
                    $valeurs['auteurs_admin'] = implode("\n", $valeurs['auteurs_admin']);
                    // $valeurs['auteurs_admin_length'] = strlen($valeurs['auteurs_admin']);
                    break;
                case 'webmestres':
                    foreach ($xml[$key][0] as $key => $value) {
                        $value = array_filter($value);
                        $valeurs['auteurs_webmestres'][] = implode('|', $value);
                    }
                    $valeurs['auteurs_webmestres'] = implode("\n", $valeurs['auteurs_webmestres']);
                    // $valeurs['auteurs_webmestres_length'] = strlen($valeurs['auteurs_webmestres']);
                    break;
                case 'plugins':
                    foreach ($xml[$key][0] as $key => $value) {
                        $value = array_filter($value);
                        $valeurs['logiciel_plugins'][] = implode('|', $value);
                    }
                    $valeurs['logiciel_plugins'] = implode("\n", $valeurs['logiciel_plugins']);
                    break;
                case 'sgbd':
                    $valeurs['sgbd_serveur'] = $xml[$key]['serveur'];
                    $valeurs['sgbd_port'] = $xml[$key]['port'];
                    $valeurs['sgbd_nom'] = $xml[$key]['nom'];
                    $valeurs['sgbd_type'] = $xml[$key]['type'];
                    $valeurs['sgbd_prefixe'] = $xml[$key]['prefixe'];
                    $valeurs['sgbd_version'] = $xml[$key]['version'];
                    break;
                default:
                    # code...
                    break;
            }
        }
    }
    ksort($valeurs);
    // echo "<pre>";
    // var_dump($valeurs);
    // echo "</pre>";
    return $valeurs;
}



?>