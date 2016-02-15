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

	$config = lire_config('facebook');

	if ($flux['args']['exec'] == 'article'
	    and (!empty($config['accessToken']))) {
		$flux['data'] .= recuperer_fond('prive/squelettes/gauche/facebook_affiche_gauche', $flux['args']);
	}

	return $flux;
}
