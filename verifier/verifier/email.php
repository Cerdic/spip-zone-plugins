<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/*
 * Verifie la validite d'une adresse de courriel.
 */
function verifier_email_dist($valeur, $options=array()){
	include_spip('inc/filtres');
	
	// Disponibilite des courriels en base AUTEURS
	// Si l'adresse n'est pas disponible, on stoppe tout sinon on continue
	if ($options['disponible'] and !verifier_disponibilite_email($valeur)){
		return _T('verifier:erreur_email_nondispo', array('email' => echapper_tags($valeur)));
	}
	
	// Choix du mode de verification de la syntaxe des courriels
	if (!$options['mode'] or !in_array($options['mode'], array('strict'))){
		$mode = 'normal';
	}
	else{
		$mode = $options['mode'];
	}
		
	if ($mode == 'normal')
		$fonction_verif = 'email_valide';
	else
		$fonction_verif = 'verifier_email_de_maniere_stricte';
	
	if (!$fonction_verif($valeur))
		return _T('verifier:erreur_email', array('email' => echapper_tags($valeur)));
	else
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
	// Si c'est un spammeur autant arreter tout de suite
	if (preg_match(",[\n\r].*(MIME|multipart|Content-),i", $valeur)) {
		spip_log("Tentative d'injection de mail : $valeur");
		return false;
	}
	include_spip('inc/is_email');
	foreach (explode(',', $valeur) as $adresse) {
		if (!is_email(trim($adresse)))
			return false;
	}
	return true;
}

/**
 * Vérifier que le courriel utilisé n'est pas
 * déjà présent en base SPIP_AUTEURS
 */
function verifier_disponibilite_email($valeur){
	include_spip('base/abstract_sql');

	if(sql_getfetsel('id_auteur', 'spip_auteurs', 'email='.sql_quote($valeur)))
		return false;
	else
		return true;
}
