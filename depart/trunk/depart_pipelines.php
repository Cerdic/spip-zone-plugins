<?php
/**
 * Utilisations de pipelines par Déplacer des articles par lot
 *
 * @plugin     Déplacer des articles par lot
 * @copyright  2018
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Depart\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function depart_affiche_droite($flux) {
	$contexte = array('exec' => $flux['args']['exec'], 'options' => $flux['args']);

	if (isset($flux['args']['id_rubrique']) and isset($flux['args']['exec']) and $flux['args']['exec'] == 'rubrique') {
		$flux['data'] .= recuperer_fond('prive/squelettes/inclure/depart_rub', $contexte['options']);
	}

	return $flux;
}