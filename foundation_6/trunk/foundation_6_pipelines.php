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

/*
 *   Pipeline Insert_head
 */
function foundation_6_insert_head($flux) {

	// Si on est en mode app, on revoie le bon squelette
	if (_FOUNDATION_SASS) {
		$flux .= recuperer_fond('inclure/head-foundation-app');
	} else {
		$flux .= recuperer_fond('inclure/head-foundation');
	}

	return $flux;
}

/*
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