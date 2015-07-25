<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction destinnée à transformée une config en config json
 * utilisable directement dans un javascript
 * #CONFIG{ma_config}|slick_config_to_json{ma_black_list}
 *
 * @param string $config la config SPIP, soit le résultat de la balise #CONFIG
 * @param string|array $black_list Les éléments qui ne doivent pas être utilisé dans le json
 * @access public
 */
function filtre_slick_config_to_json_dist($config, $black_list = array()) {

    $config = unserialize($config);

    if (!is_array($black_list))
        $black_list = explode(',', $black_list);

    // On va boucler sur la configuration pour traiter quelques variables
    foreach($config as $key=>$value) {

        // On cible les éventuel true/false et on convertir
        if ($value == 'true' or $value == 'false')
            $config[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);

        // Si un élément ce trouve dans la black_list c'est qu'on n'en veux pas de le json.
        // on supprime la variable
        if (in_array($key, $black_list))
            unset($config[$key]);

    }

    return json_encode($config);
}