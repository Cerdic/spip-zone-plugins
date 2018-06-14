<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

require_once _DIR_PLUGIN_YAML . 'vendor/autoload.php';

/**
 * Encode une structure de données PHP en une chaîne YAML.
 * Utilise pour cela la librairie symfony/yaml (branche v1) qui n'est plus maintenue mais
 * conservée par souci de compatibilité.
 *
 * @param mixed $structure
 *        Structure PHP, tableau, chaine... à convertir en YAML.
 * @param array $options
 *        Tableau associatif des options du dump. Cette librairie accepte:
 *        - 'indent' : nombre d'espaces pour chaque niveau d'indentation, 2 par défaut.
 *
 * @return string
 *        Chaîne YAML construite.
 */
function spyc_yaml_encode($structure, $options = array()) {

	// Traitement des options
	if (empty($options['indent']) or (isset($options['indent']) and !is_int($options['indent']))) {
		$options['indent'] = 2;
	}

	return Spyc::YAMLDump($structure, $options['indent'], 0, true);
}


/**
 * Décode une chaîne YAML en une structure de données PHP adaptée.
 * Utilise pour cela la librairie symfony/yaml (branche v1) qui n'est plus maintenue mais
 * conservée par souci de compatibilité.
 *
 * @param string $input
 *        La chaîne YAML à décoder.
 * @param array $options
 *        Tableau associatif des options du parsing. Cette librairie n'accepte aucune option.
 *
 * @return bool|mixed
 *        Structure PHP produite par le parsing de la chaîne YAML.
 */
function spyc_yaml_decode($input, $options = array()) {

	return Spyc::YAMLLoadString($input);
}
