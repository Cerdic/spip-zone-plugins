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
        // On supprime également les éléments vides, cela évite les surprises quand on vide un champ du formulaire de configuration
        if (in_array($key, $black_list) or empty($value))
            unset($config[$key]);

        // Variable en int, on les veux vraiment en int dans le json et pas en string
        if (is_numeric($value))
            $config[$key] = intval($value);

    }

    return json_encode($config);
}