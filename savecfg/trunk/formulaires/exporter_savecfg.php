<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_exporter_savecfg_charger_dist() {
	$valeurs = array(
		'fichier' => '',
	);

	return $valeurs;
}

function formulaires_exporter_savecfg_verifier_dist() {
	$erreurs = array();

	return $erreurs;
}

function formulaires_exporter_savecfg_traiter_dist() {
	$message = exporter_savecfg();

	return $message;
}

function exporter_savecfg() {
	$fichier = '';
	$save = array();
	foreach (_request('export') as $key => $value) {
		if ($value == 'on') {
			$sfg = sql_fetsel(array('fond', 'valeur', 'titre', 'date'), 'spip_savecfg',
				'id_savecfg=' . sql_quote($key));
			$save[$sfg['titre']] = array(
				'id_savecfg' => $key,
				'fond' => $sfg['fond'],
				'valeur' => $sfg['valeur'],
				'date' => $sfg['date']
			);
		}
	}
	$save = serialize($save);
	header("Content-type: application/cfg");
	header("Content-disposition: attachment; filename=SaveCFG_" . date("Ymd") . ".cfg");
	echo($save);
	exit;

	return true;
}