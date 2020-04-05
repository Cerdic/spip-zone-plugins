<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Action qui va exporter TOUTES les réponses d'un formulaire SPIP
 */
function action_exporter_reponses_formidable_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	$exporter_pdf = charger_fonction('exporter_pdf', 'inc');
	$exporter_pdf('modeles/reponses_formulaires', array('id_formulaire' => $arg));
}
