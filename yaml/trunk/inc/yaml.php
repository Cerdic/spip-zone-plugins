<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

# textwheel fournit yaml_decode() aussi...
# include_spip('inc/yaml-mini');

# wrapper de la class sfYAML pour SPIP
#
# fournit deux fonctions pour YAML,
# analogues a json_encode() et json_decode
#
# Regle de dev: ne pas se rendre dependant de la lib sous-jacente

if (!defined('_LIB_YAML')) {
	/**
	 * Les valeurs possibles sont :
	 * - 'sfyaml' pour l'ancienne librairie symfony v1 (compatibilité ascendante, par défaut)
	 * - 'symfony' pour le composant YAML le plus récent de Symfony
	 * - 'spyc' pour la librairie YAML spyc la plus récente
	 * - 'libyaml' pour le composant PECL basé sur la librairie libYAML écrite en C.
	 */
	define('_LIB_YAML', 'sfyaml');
}

/**
 * @api
 *
 * @param mixed $structure
 *        Structure PHP, tableau, chaine... à convertir en YAML.
 * @param array $options
 *        Tableau associatif d'options standard ou spécifique à une librairie donnée.
 *
 * @return string
 *        Chaîne YAML construite, prête pour être éventuellement écrite dans un fichier.
 */
function yaml_encode($structure, $options = array()) {

	// Déterminer la librairie à utiliser
	$librairie = _LIB_YAML;
	if (!empty($options['library'])) {
		$librairie = $options['library'];
	}

	// Déterminer la fonction à appeler à partir de la librairie utilisée.
	require_once _DIR_PLUGIN_YAML . "inc/${librairie}.php";
	$encoder = "${librairie}_yaml_encode";

	return $encoder($structure, $options);
}


if (!function_exists('yaml_decode')) {
	/**
	 * @api
	 *
	 * @param string $input
	 *        La chaîne YAML à décoder.
	 * @param array $options
	 *        Tableau associatif des options du parsing.
	 *        - 'show_error' : indicateur d'affichage des erreurs de parsing, false par défaut.
	 *
	 * @return mixed
	 */
	function yaml_decode($input, $options = array()) {

		// Déterminer la librairie à utiliser
		$librairie = _LIB_YAML;
		if (!empty($options['library'])) {
			$librairie = $options['library'];
		}

		// Déterminer la fonction à appeler à partir de la librairie utilisée.
		require_once _DIR_PLUGIN_YAML . "inc/${librairie}.php";
		$decoder = "${librairie}_yaml_decode";

		return $decoder($input, $options);
	}
}

/*
 * @api
 *
 * Decode un fichier en utilisant yaml_decode
 * @param string $fichier
 */
/**
 * @param $fichier
 *
 * @return array|mixed
 */
function yaml_decode_file($fichier, $options = array()) {

	$retour = array();

	// Traitement des options
	if (empty($options['include'])) {
		$options['include'] = false;
	}

	// Lecture du fichier YAML.
	lire_fichier($fichier, $yaml);

	// Décodage du contenu YAML en structure de données PHP.
	if ($yaml) {
		$retour = yaml_decode($yaml, $options);
		if ($options['include']) {
			$retour = decode_inclusions($retour, $options);
		}
	}

	return $retour;
}

/*
 * @internal
 *
 * Charge les inclusions de YAML dans un tableau
 * Les inclusions sont indiquees dans le tableau via la valeur 'inclure:rep/fichier.yaml' ou rep indique le chemin relatif.
 * On passe donc par find_in_path() pour trouver le fichier
 * @param array $tableau
 */
function decode_inclusions($parsed, $options = array()) {

	if (is_array($parsed)) {
		$retour = array();
		foreach ($parsed as $cle => $valeur) {
			if (is_string($valeur) && substr($valeur, 0, 8) == 'inclure:' && substr($valeur, -5) == '.yaml') {
				$inclusion = find_in_path(substr($valeur, 8));
				if ($inclusion) {
					$retour = array_merge($retour, yaml_decode_file($inclusion, $options));
				} else {
					$retour = array_merge($retour, array($cle => $valeur));
				}
			} elseif (is_array($valeur)) {
				$retour = array_merge($retour, array($cle => decode_inclusions($valeur, $options)));
			} else {
				$retour = array_merge($retour, array($cle => $valeur));
			}
		}
	} elseif (is_string($parsed) && substr($parsed, 0, 8) == 'inclure:' && substr($parsed, -5) == '.yaml') {
		$inclusion = find_in_path(substr($parsed, 8));
		if ($inclusion) {
			$retour = yaml_decode_file($inclusion, $options);
		} else {
			$retour = $parsed;
		}
	} else {
		$retour = $parsed;
	}

	return $retour;
}

/*
 * @api
 *
 * Charge les inclusions de YAML dans un tableau
 * Les inclusions sont indiquees dans le tableau via la valeur 'inclure:rep/fichier.yaml' ou rep indique le chemin relatif.
 * On passe donc par find_in_path() pour trouver le fichier
 * @param array $tableau
 */
function yaml_charger_inclusions($tableau, $options = array()) {

	// Eviter de traiter l'inclure avec la nouvelle approche
	if (isset($options['include'])) {
		unset($options['include']);
	}

	if (is_array($tableau)) {
		$retour = array();
		foreach ($tableau as $cle => $valeur) {
			if (is_string($valeur) && substr($valeur, 0, 8) == 'inclure:' && substr($valeur, -5) == '.yaml') {
				$inclusion = find_in_path(substr($valeur, 8));
				if ($inclusion) {
					$retour = array_merge($retour, yaml_charger_inclusions(yaml_decode_file($inclusion), $options));
				} else {
					$retour = array_merge($retour, array($cle => $valeur));
				}
			} elseif (is_array($valeur)) {
				$retour = array_merge($retour, array($cle => yaml_charger_inclusions($valeur, $options)));
			} else {
				$retour = array_merge($retour, array($cle => $valeur));
			}
		}
	} elseif (is_string($tableau) && substr($tableau, 0, 8) == 'inclure:' && substr($tableau, -5) == '.yaml') {
		$inclusion = find_in_path(substr($tableau, 8));
		if ($inclusion) {
			$retour = yaml_charger_inclusions(yaml_decode_file($inclusion, $options));
		} else {
			$retour = $tableau;
		}
	} else {
		$retour = $tableau;
	}

	return $retour;
}
