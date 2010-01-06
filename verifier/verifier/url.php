<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Une URL commence par HTTP:// contient un domaine, etc.
 * Doit pouvoir recevoir en option le protocole (ou pas) FTP SSH SFTP HTTP etc.
 * Si pas de protocole spécifié, commencer à :// ??
 */
function verifier_url_dist($valeur, $options=array()){
	$erreur = _T('verifier:erreur_url');
	$ok = '';

	/*
		TODO Faire une belle RegExp
	*/

	return $ok;
}
