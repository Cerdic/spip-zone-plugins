<?php
/**
 * Utilisations de pipelines par tota11y
 *
 * @plugin     tota11y
 * @copyright  2015
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\tota11y\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Ajout des scripts de tota11y dans le head des pages publiques
 *
 *
 * @pipeline jquery_plugins
 */
function tota11y_jquery_plugins($tableau) {
		$tableau[] = 'javascript/tota11y.min.js';
	return $tableau;
}


?>