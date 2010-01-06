<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Vérifier que le courriel utilisé n'est pas
 * déjà présent en base SPIP_AUTEURS
 */
function verifier_email_disponible_dist($valeur, $options=array()){
	include_spip('base/abstract_sql');
	$erreur = _T('verifier:erreur_email_nondispo');
	$ok = '';

	$emailDejaUtilise = sql_getfetsel("id_auteur", "spip_auteurs", "email='".$valeur."'");
	if($emailDejaUtilise) return $erreur;
	
	return $ok;
}
