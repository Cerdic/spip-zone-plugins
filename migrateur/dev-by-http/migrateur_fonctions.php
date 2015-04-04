<?php

/**
 * Fonctions du migrateur
 *
 * @package SPIP\Migrateur\Fonctions
**/

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Retourne la liste des étapes de migration
 *
 * @return Couples (code d'étape => Texte de l'étape)
**/
function migrateur_etapes() {
	include_spip('migrateur/config');
	return $GLOBALS['MIGRATEUR_ETAPES'] ? $GLOBALS['MIGRATEUR_ETAPES'] : array();
}


/**
 * Retourne le numéro de dernière étape réalisée
 *
 * Prend dans l'URL sinon dans le dernier fichier de log d'étape
 * @return int
**/
function migrateur_derniere_etape() {
	static $nb = null;
	if (!is_null($nb)) {
		return $nb;
	}
	if ($nb = _request('nb')) {
		return $nb;
	}
	$fichier = _DIR_TMP . "migrateur/etape.log";
	if (file_exists($fichier)) {
		$etape = file_get_contents($fichier, NULL, NULL, 17, 10);
		list($nb) = explode("\n", $etape);
		return $nb = trim($nb);
	}

	return $nb = 0;
}

/**
 * Vérifier la péremption d'une clé d'authentification
 *
 * Validité de 12h.
 * 
 * @param string $cle
 *     Clé au format '{date}@{hash}'
 * @return false|int
 *     false si la clé est périmée ou non renseignée,
 *     nombre d'heures de validité restant sinon
**/
function migrateur_verifier_peremption_auth_key($key) {
	include_spip('migrateur_options');
	return SPIP\Migrateur\Serveur::verifier_peremption_auth_key($key);
}
