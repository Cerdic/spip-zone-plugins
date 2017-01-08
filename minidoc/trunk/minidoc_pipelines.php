<?php
/**
 * Utilisations de pipelines par Documents en vue réduite
 *
 * @plugin     Documents en vue réduite
 * @copyright  2017
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Minidoc\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


function minidoc_afficher_complement_objet($flux) {
	if ($type = $flux['args']['type']
		and $id = intval($flux['args']['id'])
		and (autoriser('joindredocument', $type, $id))
	) {
		$flux['data'] .=  "\n" . '<script src="' . find_in_path('javascript/minidoc.js'). '"></script>' . "\n";
	}
	return $flux;
}

