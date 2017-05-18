<?php
/**
 * Utilisations de pipelines par Info SPIP
 *
 * @plugin     Info SPIP
 * @copyright  2013-2017
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Info_SPIP\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function info_spip_affiche_gauche($flux) {
	if ($flux['args']['exec'] == 'accueil') {
		$flux['data'] = recuperer_fond('prive/squelettes/inclure/info_spip', array(),
				array('ajax' => false)) . $flux['data'];
	}

	return $flux;
}
