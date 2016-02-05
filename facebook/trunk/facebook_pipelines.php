<?php
/**
 * Utilisations de pipelines par Facebook
 *
 * @plugin     Facebook
 * @copyright  2016
 * @author     vertige
 * @licence    GNU/GPL
 * @package    SPIP\Facebook\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function facebook_affiche_gauche($flux) {

	if ($flux['args']['exec'] == 'article') {
		$flux['data'] .= recuperer_fond('prive/squelettes/gauche/facebook_affiche_gauche', $flux['args']);
	}

	return $flux;
}
