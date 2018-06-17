<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function ajouter_libyaml($librairies) {

	if (function_exists('yaml_parse')) {
		$librairies['libyaml'] = 'libYAML';
	}

	return $librairies;
}

function decoder_fichier_yaml($filename, $options = array()) {

	include_spip('inc/yaml');
	$file = find_in_path($filename);
	$timestamp_debut = microtime(true);

	$parsed = yaml_decode_file($file, $options);

	$timestamp_fin = microtime(true);
	$duree = ($timestamp_fin - $timestamp_debut) * 1000;

	return array(
		'lib' => sinon($options['library'], 'sfyaml'),
		'fichier' => $file,
		'duree' => "${duree} ms",
		'yaml' => $parsed);
}

function comparer_decodage($fichier, $libraries, $options = array()) {

	include_spip('inc/yaml');

	$compared = array();
	$first = array();
	foreach ($libraries as $_library) {
		$options = array_merge($options, array('library' => $_library));
		$parsed = yaml_decode_file($fichier, $options);
		if (!$compared) {
			$first = $parsed;
			$compared[$_library] = 'référence';
		} else {
			$difference = array_diff_assoc_recursive($first, $parsed, $fichier);
			$compared[$_library] = $difference ? bel_env($difference, true) : 'idem';
		}
	}

	return $compared;
}

function array_diff_assoc_recursive($array1, $array2, $fichier) {

    $difference = array();

    foreach ($array1 as $_key => $_value) {
        if (is_array($_value)) {
            if (!isset($array2[$_key]) || !is_array($array2[$_key])) {
                $difference[$_key] = $_value;
            } else {
                $new_diff = array_diff_assoc_recursive($_value, $array2[$_key], $fichier);
                if (!empty($new_diff))
                    $difference[$_key] = $new_diff;
            }
        } elseif (!is_array($array2)) {
        	echo $fichier, ' - ', $_key, ' - ', $_value, '<br />';
        } elseif (!array_key_exists($_key, $array2) || ($array2[$_key] !== $_value)) {
            $difference[$_key] = $_value;
        }
    }

    return $difference;
}
