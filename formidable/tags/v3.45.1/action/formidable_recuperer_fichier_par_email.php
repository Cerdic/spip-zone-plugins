<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
include_spip('inc/autoriser');
include_spip('inc/formidable_fichiers');
/**
 * Récupère un fichier depuis un lien email, si on ne clique pas trop tard
 * et l'envoi en http
 **/
function action_formidable_recuperer_fichier_par_email() {
	//La vérification est inspirée de ce qui se fait dans notifications, pour les modifications de formu par email
	$arg = _request('arg');
	$hash = _request('hash');

	include_spip('inc/securiser_action');
	$action = 'formidable_recuperer_fichier_par_email';
	$pass = secret_du_site();
	spip_log("Action :  le arg est $arg et le hash $hash.", "formidable"._LOG_DEBUG);
	spip_log("Du coup le resultat de action_auteur est ". _action_auteur("$action-$arg", '', $pass, 'alea_ephemere'), "formidable"._LOG_DEBUG);
	if ($hash==_action_auteur("$action-$arg", '', $pass, 'alea_ephemere')
		or $hash==_action_auteur("$action-$arg", '', $pass, 'alea_ephemere_ancien')) {
		$arg = unserialize($arg);
		// Construire le chemin du fichier, en fonction de ce qu'on reçoit
		$chemin_fichier = formidable_generer_chemin_fichier($arg);
		// Vérifier que le fichier existe, qu'il n'est pas trop vieux, et l'envoyer le cas échéant
		if (@file_exists($chemin_fichier)) {
			$f = $arg['fichier'];
			$date = filemtime($chemin_fichier);
			if (_FORMIDABLE_EXPIRATION_FICHIERS_EMAIL > 0 and $date +  _FORMIDABLE_EXPIRATION_FICHIERS_EMAIL < time()) {// vérifier que le fichier n'est pas trop vieux
				include_spip('inc/minipres');
				echo minipres(_T('formidable:erreur_fichier_expire'));
			} else {
				formidable_retourner_fichier($chemin_fichier, $f);
			}
		} else {
			include_spip('inc/minipres');
			echo minipres(_T('formidable:erreur_fichier_introuvable'));
		}
	} else {
		include_spip('inc/minipres');
		echo minipres();
	}
	exit;
}
