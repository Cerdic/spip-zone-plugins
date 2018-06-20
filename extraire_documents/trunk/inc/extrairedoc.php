<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Verifier que la memoire disponible est suffisante
 * @param int $necessaire
 *   en octets
 * @return bool
 */
function extrairedoc_verifier_memoire_disponible($necessaire) {

	//Ne pas traiter si la mémoire est insuffisante
	//On doit avoir au moins 3 fois la taille du fichier de disponible avant traitement (choix empirique)
	//http://stackoverflow.com/questions/10208698/checking-memory-limit-in-php
	$memory_used = memory_get_usage();
	$memory_limit = ini_get('memory_limit');
	// S'il n'y a PAS de limite de mémoire tout va bien
	if ($memory_limit == -1) {
		return true;
	}
	if (preg_match('/^(\d+)(.)$/', $memory_limit, $matches)) {
		if ($matches[2] == 'M') {
			$memory_limit = (int)$matches[1] * 1024 * 1024; // nnnM -> nnn MB
		}
		else if ($matches[2] == 'K') {
			$memory_limit = (int)$matches[1] * 1024; // nnnK -> nnn KB
		}
	}
	$memory_available = $memory_limit - $memory_used;

	if ($memory_available < $necessaire) {
		return false;
	}
	return true;

}
