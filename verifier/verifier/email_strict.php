<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Changement de la RegExp d'origine
 * Non respect des RFC beaucoup trop souples à mon sens
 * On INTERDIT les mails dont les domaines ne sont pas "valides"
 * On INTERDIT les adresses qui comportent autre chose que des tirets / tirets bas / point
 * (même si les autres caractères sont autorisés, tant pis, ils sont trop rares)
 */
function verifier_email_strict_dist($valeur, $options=array()){
	$erreur = _T('verifier:erreur_email');
	$ok = '';

	// Si c'est un spammeur autant arreter tout de suite
	if (preg_match(",[\n\r].*(MIME|multipart|Content-),i", $valeur)) {
		spip_log("Tentative d'injection de mail : $valeur");
		return $erreur;
	}
	foreach (explode(',', $valeur) as $v) {
		// nettoyer certains formats
		// "Marie Toto <Marie@toto.com>"
		$adresse = trim(preg_replace(",^[^<>\"]*<([^<>\"]+)>$,i", "\\1", $v));
		// NOUVELLE REGEXP NE RESPECTANT PLUS RFC 822 MAIS MOINS TOLERANTE
		if (!preg_match('/^([A-Za-z0-9]){1}([A-Za-z0-9]|-|_|\.)*@[A-Za-z0-9]([A-Za-z0-9]|-|\.){1,}\.[A-Za-z]{2,4}$/', $adresse))
			return $erreur;
	}
	return $ok;
}
