<?php
/**
 * Ce fichier contient les fonctions du service N-Core pour les noisettes.
 *
 * Chaque fonction, soit aiguille vers une fonction "homonyme" propre au service si elle existe,
 * soit déroule sa propre implémentation pour le service appelant.
 * Ainsi, les services externes peuvent, si elle leur convient, utiliser l'implémentation proposée par N-Core
 * sans coder la moindre fonction.
 *
 * @package SPIP\NCORE\NOISETTE\SERVICE
 */
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
	 * Pour N-Core, le défaut est `false`.
	 */
	define('_NCORE_DYNAMIQUE_DEFAUT', false);
}


/**
 * Retourne la liste des signatures des fichiers YAML des noisettes détectées par le service.
 *
 * Le service N-Core lit les signatures dans un cache dédié.
 *
 * @uses cache_lire()
 *
 * @param string	$service
 *      Le service permet de distinguer l'appelant qui peut-être un plugin comme le noiZetier ou
 *      un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * 		Cet argument n'est utilisé que si la fonction N-Core est appelée.
 *
 * @return array
 * 		Tableau des signatures de noisettes au format `[noisette] = signature`.
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
 * Stocke les descriptions de noisettes en distinguant les noisettes obsolètes, les noisettes modifiées et
 * les noisettes nouvelles.
 * Chaque description de noisette est un tableau associatif dont tous les index possibles - y compris la signature -
 * sont initialisés quelque soit le contenu du fichier YAML.
 *
 * Le service N-Core stocke les descriptions dans un cache dédié et les signatures dans un autre.
 *
 * @uses cache_lire()
 * @uses cache_ecrire()
 *
 * @param string	$service
 *      Le service permet de distinguer l'appelant qui peut-être un plugin comme le noiZetier ou
 *      un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * 		Cet argument n'est utilisé que si la fonction N-Core est appelée.
 * @param array		$noisettes
 * 		Tableau associatif à 3 entrées fournissant les descriptions des noisettes nouvelles, obsolètes et modifiées:
 * 		- 'obsoletes' : liste des identifiants de noisette devenus obsolètes
 * 		- 'modifiees' : liste des descriptions des noisettes dont le fichier YAML a été modifié
 *      - 'nouvelles' : liste des descriptions de nouvelles noisettes.
 *
 * 		Si $recharger est à `true`, seul l'index `nouvelles` est fourni dans le tableau $noisettes.
 * @param bool		$recharger
 *      Indique si le chargement en cours est forcé ou pas. Cela permet à la fonction N-Core ou au service
 *      concerné d'optimiser le traitement sachant que seules les noisettes nouvelles sont fournies.
 *
 * @return bool
 * 		`true` si le traitement s'est bien déroulé, `false` sinon.
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
 * Renvoie la description brute d'une noisette sans traitement typo ni désérialisation des champs.
 *
 * Le service N-Core lit la description de la noisette concernée dans le cache des descriptions.
 *
 * @uses cache_lire()
 *
 * @param string	$service
 *      Le service permet de distinguer l'appelant qui peut-être un plugin comme le noiZetier ou
 *      un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * 		Cet argument n'est utilisé que si la fonction N-Core est appelée.
 * @param string	$noisette
 * 		Identifiant de la noisette.
 *
 * @return array
 * 		Tableau de la description de la noisette. Les champs textuels et les champs de type tableau sérialisé
 * 		sont retournés en l'état.
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
 * Renvoie la configuration par défaut de l'ajax à appliquer pour les noisettes.
 * Cette information est utilisée si la description YAML d'une noisette ne contient pas de tag ajax
 * ou contient un tag ajax à `defaut`.
 *
 * Le service N-Core considère que toute noisette est par défaut insérée en ajax.
 *
 * @param string	$service
 *      Le service permet de distinguer l'appelant qui peut-être un plugin comme le noiZetier ou
 *      un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * 		Cet argument n'est utilisé que si la fonction N-Core est appelée.
 *
 * @return bool
 * 		`true` si par défaut une noisette est insérée en ajax, `false` sinon.
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
 * Renvoie l'information brute demandée pour l'ensemble des noisettes utilisées par le service.
 *
 * @uses cache_lire()
 *
 * @param string	$service
 *      Le service permet de distinguer l'appelant qui peut-être un plugin comme le noiZetier ou
 *      un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * 		Cet argument n'est utilisé que si la fonction N-Core est appelée.
 * @param string	$information
 *      Identifiant d'un champ de la description d'une noisette. Si l'argument est vide ou invalide,
 * 		la fonction renvoie un tableau vide.
 *
 * @return array
 * 		Tableau de la forme `[noisette] = information`.
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
				// Si $information n'est pas une colonne valide la fonction retournera un tableau vide.
				$information_noisettes = array_column($descriptions, $information, 'noisette');
			}
		}
	}

	return $information_noisettes;
}
