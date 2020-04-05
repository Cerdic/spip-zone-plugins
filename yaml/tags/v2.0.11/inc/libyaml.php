<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Encode une structure de données PHP en une chaîne YAML.
 * Utilise pour cela la librairie PECL libYAML (encapsulation de la librairie C du même nom).
 * Une fois installée l'extension Apache, les fonctions de la librairie s'utilise nativement en PHP.
 *
 * @param mixed $structure
 *        Structure PHP, tableau, chaine... à convertir en YAML.
 * @param array $options
 *        Tableau associatif des options du dump. Cette librairie accepte:
 *        - 'indent'  : nombre d'espaces pour chaque niveau d'indentation, 2 par défaut.
 *
 * @return string
 *        Chaîne YAML construite.
 */
function libyaml_yaml_encode($structure, $options = array()) {

	// Traitement des options
	// -- par défaut la librairie positionne l'indentation à 2 qui est la valeur par défaut
	//    général du plugin.
	if (!empty($options['indent']) and is_int($options['indent'])) {
		ini_set('yaml.output_indent', strval($options['indent']));
	}

	// Encodage en YAML
	$yaml = yaml_emit($structure);

	// Suppression des --- et ... de début et fin.
	$yaml = preg_replace(array("#^\-\-\-.*?\n#s", "#\.\.\.\s*$#"), array('', ''), $yaml);

	return $yaml;
}


/**
 * Décode une chaîne YAML en une structure de données PHP adaptée.
 * Utilise pour cela la librairie PECL libYAML (encapsulation de la librairie C du même nom).
 * Une fois installée l'extension Apache, les fonctions de la librairie s'utilise nativement en PHP.
 *
 * @param string $input
 *        La chaîne YAML à décoder.
 * @param array $options
 *        Tableau associatif des options du parsing. Cette librairie n'accepte aucune option.
 *
 * @return bool|mixed
 *        Structure PHP produite par le parsing de la chaîne YAML.
 */
function libyaml_yaml_decode($input, $options = array()) {

	return yaml_parse($input,0);
}
