<?php
/**
 * Utilisations de pipelines par foundation-4-spip
 *
 * @plugin     foundation_6
 * @copyright  2013
 * @author     Phenix
 * @licence    GNU/GPL
 * @package    SPIP\Foundation\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 *   Pipeline Insert_head_css
 */
function foundation_6_insert_head_css($flux) {

	// Si on est en mode app, on revoie le bon squelette
	if (_FOUNDATION_SASS) {
		$flux .= recuperer_fond('inclure/css/head-foundation-app');
	} else {
		$flux .= recuperer_fond('inclure/css/head-foundation');
	}

	return $flux;
}

/**
 * Pipeline affichage_final
 * On insert foundation a la fin du body des pages du site
 *
 * @param string $flux html de la page
 * @access public
 * @return string
 */
function foundation_6_affichage_final($flux) {
	include_spip('inc/config');
	if (lire_config('foundation_6/javascript')) {
		if (_FOUNDATION_SASS) {
			$js = recuperer_fond('inclure/js-foundation-app');
		} else {
			$js = recuperer_fond('inclure/js-foundation');
		}
		$pos_body = strpos($flux, '</body>');

		if ($pos_body) {
			return substr_replace($flux, $js, $pos_body, 0);
		}
	}

	return $flux;
}
