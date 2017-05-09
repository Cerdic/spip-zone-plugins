<?php
/**
 * Fichier de pipelines pour le plugin Socicon
 *
 * @plugin     Socicon
 * @copyright  2017
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP/Socicon/Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function socicon_sociaux_lister($flux){
	include_spip('inc/config');
	$socicon_config = lire_config('socicon');

	if (empty($socicon_config) or count($socicon_config) == 0) {
		$socicon_config = array('facebook','twitter','instagram','googleplus','blogger','pinterest','linkedin','youtube','rss','mail','tripadvisor','vimeo','flickr');
	}
	$sociaux_pipe = array_keys($flux['data']);
	$diff = array_diff($sociaux_pipe, $socicon_config);
	if (is_array($diff) and count($diff)) {
		foreach ($diff as $value) {
			unset($flux['data'][$value]);
		}
	}
	foreach ($socicon_config as $value) {
		$flux['data'][$value] = ucwords($value);
	}

	return $flux;
}
