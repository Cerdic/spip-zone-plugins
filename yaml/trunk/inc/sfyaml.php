<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Encode une structure de données PHP en une chaîne YAML.
 * Utilise pour cela la librairie symfony/yaml (branche v1) qui n'est plus maintenue mais
 * conservée par souci de compatibilité.
 *
 * @param mixed $structure
 *        Structure PHP, tableau, chaine... à convertir en YAML.
 * @param array $options
 *        Tableau associatif des options du dump. Cette librairie accepte:
 *        - 'inline'      : niveau à partir duquel la présentation du YAML devient inline, 2 par défaut.
 *        - 'indent' : nombre d'espaces pour chaque niveau d'indentation, 2 par défaut.
 *
 * @return string
 *        Chaîne YAML construite.
 */
function sfyaml_yaml_encode($structure, $options = array()) {

	require_once _DIR_PLUGIN_YAML . 'sfyaml/sfYaml.php';
	require_once _DIR_PLUGIN_YAML . 'sfyaml/sfYamlDumper.php';

	// Traitement des options
	if (empty($options['inline']) or (isset($options['inline']) and !is_int($options['inline']))) {
		$options['inline'] = 3;
	}
	if (empty($options['indent']) or (isset($options['indent']) and !is_int($options['indent']))) {
		$options['indent'] = 2;
	}

	// On crée l'objet de dump avec le paramètre d'indentation.
	$yaml = new sfYamlDumper($options['indent']);

	return $yaml->dump($structure, $options['inline']);
}


/**
 * Décode une chaîne YAML en une structure de données PHP adaptée.
 * Utilise pour cela la librairie symfony/yaml (branche v1) qui n'est plus maintenue mais
 * conservée par souci de compatibilité.
 *
 * @param string $input
 *        La chaîne YAML à décoder.
 * @param array $options
 *        Tableau associatif des options du parsing. Cette librairie accepte:
 *        - 'show_error' : indicateur d'affichage des erreurs de parsing, false par défaut.
 * @param bool   $show_error
 *        Indicateur d'affichage des erreurs de parsing.
 *
 * @return bool|mixed
 *        Structure PHP produite par le parsing de la chaîne YAML.
 */
function sfyaml_yaml_decode($input, $options = array()) {

	require_once _DIR_PLUGIN_YAML . 'sfyaml/sfYaml.php';
	require_once _DIR_PLUGIN_YAML . 'sfyaml/sfYamlParser.php';

	// On crée l'objet de parsing.
	$yaml = new sfYamlParser();

	$parsed = false;
	try {
		$parsed = $yaml->parse($input);
	} catch (Exception $exception) {
		// On garde la compatibilité ascendante avec l'ancien argument $show_error qui a été remplacé par $options
		// mais on inverse la valeur par défaut.
		if ((is_bool($options) and $options) or (!empty($options['show_error']))) {
			throw new InvalidArgumentException(sprintf('Unable to parse string: %s', $exception->getMessage()));
		}

		// Pour compenser l'absence par défaut d'erreurs, on loge l'erreur dans les logs SPIP.
		spip_log("Erreur d'analyse YAML : " . $exception->getMessage(), 'yaml' . _LOG_ERREUR);
	}

	return $parsed;
}
