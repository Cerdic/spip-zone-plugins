<?php

# wrapper de la class sfYAML pour SPIP
#
# fournit deux fonctions pour YAML,
# analogues a json_encode() et json_decode
#
# Regle de dev: ne pas se rendre dependant de la lib sous-jacente

// temporaire le temps de tester spyc
define('_LIB_YAML','sfyaml'); 
#define('_LIB_YAML','spyc'); 

/*
 * Encode n'importe quelle structure en yaml
 * @param $struct
 * @return string
 */
function yaml_encode($struct, $opt = array()) {
	// test temporaire
	if (_LIB_YAML == 'spyc') {
		require_once _DIR_PLUGIN_YAML.'spyc/spyc.php';
		return Spyc::YAMLDump($struct);
	}
	require_once _DIR_PLUGIN_YAML.'sfyaml/sfYaml.php';
	require_once _DIR_PLUGIN_YAML.'sfyaml/sfYamlDumper.php';
	$opt = array_merge(
		array(
			'inline' => 2
		), $opt);
	$yaml = new sfYamlDumper();
	return $yaml->dump($struct, $opt['inline']);
}

/*
 * Decode un texte yaml, renvoie la structure
 * @param string $input
 */
function yaml_decode($input) {
	// test temporaire
	if (_LIB_YAML == 'spyc') {
		require_once _DIR_PLUGIN_YAML.'spyc/spyc.php';
		return Spyc::YAMLLoad($input);
	}
	require_once _DIR_PLUGIN_YAML.'sfyaml/sfYaml.php';
	require_once _DIR_PLUGIN_YAML.'sfyaml/sfYamlParser.php';

	$yaml = new sfYamlParser();

	try
	{
	  $ret = $yaml->parse($input);
	}
	catch (Exception $e)
	{
		throw new InvalidArgumentException(sprintf('Unable to parse string: %s', $e->getMessage()));
	}

	return $ret;
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
