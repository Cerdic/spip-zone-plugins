<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


#
# action generique
#
function action_webRadio_radio_action() {

	global $action, $arg, $hash, $id_auteur;
	include_spip('inc/securiser_action');
	if (!verifier_action_auteur("$action-$arg", $hash, $id_auteur)) {
		include_spip('inc/minipres');
		minipres(_T('info_acces_interdit'));
	}

	preg_match('/^(\w+)\W(.*)$/', $arg, $r);
	$var_nom = 'action_webRadio_radio_action_' . $r[1];
	if (function_exists($var_nom)) {
		spip_log("$var_nom $r[2]");
		$var_nom($r[2]);
	}
	else {
		spip_log("action $action: $arg incompris");
	}
}

// met a jour le titre et le descriptif
function action_webRadio_radio_action_changerContenu($arg) {
	global $redirect, $titre, $descriptif;

	sql_updateq(
		array('spip_documents'),
		array(
			'titre' => $titre,
			'descriptif' => $descriptif
		),
		array('id_document = '.sql_quote($arg))
	);

	redirige_par_entete(rawurldecode($redirect));
}

// ajoute un document à la playlist
function action_webRadio_radio_action_ajouter($arg) {
	global $redirect;

	sql_updateq(
		array('spip_documents'),
		array('playlist' => 'oui'),
		array('id_document = '.sql_quote($arg))
	);

	redirige_par_entete(rawurldecode($redirect));
}

// retire un document de la playlist
function action_webRadio_radio_action_retirer($arg) {
	global $redirect;

	sql_updateq(
		array('spip_documents'),
		array('playlist' => 'non'),
		array('id_document = '.sql_quote($arg))
	);

	redirige_par_entete(rawurldecode($redirect));
}

?>