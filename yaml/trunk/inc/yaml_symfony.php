<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

require_once _DIR_PLUGIN_YAML . 'vendor/autoload.php';
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

function yaml_symfony_encode($array, $flags) {

	$dump = Yaml::dump($array, 2, 2);
	return $dump;
}

function yaml_symfony_decode($input, $show_error = true) {

	$parsed = false;
	
	try {
		$parsed = Yaml::parse($input, Yaml::PARSE_CUSTOM_TAGS);
	} catch (ParseException $exception) {
		if ($show_error) {
			printf('Unable to parse the YAML string: %s', $exception->getMessage());
		}
	}

	return $parsed;
}

function yaml_symfony_decode_file($file, $show_error = true) {

	$parsed = false;

	try {
		$parsed = Yaml::parseFile($file);
	} catch (ParseException $exception) {
		if ($show_error) {
			printf('Unable to parse the YAML file: %s', $exception->getMessage());
		}
	}

	return $parsed;
}
