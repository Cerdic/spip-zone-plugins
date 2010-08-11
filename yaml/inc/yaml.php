<?php

# wrapper de la class sfYAML pour SPIP
#
# fournit deux fonctions pour YAML,
# analogues a json_encode() et json_decode
#
# Regle de dev: ne pas se rendre dependant de la lib sous-jacente

// Si on est en PHP4
 if (version_compare(PHP_VERSION, '5.0.0', '<'))
	define('_LIB_YAML','spyc-php4'); 
 else {
	// temporaire le temps de tester spyc
	define('_LIB_YAML','sfyaml'); 
	#define('_LIB_YAML','spyc'); 
}
/*
 * Encode n'importe quelle structure en yaml
 * @param $struct
 * @return string
 */
function yaml_encode($struct, $opt = array()) {
	// Si PHP4
	if (_LIB_YAML == 'spyc-php4') {
		require_once _DIR_PLUGIN_YAML.'spyc/spyc-php4.php';
		return Spyc::YAMLDump($struct);
	}
	// test temporaire
	if (_LIB_YAML == 'spyc') {
		require_once _DIR_PLUGIN_YAML.'spyc/spyc.php';
		return Spyc::YAMLDump($struct);
	}

	require_once _DIR_PLUGIN_YAML.'inc/yaml_sfyaml.php';
	return yaml_sfyaml_encode($struct, $opt);
}

/*
 * Decode un texte yaml, renvoie la structure
 * @param string $input
 */
function yaml_decode($input) {
	// Si PHP4
	if (_LIB_YAML == 'spyc-php4') {
		require_once _DIR_PLUGIN_YAML.'spyc/spyc-php4.php';
		return Spyc::YAMLLoad($input);
	}
	// test temporaire
	if (_LIB_YAML == 'spyc') {
		require_once _DIR_PLUGIN_YAML.'spyc/spyc.php';
		return Spyc::YAMLLoad($input);
	}

	require_once _DIR_PLUGIN_YAML.'inc/yaml_sfyaml.php';
	return yaml_sfyaml_decode($input);
}

/*
 * Decode un fichier en utilisant yaml_decode
 * @param string $fichier
 */
function yaml_decode_file($fichier){
	$yaml = '';
	$retour = false;
	
	lire_fichier($fichier, $yaml);
	// Si on recupere bien quelque chose
	if ($yaml){
		$retour = yaml_decode($yaml);
	}
	
	return $retour;
}

?>
