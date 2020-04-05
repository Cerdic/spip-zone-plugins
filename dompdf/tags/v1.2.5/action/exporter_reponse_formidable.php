<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Exporte UNE réponse de formidable
function action_exporter_reponse_formidable_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	$exporter_pdf = charger_fonction('exporter_pdf', 'inc');
	$exporter_pdf('modeles/formulaires_reponse', array('id_formulaires_reponse' => $arg));
}
