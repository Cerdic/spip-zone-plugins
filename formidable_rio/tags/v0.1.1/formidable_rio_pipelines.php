<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Ajoute le formulaire de regeneration d'un paiement 3ps dans les infos sur les réponses
 * @param array $flux
 * @return $flux
**/
function formidable_rio_affiche_gauche($flux) {
	if ($flux['args']['exec'] == 'formulaire') {
		$flux['data'] .= recuperer_fond('prive/squelettes/inclure/formidable_rio', array('id_formulaire' => $flux['args']['id_formulaire']));
	}
		return $flux;
}
