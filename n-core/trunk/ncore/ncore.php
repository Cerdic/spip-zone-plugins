<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function ncore_noisette_lister_signatures($service) {

	// Récupération des signatures md5 des noisettes déjà enregistrées.
	// -- Les signatures md5 sont sockées dans un fichier cache séparé de celui des descriptions de noisettes.
	include_spip('inc/ncore_cache');
	$signatures = cache_lire($service, _NCORE_NOMCACHE_NOISETTE_SIGNATURE);

	return $signatures;
}

function ncore_noisette_stocker($service, $noisettes, $recharger) {

	$retour = true;

	// Les descriptions de noisettes et les signatures sont stockés dans deux caches distincts.
	// -- Les descriptions : on conserve la signature pour chaque description, le tableau est réindexé avec l'identifiant
	//    de la noisette.
	// -- Les signatures : on isole la liste des signatures et on indexe le tableau avec l'identifiant de la noisette.
	include_spip('inc/ncore_cache');
	if ($recharger) {
		// Si le rechargement est forcé, toutes les noisettes sont nouvelles, on peut donc écraser les caches
		// existants sans s'en préoccuper.
		$descriptions = array_column($noisettes['nouvelles'], null, 'noisette');
		cache_ecrire($service, _NCORE_NOMCACHE_NOISETTE_DESCRIPTION, $descriptions);

		$signatures = array_column($noisettes['nouvelles'], 'signature', 'noisette');
		cache_ecrire($service, _NCORE_NOMCACHE_NOISETTE_SIGNATURE, $signatures);
	} else {
		// On lit les cache existants et on applique les modifications.
		$descriptions = cache_lire($service, _NCORE_NOMCACHE_NOISETTE_DESCRIPTION);
		$signatures = cache_lire($service, _NCORE_NOMCACHE_NOISETTE_SIGNATURE);

		// On supprime les noisettes obsolètes
		if ($noisettes['obsoletes']) {
			$descriptions_obsoletes = array_column($noisettes['obsoletes'], null, 'noisette');
			$descriptions = array_diff($descriptions, $descriptions_obsoletes);

			$signatures_obsoletes = array_column($noisettes['obsoletes'], 'signature', 'noisette');
			$signatures = array_diff($signatures, $signatures_obsoletes);
		}

		// On remplace les noisettes modifiées et on ajoute les noisettes nouvelles. Cette opération peut-être
		// réalisée en une action avec la fonction array_merge.
		if ($noisettes['modifiees'] or $noisettes['nouvelles']) {
			$descriptions_modifiees = array_column($noisettes['modifiees'], null, 'noisette');
			$descriptions_nouvelles = array_column($noisettes['nouvelles'], null, 'noisette');
			$descriptions = array_merge($descriptions, $descriptions_modifiees, $descriptions_nouvelles);

			$signatures_modifiees = array_column($noisettes['modifiees'], 'signature', 'noisette');
			$signatures_nouvelles = array_column($noisettes['nouvelles'], 'signature', 'noisette');
			$signatures = array_diff($signatures, $signatures_modifiees, $signatures_nouvelles);
		}

		// On recrée les caches.
		cache_ecrire($service, _NCORE_NOMCACHE_NOISETTE_DESCRIPTION, $descriptions);
		cache_ecrire($service, _NCORE_NOMCACHE_NOISETTE_SIGNATURE, $signatures);
	}


	return $retour;
}

function ncore_noisette_decrire($service, $noisette) {

	$description = array();

	// Chargement de toute la configuration de la noisette en base de données.
	// Les données sont renvoyées brutes sans traitement sur les textes ni les tableaux sérialisés.
	include_spip('inc/ncore_cache');
	$descriptions = cache_lire($service, _NCORE_NOMCACHE_NOISETTE_DESCRIPTION);
	if (isset($descriptions[$noisette])) {
		$description = $descriptions[$noisette];
	}

	return $description;
}

function ncore_noisette_config_ajax($service) {

	// On détermine la valeur par défaut de l'ajax des noisettes qui est stocké dans la configuration du plugin.

	return false;
}

function ncore_noisette_lister($service, $information) {

	// Initialisation du tableau de sortie
	$info_noisettes = array();

	if ($information) {
		include_spip('inc/ncore_cache');
		if ($descriptions = cache_lire($service, _NCORE_NOMCACHE_NOISETTE_DESCRIPTION)) {
			$info_noisettes = array_column($descriptions, $information, 'noisette');
		}
	}

	return $info_noisettes;
}
