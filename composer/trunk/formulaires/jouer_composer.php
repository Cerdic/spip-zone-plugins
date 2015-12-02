<?php

function formulaires_jouer_composer_charger_dist() {
	if (!autoriser('executer', 'composer')) {
		return null;
	}

	$valeurs = array();
	$valeurs['_composer_json'] = file_get_contents(_FILE_COMPOSER_JSON);
	$valeurs['_composer_phar'] = file_exists(_DIR_COMPOSER . 'composer.phar');
	$valeurs['message_output'] = "";

	return $valeurs;
}

function formulaires_jouer_composer_verifier_dist() {
	$erreurs = array();
	return $erreurs;
}

function formulaires_jouer_composer_traiter_dist() {
	$res = array(
		'editable' => true
	);

	$output = array();
	$err = "";

	if (_request('obtenir')) {
		list($output, $err) = composer_composer_obtenir();
	}
	elseif (_request('self_update')) {
		list($output, $err) = composer_composer_self_update();
	}
	elseif (_request('update')) {
		list($output, $err) = composer_composer_update();
	}


	if ($err) {
		$res['message_erreur'] = "Erreur survenue : $err";
	} else {
		$res['message_ok'] = "Opération réussie";
	}

	if (count($output)) {
		$res['message_output'] = implode("\n", $output);
		set_request('message_output', $res['message_output']);
	}

	return $res;
}

/**
 * Télécharge composer
 *
 * @return array Liste (sorties, erreur)
**/
function composer_composer_obtenir() {
	sous_repertoire(_DIR_COMPOSER);
	$root = realpath(_DIR_COMPOSER);
	chdir($root);
	$cmd = "curl -sS https://getcomposer.org/installer | php 2>&1";
	exec($cmd, $output, $err);
	if (!$err) {
		exec("chmod 775 composer.phar", $output, $err);
	}
	chdir(_ROOT_CWD);
	return array($output, $err);
}

/**
 * Met à jour composer.phar
 *
 * @return array Liste (sorties, erreur)
**/
function composer_composer_self_update() {
	return composer_composer_run('self-update');
}

/**
 * Met à jour les libs composer
 *
 * @return array Liste (sorties, erreur)
**/
function composer_composer_update() {
	return composer_composer_run('update');
}

/**
 * Exécute la commande indiquée par composer.
 *
 * @return array Liste (sorties, erreur)
**/
function composer_composer_run($commande) {
	$root = realpath(_DIR_COMPOSER);
	chdir($root);
	$home = "export COMPOSER_HOME=" . _ROOT_RACINE . "tmp";
	$cmd = "$home && php composer.phar $commande 2>&1";
	exec($cmd, $output, $err);
	chdir(_ROOT_CWD);
	return array($output, $err);
}
