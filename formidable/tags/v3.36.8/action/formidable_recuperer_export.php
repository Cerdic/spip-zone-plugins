<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/autoriser');
include_spip('inc/formidable');
include_spip('inc/formidable_fichiers');

/**
 * Récupère, si on est autorisé à voir la réponse du formulaire,
 * un export csv ou xls (ou zip) qui aurait été préablablement réalisé.
 * et l'envoi en http
 *
 * On s’assure que la personne est identifiée, et à l’autorisation de voir les réponses.
 * Par ailleurs, on s’assure que le hash est valable uniquement pour un contenu de fichier donné.
 *
 **/
function action_formidable_recuperer_export() {

	// {id_formulaire}:{filename}
	$args = _request('args');
	$cle = _request('cle');

	include_spip('inc/securiser_action');
	include_spip('inc/minipres');

	if (!verifier_cle_action($args, $cle)) {
		echo minipres();
		exit;
	}

	list($id_formulaire, $md5, $filename) = array_pad(explode(':', $args, 3), 3, null);
	if (
		!$id_formulaire
		or !$md5
		or !$filename
		or false !== stripos($filename, '/')
		or false !== stripos($filename, '\\')
		or empty($GLOBALS['visiteur_session']['id_auteur'])
		or !autoriser('voir', 'formulairesreponse', $id_formulaire)
	) {
		echo minipres();
		exit;
	}

	$chemin_fichier = _DIR_CACHE . 'export/' . $filename;
	if (!file_exists($chemin_fichier)) {
		echo minipres(_T('formidable:erreur_fichier_introuvable'));
		exit;
	}

	if (md5_file($chemin_fichier) !== $md5) {
		echo minipres(_L('URL d’export obsolète'));
		exit;
	}

	formidable_retourner_fichier($chemin_fichier, $filename);
	exit;
}
