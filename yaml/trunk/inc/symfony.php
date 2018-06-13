<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

require_once _DIR_PLUGIN_YAML . 'vendor/autoload.php';
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * Encode une structure de données PHP en une chaîne YAML.
 * Utilise pour cela la librairie symfony/yaml (branche v4) qui est la plus récente.
 *
 * @param mixed $structure
 *        Structure PHP, tableau, chaine... à convertir en YAML.
 * @param array $options
 *        Tableau associatif des options du dump. Cette librairie accepte:
 *        - 'inline'      : niveau à partir duquel la présentation du YAML devient inline, 2 par défaut.
 *        - 'indent' : nombre d'espaces pour chaque niveau d'indentation, 2 par défaut.
 *        - 'flags'       : combinaison des flags DUMP_* supportés par la fonction (voir documentation).
 *
 * @return string
 *        Chaîne YAML construite.
 */
function symfony_yaml_encode($structure, $options = array()) {

	// Traitement des options du dump
	if (empty($options['inline']) or (isset($options['inline']) and !is_int($options['inline']))) {
		$options['inline'] = 3;
	}
	if (empty($options['indent']) or (isset($options['indent']) and !is_int($options['indent']))) {
		$options['indent'] = 2;
	}
	if (!isset($options['flags']) or (isset($options['flags']) and !is_int($options['flags']))) {
		$options['flags'] = 0;
	}

	$dump = Yaml::dump($structure, $options['inline'], $options['indent'], $options['flags']);
	return $dump;
}


/**
 * Décode une chaîne YAML en une structure de données PHP adaptée.
 * Utilise pour cela la librairie symfony/yaml (branche v4) qui est la plus récente.
 *
 * @param string $input
 *        La chaîne YAML à décoder.
 *        Tableau associatif des options du parsing. Cette librairie accepte:
 *        - 'include'    : si vrai indique qu'il faut traiter les inclusions YAML. Cela implique de forcer le flag
 *                         PARSE_CUSTOM_TAGS
 *        - 'flags'      : combinaison des flags PARSE_* supportés par la fonction (voir documentation).
 *        - 'show_error' : indicateur d'affichage des erreurs de parsing, false par défaut.
 *
 * @return bool|mixed
 *        Structure PHP produite par le parsing de la chaîne YAML.
 */
function symfony_yaml_decode($input = true, $options = array()) {

	$parsed = false;

	// Traitement des options du parsing.
	$flags = 0;
	if (isset($options['flags']) and is_int($options['flags'])) {
		$flags = $options['flags'];
	}

	try {
		$parsed = Yaml::parse($input, $flags);
	} catch (ParseException $exception) {
		if ((is_bool($options) and $options) or (!empty($options['show_error']))) {
			printf('Unable to parse the YAML string: %s', $exception->getMessage());
		}

		// Pour compenser l'absence par défaut d'erreurs, on loge l'erreur dans les logs SPIP.
		spip_log("Erreur d'analyse YAML : " . $exception->getMessage(), 'yaml' . _LOG_ERREUR);
	}

	return $parsed;
}
