<?php
/**
 * Ce fichier contient les fonctions du service de stockage N-Core pour les types de noisettes et les noisettes.
 *
 * Chaque fonction, soit aiguille, si elle existe, vers une fonction "homonyme" propre au plugin appelant
 * ou à un autre service de stockage, soit déroule sa propre implémentation.
 * Ainsi, les plugins externes peuvent, si elle leur convient, utiliser l'implémentation proposée par N-Core
 * sans coder la moindre fonction.
 *
 * @package SPIP\NCORE\DATA
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Stocke les descriptions des types de noisette en distinguant les types de noisette obsolètes, les types de
 * noisettes modifiés et les types de noisettes nouveaux.
 * Chaque description de type de noisette est un tableau associatif dont tous les index possibles - y compris
 * la signature - sont initialisés quelque soit le contenu du fichier YAML.
 *
 * Le service N-Core stocke les descriptions dans un cache et les signatures dans un autre.
 *
 * @uses cache_lire()
 * @uses cache_ecrire()
 *
 * @param string	$plugin
 *      Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *      un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array		$types_noisette
 *      Tableau associatif à 3 entrées fournissant les descriptions des types de noisettes nouveaux, obsolètes
 * 		et modifiés:
 * 		`a_effacer` : liste des identifiants de type de noisette devenus obsolètes.
 * 		`a_changer` : liste des descriptions des types de noisette dont le fichier YAML a été modifié.
 *      `a_ajouter` : liste des descriptions des nouveaux types de noisette.
 * 		Si $recharger est à `true`, seul l'index `nouvelles` est fourni dans le tableau $types_noisette.
 * @param bool		$recharger
 *      Indique si le chargement en cours est forcé ou pas. Cela permet à la fonction N-Core ou au service
 *      concerné d'optimiser le traitement sachant que seules les types de noisette nouveaux sont fournis.
 * @param string	$stockage
 *      Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin
 *      ni celui de N-Core ne seront utilisés. En général, cet identifiant est le préfixe d'un plugin
 * 		fournissant le service de stockage souhaité.
 *
 * @return bool
 * 		`true` si le traitement s'est bien déroulé, `false` sinon.
 */
function ncore_type_noisette_stocker($plugin, $types_noisette, $recharger, $stockage = '') {

	$retour = true;

	// On cherche le service de stockage à utiliser selon la logique suivante :
	// - si le service de stockage est non vide on l'utilise en considérant que la fonction existe forcément;
	// - sinon, on utilise la fonction du plugin appelant si elle existe;
	// - et sinon, on utilise la fonction de N-Core.
	include_spip('inc/ncore_utils');
	if ($stocker = ncore_chercher_fonction($plugin, 'type_noisette_stocker', $stockage)) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		$retour = $stocker($plugin, $types_noisette, $recharger);
	} else {
		// Le plugin ne propose pas de fonction propre ou le stockage N-Core est explicitement demandé.
		// Les descriptions de types de noisette et les signatures sont stockés dans deux caches distincts.
		// -- Les descriptions : on conserve la signature pour chaque description, le tableau est réindexé avec l'identifiant
		//    de la noisette.
		// -- Les signatures : on isole la liste des signatures et on indexe le tableau avec l'identifiant de la noisette.
		include_spip('inc/ncore_cache');
		if ($recharger) {
			// Si le rechargement est forcé, tous les types de noisette sont nouveaux, on peut donc écraser les caches
			// existants sans s'en préoccuper.
			$descriptions = array_column($types_noisette['a_ajouter'], null, 'noisette');
			cache_ecrire($plugin, _NCORE_NOMCACHE_NOISETTE_DESCRIPTION, $descriptions);

			$signatures = array_column($types_noisette['a_ajouter'], 'signature', 'noisette');
			cache_ecrire($plugin, _NCORE_NOMCACHE_NOISETTE_SIGNATURE, $signatures);
		} else {
			// On lit les cache existants et on applique les modifications.
			$descriptions = cache_lire($plugin, _NCORE_NOMCACHE_NOISETTE_DESCRIPTION);
			$signatures = cache_lire($plugin,_NCORE_NOMCACHE_NOISETTE_SIGNATURE);

			// On supprime les noisettes obsolètes
			if (!empty($types_noisette['a_effacer'])) {
				$descriptions_obsoletes = array_column($types_noisette['a_effacer'], null, 'noisette');
				$descriptions = array_diff($descriptions, $descriptions_obsoletes);

				$signatures_obsoletes = array_column($types_noisette['a_effacer'], 'signature', 'noisette');
				$signatures = array_diff($signatures, $signatures_obsoletes);
			}

			// On remplace les noisettes modifiées et on ajoute les noisettes nouvelles. Cette opération peut-être
			// réalisée en une action avec la fonction array_merge.
			if (!empty($types_noisette['a_changer']) or !empty($types_noisette['a_ajouter'])) {
				$descriptions_modifiees = array_column($types_noisette['a_changer'], null, 'noisette');
				$descriptions_nouvelles = array_column($types_noisette['a_ajouter'], null, 'noisette');
				$descriptions = array_merge($descriptions, $descriptions_modifiees, $descriptions_nouvelles);

				$signatures_modifiees = array_column($types_noisette['a_changer'], 'signature', 'noisette');
				$signatures_nouvelles = array_column($types_noisette['a_ajouter'], 'signature', 'noisette');
				$signatures = array_merge($signatures, $signatures_modifiees, $signatures_nouvelles);
			}

			// On recrée les caches.
			cache_ecrire($plugin, _NCORE_NOMCACHE_NOISETTE_DESCRIPTION, $descriptions);
			cache_ecrire($plugin, _NCORE_NOMCACHE_NOISETTE_SIGNATURE, $signatures);
		}
	}

	return $retour;
}

/**
 * Renvoie la description brute d'un type de noisette sans traitement typo ni désérialisation des champs de type
 * tableau sérialisé.
 *
 * Le service N-Core lit la description du type de noisette concerné dans le cache des descriptions.
 *
 * @uses cache_lire()
 *
 * @param string	$plugin
 *      Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *      un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string	$type_noisette
 * 		Identifiant du type de noisette.
 * @param string	$stockage
 *      Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin
 *      ni celui de N-Core ne seront utilisés. En général, cet identifiant est le préfixe d'un plugin
 * 		fournissant le service de stockage souhaité.
 *
 * @return array
 * 		Tableau de la description du type de noisette. Les champs textuels et les champs de type tableau sérialisé
 * 		sont retournés en l'état.
 */
function ncore_type_noisette_decrire($plugin, $type_noisette, $stockage = '') {

	$description = array();

	// On cherche le service de stockage à utiliser selon la logique suivante :
	// - si le service de stockage est non vide on l'utilise en considérant que la fonction existe forcément;
	// - sinon, on utilise la fonction du plugin appelant si elle existe;
	// - et sinon, on utilise la fonction de N-Core.
	include_spip('inc/ncore_utils');
	if ($decrire = ncore_chercher_fonction($plugin, 'type_noisette_decrire', $stockage)) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		$description = $decrire($plugin, $type_noisette);
	} else {
		// Le plugin ne propose pas de fonction propre ou le stockage N-Core est explicitement demandé.
		// -- Lecture de toute la description du type de noisette à partir du cache.
		// -- Les données sont renvoyées brutes sans traitement sur les textes ni sur les tableaux sérialisés.
		include_spip('inc/ncore_cache');
		$descriptions = cache_lire($plugin, _NCORE_NOMCACHE_NOISETTE_DESCRIPTION);
		if (isset($descriptions[$type_noisette])) {
			$description = $descriptions[$type_noisette];
		}
	}

	return $description;
}

/**
 * Renvoie l'information brute demandée pour l'ensemble des types de noisette utilisés par le plugin appelant.
 *
 * @uses cache_lire()
 *
 * @param string	$plugin
 *      Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *      un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string	$information
 *      Identifiant d'un champ de la description d'un type de noisette ou `signature`.
 *      Si l'argument est vide ou invalide, la fonction renvoie un tableau vide.
 * @param string	$stockage
 *      Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin ni celui de N-Core
 * 		ne seront utilisés. En général, cet identifiant est le préfixe du plugin fournissant le stockage.
 *
 * @return array
 * 		Tableau de la forme `[noisette] = information`.
 */
function ncore_type_noisette_lister($plugin, $information, $stockage = '') {

	// Initialisation du tableau de sortie
	$information_noisettes = array();

	// On cherche le service de stockage à utiliser selon la logique suivante :
	// - si le service de stockage est non vide on l'utilise en considérant que la fonction existe forcément;
	// - sinon, on utilise la fonction du plugin appelant si elle existe;
	// - et sinon, on utilise la fonction de N-Core.
	include_spip('inc/ncore_utils');
	if ($lister = ncore_chercher_fonction($plugin, 'type_noisette_lister', $stockage)) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		$information_noisettes = $lister($plugin, $information);
	} else {
		// Le plugin ne propose pas de fonction propre ou le stockage N-Core est explicitement demandé.
		if ($information) {
			include_spip('inc/ncore_cache');
			if ($information == 'signature') {
				// Les signatures md5 sont sockées dans un fichier cache séparé de celui des descriptions de noisettes.
				$information_noisettes = cache_lire($plugin, _NCORE_NOMCACHE_NOISETTE_SIGNATURE);
			}
			elseif ($descriptions = cache_lire($plugin, _NCORE_NOMCACHE_NOISETTE_DESCRIPTION)) {
				// Si $information n'est pas une colonne valide array_column retournera un tableau vide.
				$information_noisettes = array_column($descriptions, $information, 'noisette');
			}
		}
	}

	return $information_noisettes;
}
