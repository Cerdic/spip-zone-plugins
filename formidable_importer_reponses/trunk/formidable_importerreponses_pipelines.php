<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Ajouter notre formulaire en colonne droite sur la page des réponses
 *
 * @pipeline recuperer_fond()
 * @param array $flux
 * @return array
 */
function formidable_importer_reponses_recuperer_fond($flux) {
	if ($flux['args']['fond'] == 'prive/squelettes/extra/formulaires_reponses') {
		$flux['data']['texte'] .= recuperer_fond('prive/squelettes/extra/formulaires_reponses_importer', $flux['args']['contexte']);
	}
	return $flux;
}
