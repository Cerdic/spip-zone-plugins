<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/*
 * Verifie la validite d'une adresse de courriel.
 */
function verifier_email_dist($valeur, $options=array()){
	include_spip('inc/filtres');
	
	// Disponibilite des courriels en base AUTEURS
	if ($options['disponible']){
		return verifier_disponibilite_email($valeur);
	}
	
	// Choix du de verification de la syntaxe des courriels
	if (!$options['mode'] or !in_array($options['mode'], array('strict'))){
		$mode = 'normal';
	}
	else{
		$mode = $options['mode'];
	}
		
	if ($mode == 'normal'){	
		if (email_valide($valeur))
			return '';
		else
			return _T('verifier:erreur_email');
	}
	// Validation Stricte
	else{
		return verifier_email_de_maniere_stricte($valeur);
	}
	return '';
}

/**
 * Changement de la RegExp d'origine
 * Non respect des RFC beaucoup trop souples à mon sens
 * On INTERDIT les mails dont les domaines ne sont pas "valides"
 * On INTERDIT les adresses qui comportent autre chose que des tirets / tirets bas / point
 * (même si les autres caractères sont autorisés, tant pis, ils sont trop rares)
 */
function verifier_email_de_maniere_stricte($valeur){
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

/**
 * Vérifier que le courriel utilisé n'est pas
 * déjà présent en base SPIP_AUTEURS
 */
function verifier_disponibilite_email($valeur){
	include_spip('base/abstract_sql');
	$erreur = _T('verifier:erreur_email_nondispo');
	$ok = '';

	$emailDejaUtilise = sql_getfetsel("id_auteur", "spip_auteurs", "email='".$valeur."'");
	if($emailDejaUtilise) return $erreur;

	return $ok;
}