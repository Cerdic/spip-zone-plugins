<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_importer_savecfg_charger_dist() {
	$valeurs = array(
		'fichier' => '',
	);

	return $valeurs;
}

function formulaires_importer_savecfg_verifier_dist() {
	$erreurs = array();
	if (strtolower(substr(strrchr($_FILES['fichier']['name'], '.'), 1)) != 'cfg') {
		$erreurs['message_erreur'] == _T('savecfg:fichier_mauvaise_extension');
	}
	$file = unserialize(file_get_contents($_FILES['fichier']['tmp_name']));
	foreach ($file as $save => $value) {
		if ((!is_array($file[$save])) OR (count($file[$save]) < 4)) {
			$erreurs['message_erreur'] = _T('savecfg:fichier_mauvaise_syntaxe');
		}
	}

	return $erreurs;
}

function formulaires_importer_savecfg_traiter_dist() {
	$message = importer_savecfg('fichier');

	return $message;
}

function importer_savecfg($fichier) {
	$res = array();
	$titres = array();
	include_spip('inc/sauvegarder_savecfg');

	$file = unserialize(file_get_contents($_FILES['fichier']['tmp_name']));
	foreach ($file as $save => $value) {
		foreach ($file[$save] as $mat => $content) {
			if ($mat == 'id_savecfg') {
				$file[$save][$mat] = '';
			}
			$file[$save]['titre'] = $save;
		}
		$titres[] = sauvegarder_savecfg($file[$save]['fond'], $file[$save]['titre'], $file[$save]['valeur']);
	}
	$res['message_ok'] = _T('savecfg:import_ok') . ' <ul><li>' . implode('</li><li>',
			array_unique($titres)) . '</li></ul>';

	return $res;
}