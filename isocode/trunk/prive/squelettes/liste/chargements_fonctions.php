<?php
/**
 * Ce fichier contient l'ensemble des fonctions implémentant l'API du plugin.
 *
 * @package SPIP\ISOCODE\API
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Informe sur la liste des tables déjà chagées en base de données.
 * Les informations de la meta de chaque table sont complétées et renvoyées.
 *
 * @api
 * @filtre
 *
 * @return array
 *      Liste des tables de codes ISO sans le préfixe `spip_` et leurs informations de chargement.
 */
function isocode_informer_consignation($type) {

	// On initialise la liste des tables en lisant la meta idoine.
	include_spip('inc/config');
	$chargements = lire_config("isocode/${type}", array());

	// On complète chaque bloc d'informations par le nom de la table et son libéllé.
	if ($chargements) {
		foreach (array_keys($chargements) as $_cle) {
			$chargements[$_cle]['libelle'] = _T("isocode:${type}_${_cle}");
		}
	}

	return $chargements;
}
