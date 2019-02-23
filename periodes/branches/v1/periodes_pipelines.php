<?php
/**
 * Utilisations de pipelines par Périodes
 *
 * @plugin     Périodes
 * @copyright  2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Periodes\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Ajoute des contenus dans la partie <head> des pages de l’espace privé.
 *
 * @param array $flux
 *   Les donnes de la pipeline
 *
 * @return array
 *   Les données de la pipeline.
 */
function periodes_header_prive($flux){
	$flux .= '<link rel="stylesheet" href="' . _DIR_PLUGIN_PERIODES  .'css/admin_periodes.css" type="text/css" media="all" />';
	return $flux;
}
