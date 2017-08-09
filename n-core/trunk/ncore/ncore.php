<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined('_NCORE_CONFIG_AJAX_DEFAUT')) {
	/**
	 * Valeur par défaut de la configuration AJAX des noisettes.
	 * Pour N-Core, le défaut est `true`.
	 *
	 */
	define('_NCORE_CONFIG_AJAX_DEFAUT', true);
}
if (!defined('_NCORE_DYNAMIQUE_DEFAUT')) {
	/**
	 * Valeur par défaut de l'indicateur d'inclusion dynamique des noisettes.
	 * Pour N-Core, le défaut est `false`
	 */
	define('_NCORE_DYNAMIQUE_DEFAUT', false);
}


/**
 * @param $service
 *
 * @return array
 */
function ncore_noisette_lister_signatures($service) {

	// On recherche au préalable si il existe une fonction propre au service et si oui on l'appelle.
	include_spip("ncore/${service}");
	$lister_md5 = "${service}_noisette_lister_signatures";
	if (function_exists($lister_md5)) {
		$signatures = $lister_md5();
	} else {
		// Le service ne propose pas de fonction propre, on utilise celle de N-Core.
		// -- Les signatures md5 sont sockées dans un fichier cache séparé de celui des descriptions de noisettes.
		include_spip('inc/ncore_cache');
		$signatures = cache_lire($service, _NCORE_NOMCACHE_NOISETTE_SIGNATURE);
	}

	return $signatures;
}

/**
 * @param $service
 * @param $noisettes
 * @param $recharger
 *
 * @return bool
 */
function ncore_noisette_stocker($service, $noisettes, $recharger) {

	$retour = true;

	// On recherche au préalable si il existe une fonction propre au service et si oui on l'appelle.
	include_spip("ncore/${service}");
	$stocker = "${service}_noisette_stocker";
	if (function_exists($stocker)) {
		$retour = $stocker($noisettes, $recharger);
	} else {
		// Le service ne propose pas de fonction propre, on utilise celle de N-Core.
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
			$signatures = cache_lire($service,_NCORE_NOMCACHE_NOISETTE_SIGNATURE);

			// On supprime les noisettes obsolètes
			if (!empty($noisettes['obsoletes'])) {
				$descriptions_obsoletes = array_column($noisettes['obsoletes'], null, 'noisette');
				$descriptions = array_diff($descriptions, $descriptions_obsoletes);

				$signatures_obsoletes = array_column($noisettes['obsoletes'], 'signature', 'noisette');
				$signatures = array_diff($signatures, $signatures_obsoletes);
			}

			// On remplace les noisettes modifiées et on ajoute les noisettes nouvelles. Cette opération peut-être
			// réalisée en une action avec la fonction array_merge.
			if (!empty($noisettes['modifiees']) or !empty($noisettes['nouvelles'])) {
				$descriptions_modifiees = array_column($noisettes['modifiees'], null, 'noisette');
				$descriptions_nouvelles = array_column($noisettes['nouvelles'], null, 'noisette');
				$descriptions = array_merge($descriptions, $descriptions_modifiees, $descriptions_nouvelles);

				$signatures_modifiees = array_column($noisettes['modifiees'], 'signature', 'noisette');
				$signatures_nouvelles = array_column($noisettes['nouvelles'], 'signature', 'noisette');
				$signatures = array_merge($signatures, $signatures_modifiees, $signatures_nouvelles);
			}

			// On recrée les caches.
			cache_ecrire($service, _NCORE_NOMCACHE_NOISETTE_DESCRIPTION, $descriptions);
			cache_ecrire($service, _NCORE_NOMCACHE_NOISETTE_SIGNATURE, $signatures);
		}
	}

	return $retour;
}

/**
 * @param $service
 * @param $noisette
 *
 * @return array
 */
function ncore_noisette_decrire($service, $noisette) {

	$description = array();

	// On recherche au préalable si il existe une fonction propre au service et si oui on l'appelle.
	include_spip("ncore/${service}");
	$decrire = "${service}_noisette_decrire";
	if (function_exists($decrire)) {
		$description = $decrire($noisette);
	} else {
		// Le service ne propose pas de fonction propre, on utilise celle de N-Core.
		// Chargement de toute la configuration de la noisette en base de données.
		// Les données sont renvoyées brutes sans traitement sur les textes ni les tableaux sérialisés.
		include_spip('inc/ncore_cache');
		$descriptions = cache_lire($service, _NCORE_NOMCACHE_NOISETTE_DESCRIPTION);
		if (isset($descriptions[$noisette])) {
			$description = $descriptions[$noisette];
		}
	}

	return $description;
}

/**
 * @param $service
 *
 * @return bool
 */
function ncore_noisette_config_ajax($service) {

	// On recherche au préalable si il existe une fonction propre au service et si oui on l'appelle.
	include_spip("ncore/${service}");
	$config_ajax = "${service}_noisette_config_ajax";
	if (function_exists($config_ajax)) {
		$defaut_ajax = $config_ajax();
	} else {
		// Le service ne propose pas de fonction propre, on utilise celle de N-Core.
		$defaut_ajax = _NCORE_CONFIG_AJAX_DEFAUT;
	}

	return $defaut_ajax;
}

/**
 * @param $service
 * @param $information
 *
 * @return array
 */
function ncore_noisette_lister($service, $information) {

	// Initialisation du tableau de sortie
	$information_noisettes = array();

	// On recherche au préalable si il existe une fonction propre au service et si oui on l'appelle.
	include_spip("ncore/${service}");
	$lister = "${service}_noisette_lister";
	if (function_exists($lister)) {
		$information_noisettes = $lister($information);
	} else {
		// Le service ne propose pas de fonction propre, on utilise celle de N-Core.
		if ($information) {
			include_spip('inc/ncore_cache');
			if ($descriptions = cache_lire($service, _NCORE_NOMCACHE_NOISETTE_DESCRIPTION)) {
				$information_noisettes = array_column($descriptions, $information, 'noisette');
			}
		}
	}

	return $information_noisettes;
}
