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

/**
 * Afficher automatiquement la boite de publication d'article sur Facebook
 *
 * @param mixed $flux
 * @access public
 * @return mixed
 */
function facebook_affiche_gauche($flux) {

	$config = lire_config('facebook');
	$token = sql_getfetsel('token','spip_connecteur',array('type=0','id_auteur=0'));

	if ($flux['args']['exec'] == 'article' and $token) {
		$flux['data'] .= recuperer_fond('prive/squelettes/gauche/facebook_affiche_gauche', $flux['args']);
	}

	return $flux;
}
