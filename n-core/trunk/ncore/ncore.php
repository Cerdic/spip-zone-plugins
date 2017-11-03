<?php
/**
 * Ce fichier contient les fonctions du service de stockage N-Core pour les types de noisettes et les noisettes.
 *
 * Chaque fonction, soit aiguille, si elle existe, vers une fonction "homonyme" propre au plugin appelant
 * ou à un autre service de stockage, soit déroule sa propre implémentation.
 * Ainsi, les plugins externes peuvent, si elle leur convient, utiliser l'implémentation proposée par N-Core
 * en codant un minimum de fonction, à savoir, `conteneur_identifier`.
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


// -----------------------------------------------------------------------
// ------------------------- TYPES DE NOISETTE ---------------------------
// -----------------------------------------------------------------------

/**
 * Stocke les descriptions des types de noisette en distinguant les types de noisette obsolètes, les types de
 * noisettes modifiés et les types de noisettes nouveaux.
 * Chaque description de type de noisette est un tableau associatif dont tous les index possibles - y compris
 * la signature - sont initialisés quelque soit le contenu du fichier YAML.
 *
 * Le service N-Core stocke les descriptions dans un cache et les signatures dans un autre.
 *
 * @package SPIP\NCORE\SERVICE\TYPE_NOISETTE
 *
 * @uses ncore_chercher_service()
 * @uses cache_lire()
 * @uses cache_ecrire()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $types_noisette
 *        Tableau associatif à 3 entrées fournissant les descriptions des types de noisettes nouveaux, obsolètes
 *        et modifiés:
 *        `a_effacer` : liste des identifiants de type de noisette devenus obsolètes.
 *        `a_changer` : liste des descriptions des types de noisette dont le fichier YAML a été modifié.
 *        `a_ajouter` : liste des descriptions des nouveaux types de noisette.
 *        Si $recharger est à `true`, seul l'index `nouvelles` est fourni dans le tableau $types_noisette.
 * @param bool   $recharger
 *        Indique si le chargement en cours est forcé ou pas. Cela permet à la fonction N-Core ou au service
 *        concerné d'optimiser le traitement sachant que seules les types de noisette nouveaux sont fournis.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin ni celui de N-Core
 *        ne seront utilisés. En général, cet identifiant est le préfixe du plugin fournissant le stockage.
 *
 * @return bool
 *        `true` si le traitement s'est bien déroulé, `false` sinon.
 */
function ncore_type_noisette_stocker($plugin, $types_noisette, $recharger, $stockage = '') {

	$retour = true;

	// On cherche le service de stockage à utiliser selon la logique suivante :
	// - si le service de stockage est non vide on l'utilise en considérant que la fonction existe forcément;
	// - sinon, on utilise la fonction du plugin appelant si elle existe;
	// - et sinon, on utilise la fonction de N-Core.
	include_spip('inc/ncore_utils');
	if ($stocker = ncore_chercher_service($plugin, 'type_noisette_stocker', $stockage)) {
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
			cache_ecrire($plugin, _NCORE_NOMCACHE_TYPE_NOISETTE_DESCRIPTION, $descriptions);

			$signatures = array_column($types_noisette['a_ajouter'], 'signature', 'noisette');
			cache_ecrire($plugin, _NCORE_NOMCACHE_TYPE_NOISETTE_SIGNATURE, $signatures);
		} else {
			// On lit les cache existants et on applique les modifications.
			$descriptions = cache_lire($plugin, _NCORE_NOMCACHE_TYPE_NOISETTE_DESCRIPTION);
			$signatures = cache_lire($plugin, _NCORE_NOMCACHE_TYPE_NOISETTE_SIGNATURE);

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
			cache_ecrire($plugin, _NCORE_NOMCACHE_TYPE_NOISETTE_DESCRIPTION, $descriptions);
			cache_ecrire($plugin, _NCORE_NOMCACHE_TYPE_NOISETTE_SIGNATURE, $signatures);
		}
	}

	return $retour;
}

/**
 * Complète la description d'un type de noisette issue de la lecture de son fichier YAML.
 *
 * Le plugin N-Core ne complète pas les types de noisette.
 *
 * @package SPIP\NCORE\SERVICE\TYPE_NOISETTE
 *
 * @uses ncore_chercher_service()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $description
 *        Description du type de noisette issue de la lecture du fichier YAML. Suivant le plugin utilisateur elle
 *        nécessite d'être compléter avant son stockage.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin ni celui de N-Core
 *        ne seront utilisés. En général, cet identifiant est le préfixe du plugin fournissant le stockage.
 *
 * @return array
 *        Description du type de noisette éventuellement compléter par le plugin utilisateur.
 */
function ncore_type_noisette_completer($plugin, $description, $stockage = '') {

	$description_complete = $description;

	// On cherche le service de stockage à utiliser selon la logique suivante :
	// - si le service de stockage est non vide on l'utilise en considérant que la fonction existe forcément;
	// - sinon, on utilise la fonction du plugin appelant si elle existe;
	// - et sinon, on utilise la fonction de N-Core.
	include_spip('inc/ncore_utils');
	if ($completer = ncore_chercher_service($plugin, 'type_noisette_completer', $stockage)) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		$description_complete = $completer($plugin, $description);
	}

	return $description_complete;
}

/**
 * Renvoie la description brute d'un type de noisette sans traitement typo ni désérialisation des champs de type
 * tableau sérialisé.
 *
 * Le service N-Core lit la description du type de noisette concerné dans le cache des descriptions.
 *
 * @package SPIP\NCORE\SERVICE\TYPE_NOISETTE
 *
 * @uses ncore_chercher_service()
 * @uses cache_lire()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string $type_noisette
 *        Identifiant du type de noisette.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin ni celui de N-Core
 *        ne seront utilisés. En général, cet identifiant est le préfixe du plugin fournissant le stockage.
 *
 * @return array
 *        Tableau de la description du type de noisette. Les champs textuels et les champs de type tableau sérialisé
 *        sont retournés en l'état.
 */
function ncore_type_noisette_decrire($plugin, $type_noisette, $stockage = '') {

	$description = array();

	// On cherche le service de stockage à utiliser selon la logique suivante :
	// - si le service de stockage est non vide on l'utilise en considérant que la fonction existe forcément;
	// - sinon, on utilise la fonction du plugin appelant si elle existe;
	// - et sinon, on utilise la fonction de N-Core.
	include_spip('inc/ncore_utils');
	if ($decrire = ncore_chercher_service($plugin, 'type_noisette_decrire', $stockage)) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		$description = $decrire($plugin, $type_noisette);
	} else {
		// Le plugin ne propose pas de fonction propre ou le stockage N-Core est explicitement demandé.
		// -- Lecture de toute la description du type de noisette à partir du cache.
		// -- Les données sont renvoyées brutes sans traitement sur les textes ni sur les tableaux sérialisés.
		include_spip('inc/ncore_cache');
		$descriptions = cache_lire($plugin, _NCORE_NOMCACHE_TYPE_NOISETTE_DESCRIPTION);
		if (isset($descriptions[$type_noisette])) {
			$description = $descriptions[$type_noisette];
		}
	}

	return $description;
}

/**
 * Renvoie l'information brute demandée pour l'ensemble des types de noisette utilisés par le plugin appelant
 * ou toute les descriptions si aucune information n'est explicitement demandée.
 *
 * @package SPIP\NCORE\SERVICE\TYPE_NOISETTE
 *
 * @uses ncore_chercher_service()
 * @uses cache_lire()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string $information
 *        Identifiant d'un champ de la description d'un type de noisette ou `signature`.
 *        Si l'argument est vide, la fonction renvoie les descriptions complètes et si l'argument est
 *        un champ invalide la fonction renvoie un tableau vide.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin ni celui de N-Core
 *        ne seront utilisés. En général, cet identifiant est le préfixe du plugin fournissant le stockage.
 *
 * @return array
 *        Tableau de la forme `[noisette] = information ou description complète`.
 */
function ncore_type_noisette_lister($plugin, $information = '', $stockage = '') {

	// Initialisation du tableau de sortie
	$types_noisettes = array();

	// On cherche le service de stockage à utiliser selon la logique suivante :
	// - si le service de stockage est non vide on l'utilise en considérant que la fonction existe forcément;
	// - sinon, on utilise la fonction du plugin appelant si elle existe;
	// - et sinon, on utilise la fonction de N-Core.
	include_spip('inc/ncore_utils');
	if ($lister = ncore_chercher_service($plugin, 'type_noisette_lister', $stockage)) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		$types_noisettes = $lister($plugin, $information);
	} else {
		// Le plugin ne propose pas de fonction propre ou le stockage N-Core est explicitement demandé.
		include_spip('inc/ncore_cache');
		if ($information == 'signature') {
			// Les signatures md5 sont sockées dans un fichier cache séparé de celui des descriptions de noisettes.
			$types_noisettes = cache_lire($plugin, _NCORE_NOMCACHE_TYPE_NOISETTE_SIGNATURE);
		} elseif ($descriptions = cache_lire($plugin, _NCORE_NOMCACHE_TYPE_NOISETTE_DESCRIPTION)) {
			if ($information) {
				// Si $information n'est pas une colonne valide array_column retournera un tableau vide.
				$types_noisettes = array_column($descriptions, $information, 'noisette');
			} else {
				$types_noisettes = $descriptions;
			}
		}
	}

	return $types_noisettes;
}


// -----------------------------------------------------------------------
// ----------------------------- NOISETTES -------------------------------
// -----------------------------------------------------------------------

/**
 * Stocke la description d'une nouvelle noisette et calcule son identifiant unique, ou met à jour les paramètres
 * d'affichage d'une noisette existante.
 *
 * @package SPIP\NCORE\SERVICE\NOISETTE
 *
 * @uses ncore_chercher_service()
 * @uses ncore_conteneur_identifier()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $description
 *        Description de la noisette. Soit la description ne contient pas l'id de la noisette et c'est un ajout,
 *        soit la description contient l'id et c'est une mise à jour.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin ni celui de N-Core
 *        ne seront utilisés. En général, cet identifiant est le préfixe du plugin fournissant le stockage.
 *
 * @return int|string
 *        Id de la noisette de type entier ou chaine.
 *        Le stockage N-Core renvoie lui une chaine construite à partir du plugin et de la fonction uniqid().
 */
function ncore_noisette_stocker($plugin, $description, $stockage = '') {

	// On cherche le service de stockage à utiliser selon la logique suivante :
	// - si le service de stockage est non vide on l'utilise en considérant que la fonction existe forcément;
	// - sinon, on utilise la fonction du plugin appelant si elle existe;
	// - et sinon, on utilise la fonction de N-Core.
	include_spip('inc/ncore_utils');
	if ($stocker = ncore_chercher_service($plugin, 'noisette_stocker', $stockage)) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		$id_noisette = $stocker($plugin, $description);
	} else {
		// Le plugin ne propose pas de fonction propre ou le stockage N-Core est explicitement demandé.
		// -- N-Core stocke les noisettes dans une meta propre au plugin appelant contenant un tableau au format
		//    [conteneur][rang] = description
		//    N-Core calcule un identifiant unique pour la noisette qui sera stocké à l'index 'id_noisette' de sa
		//    description et qui vaudra uniqid() avec comme préfixe le plugin appelant.

		// On lit la meta de stockage des noisettes pour le plugin appelant.
		include_spip('inc/config');
		$noisettes = lire_config("${plugin}_noisettes", array());

		// Détermination de l'identifiant du conteneur. Le conteneur est un tableau sérialisé qui fait partie
		// de la description.
		$id_conteneur = ncore_conteneur_identifier(
			$plugin,
			unserialize($description['conteneur']),
			$stockage
		);

		if (empty($description['id_noisette'])) {
			// Ajout de la noisette :
			// -- la description est complète à l'exception de l'id unique qui est créé à la volée
			// -- et on range la noisette avec les noisettes affectées au même conteneur en fonction de son rang.
			$description['id_noisette'] = uniqid("${plugin}_");
			$noisettes[$id_conteneur][$description['rang']] = $description;
		} else {
			// Modification de la noisette :
			// -- les identifiants de la noisette sont toujours fournies, à savoir, l'id, le conteneur et le rang.
			// -- on utilise le conteneur et le rang pour se positionner sur la noisette concernée.
			// -- Les modifications ne concernent que les paramètres d'affichage, cette fonction n'est jamais utilisée
			//    pour le changement de rang.
			if (isset($noisettes[$id_conteneur][$description['rang']])) {
				$noisettes[$id_conteneur][$description['rang']] = array_merge(
					$noisettes[$id_conteneur][$description['rang']],
					$description
				);
			}
		}

		// On met à jour la meta
		ecrire_config("${plugin}_noisettes", $noisettes);

		// On renvoie l'id de la noisette ajoutée ou modifiée.
		$id_noisette = $description['id_noisette'];
	}

	return $id_noisette;
}


/**
 * Complète la description d'une noisette avec des champs spécifiques au plugin utilisateur si besoin.
 *
 * Le plugin N-Core ne complète pas les descriptions de noisette.
 *
 * @package SPIP\NCORE\SERVICE\NOISETTE
 *
 * @uses ncore_chercher_service()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $description
 *        Description de la noisette par défaut. Suivant le plugin utilisateur elle nécessite d'être compléter
 *        avant son stockage.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin ni celui de N-Core
 *        ne seront utilisés. En général, cet identifiant est le préfixe du plugin fournissant le stockage.
 *
 * @return array
 *        Description de la noisette éventuellement complétée par le plugin utilisateur.
 */
function ncore_noisette_completer($plugin, $description, $stockage = '') {

	$description_complete = $description;

	// On cherche le service de stockage à utiliser selon la logique suivante :
	// - si le service de stockage est non vide on l'utilise en considérant que la fonction existe forcément;
	// - sinon, on utilise la fonction du plugin appelant si elle existe;
	// - et sinon, on utilise la fonction de N-Core.
	include_spip('inc/ncore_utils');
	if ($completer = ncore_chercher_service($plugin, 'noisette_completer', $stockage)) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		$description_complete = $completer($plugin, $description);
	}

	return $description_complete;
}


/**
 * Positionne une noisette à un rang différent que celui qu'elle occupe dans le conteneur.
 *
 * @package SPIP\NCORE\SERVICE\NOISETTE
 *
 * @uses ncore_chercher_service()
 * @uses ncore_conteneur_identifier()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $description
 *        Description complète de la noisette.
 * @param int    $rang_destination
 *        Position à laquelle ranger la noisette au sein du conteneur.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin ni celui de N-Core
 *        ne seront utilisés. En général, cet identifiant est le préfixe du plugin fournissant le stockage.
 *
 * @return bool
 *        `true` si le traitement s'est bien déroulé, `false` sinon.
 */
function ncore_noisette_ranger($plugin, $description, $rang_destination, $stockage = '') {

	// Initialisation de la sortie.
	$retour = false;

	// On cherche le service de stockage à utiliser selon la logique suivante :
	// - si le service de stockage est non vide on l'utilise en considérant que la fonction existe forcément;
	// - sinon, on utilise la fonction du plugin appelant si elle existe;
	// - et sinon, on utilise la fonction de N-Core.
	include_spip('inc/ncore_utils');
	if ($ranger = ncore_chercher_service($plugin, 'noisette_ranger', $stockage)) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		$retour = $ranger($plugin, $description, $rang_destination);
	} else {
		// Le plugin ne propose pas de fonction propre ou le stockage N-Core est explicitement demandé.
		// -- N-Core stocke les noisettes dans une meta propre au plugin appelant contenant un tableau au format
		//    [conteneur][rang] = description

		// On lit la meta de stockage des noisettes pour le plugin appelant.
		include_spip('inc/config');
		$noisettes = lire_config("${plugin}_noisettes", array());

		// Détermination de l'identifiant du conteneur. Le conteneur est un tableau sérialisé qui fait partie
		// de la description.
		$id_conteneur = ncore_conteneur_identifier(
			$plugin,
			unserialize($description['conteneur']),
			$stockage
		);

		// On ajoute la noisette au rang choisi même si on doit écraser un index existant:
		// -- Il est donc nécessaire de gérer la collision en amont de cette fonction.
		// -- Par contre, l'ancien rang de la noisette est supprimé sauf si celui-ci est à zéro.
		$rang_source = $description['rang'];
		$description['rang'] = $rang_destination;
		$noisettes[$id_conteneur][$rang_destination] = $description;
		if ($rang_source != 0) {
			unset($noisettes[$id_conteneur][$rang_source]);
		}

		// On met à jour la meta
		ecrire_config("${plugin}_noisettes", $noisettes);
		$retour = true;
	}

	return $retour;
}

/**
 * Retire, de l'espace de stockage, une noisette donnée de son conteneur ou l'ensemble des noisettes d'un conteneur.
 *
 * @package SPIP\NCORE\SERVICE\NOISETTE
 *
 * @uses ncore_chercher_service()
 * @uses ncore_conteneur_identifier()
 *
 * @param string       $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array|string $description
 *        Description complète de la noisette ou identifiant du conteneur.
 * @param string       $stockage
 *        Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin ni celui de N-Core
 *        ne seront utilisés. En général, cet identifiant est le préfixe du plugin fournissant le stockage.
 *
 * @return bool
 *        `true` si le traitement s'est bien déroulé, `false` sinon.
 */
function ncore_noisette_destocker($plugin, $description, $stockage = '') {

	// Initialisation de la sortie.
	$retour = false;

	// On cherche le service de stockage à utiliser selon la logique suivante :
	// - si le service de stockage est non vide on l'utilise en considérant que la fonction existe forcément;
	// - sinon, on utilise la fonction du plugin appelant si elle existe;
	// - et sinon, on utilise la fonction de N-Core.
	include_spip('inc/ncore_utils');
	if ($destocker = ncore_chercher_service($plugin, 'noisette_destocker', $stockage)) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		$retour = $destocker($plugin, $description);
	} else {
		// Le plugin ne propose pas de fonction propre ou le stockage N-Core est explicitement demandé.
		// -- N-Core stocke les noisettes dans une meta propre au plugin appelant contenant un tableau au format
		//    [conteneur][rang] = description
		// -- $description est soit le tableau descriptif de la noisette, soit le conteneur, et dans ce cas, il faut
		//    supprimer toutes les noisettes du conteneur.
		include_spip('inc/config');
		$meta_noisettes = lire_config("${plugin}_noisettes", array());

		// Détermination de l'identifiant du conteneur. Le conteneur est un tableau sérialisé qui fait partie
		// de la description.
		$id_conteneur = ncore_conteneur_identifier(
			$plugin,
			unserialize($description['conteneur']),
			$stockage
		);

		if (!empty($description['id_noisette']) and isset($meta_noisettes[$id_conteneur][$description['rang']])) {
			// On supprime une noisette donnée.
			unset($meta_noisettes[$id_conteneur][$description['rang']]);
			// Si c'est la dernière noisette du conteneur il faut aussi supprimer l'index correspondant au conteneur.
			if (!$meta_noisettes[$id_conteneur]) {
				unset($meta_noisettes[$id_conteneur]);
			}
			ecrire_config("${plugin}_noisettes", $meta_noisettes);
			$retour = true;
		} elseif (isset($meta_noisettes[$id_conteneur])) {
			// On supprime toutes les noisettes du conteneur.
			unset($meta_noisettes[$id_conteneur]);
			ecrire_config("${plugin}_noisettes", $meta_noisettes);
			$retour = true;
		}
	}

	return $retour;
}


/**
 * Renvoie un champ ou toute la description des noisettes d'un conteneur ou de tous les conteneurs.
 * Le tableau retourné est indexé soit par identifiant de noisette soit par identifiant du conteneur et rang.
 *
 * @package SPIP\NCORE\SERVICE\NOISETTE
 *
 * @uses ncore_chercher_service()
 * @uses ncore_conteneur_identifier()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $conteneur
 *        Tableau associatif descriptif du conteneur accueillant la noisette. Un conteneur peut-être un squelette seul
 *        ou associé à un contexte d'utilisation et dans ce cas il possède un index `squelette` ou un objet quelconque
 *        sans lien avec un squelette. Dans tous les cas, les index, à l'exception de `squelette`, sont spécifiques
 *        à l'utilisation qui en est faite par le plugin.
 * @param string $information
 *        Identifiant d'un champ de la description d'une type de noisette.
 *        Si l'argument est vide, la fonction renvoie les descriptions complètes et si l'argument est
 *        un champ invalide la fonction renvoie un tableau vide.
 * @param string $cle
 *        Champ de la description d'une noisette servant d'index du tableau. En général on utilisera soit `id_noisette`
 *        soit `rang`.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin ni celui de N-Core
 *        ne seront utilisés. En général, cet identifiant est le préfixe du plugin fournissant le stockage.
 *
 * @return array
 *        Tableau de la liste des informations demandées indexé par identifiant de noisette ou par rang.
 */
function ncore_noisette_lister($plugin, $conteneur = array(), $information = '', $cle = 'rang', $stockage = '') {

	// Initialisation du tableau de sortie.
	$noisettes = array();

	// On cherche le service de stockage à utiliser selon la logique suivante :
	// - si le service de stockage est non vide on l'utilise en considérant que la fonction existe forcément;
	// - sinon, on utilise la fonction du plugin appelant si elle existe;
	// - et sinon, on utilise la fonction de N-Core.
	include_spip('inc/ncore_utils');
	if ($lister = ncore_chercher_service($plugin, 'noisette_lister', $stockage)) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		$noisettes = $lister($plugin, $conteneur, $information, $cle);
	} else {
		// Le plugin ne propose pas de fonction propre ou le stockage N-Core est explicitement demandé.
		// -- N-Core stocke les noisettes dans une meta propre au plugin appelant contenant un tableau au format
		//    [conteneur][rang] = description
		include_spip('inc/config');
		$meta_noisettes = lire_config("${plugin}_noisettes", array());

		if ($conteneur) {
			// Détermination de l'identifiant du conteneur.
			$id_conteneur = ncore_conteneur_identifier($plugin, $conteneur, $stockage);
			if (!empty($meta_noisettes[$id_conteneur])) {
				$noisettes = $meta_noisettes[$id_conteneur];
				$noisettes = $information
					? array_column($noisettes, $information, $cle)
					: array_column($noisettes, null, $cle);
			}
		} elseif ($meta_noisettes) {
			if ($cle == 'rang') {
				$noisettes = $meta_noisettes;
			} else {
				foreach ($meta_noisettes as $_squelette => $_descriptions) {
					$noisettes_squelette = $information
						? array_column($_descriptions, $information, $cle)
						: array_column($_descriptions, null, $cle);
					$noisettes = array_merge($noisettes, $noisettes_squelette);
				}
			}
		}
	}

	return $noisettes;
}


/**
 * Renvoie la description brute d'une noisette sans traitement typo des champs textuels ni désérialisation
 * des champs de type tableau sérialisé.
 *
 * @package SPIP\NCORE\SERVICE\NOISETTE
 *
 * @uses ncore_chercher_service()
 * @uses ncore_conteneur_identifier()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param mixed  $noisette
 *        Identifiant de la noisette qui peut prendre soit la forme d'un entier ou d'une chaine unique, soit la forme
 *        d'un couple (conteneur, rang).
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin ni celui de N-Core
 *        ne seront utilisés. En général, cet identifiant est le préfixe du plugin fournissant le stockage.
 *
 * @return array
 *        Tableau de la description du type de noisette. Les champs textuels et les champs de type tableau sérialisé
 *        sont retournés en l'état.
 */
function ncore_noisette_decrire($plugin, $noisette, $stockage = '') {

	$description = array();

	// On cherche le service de stockage à utiliser selon la logique suivante :
	// - si le service de stockage est non vide on l'utilise en considérant que la fonction existe forcément;
	// - sinon, on utilise la fonction du plugin appelant si elle existe;
	// - et sinon, on utilise la fonction de N-Core.
	include_spip('inc/ncore_utils');
	if ($decrire = ncore_chercher_service($plugin, 'noisette_decrire', $stockage)) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		$description = $decrire($plugin, $noisette);
	} else {
		// Le plugin ne propose pas de fonction propre ou le stockage N-Core est explicitement demandé.
		// -- N-Core stocke les noisettes dans une meta propre au plugin appelant contenant un tableau au format
		//    [conteneur][rang] = description
		include_spip('inc/config');
		$meta_noisettes = lire_config("${plugin}_noisettes", array());

		// On recherche la description dans la meta.
		if ($meta_noisettes) {
			if (!is_array($noisette)) {
				// L'identifiant est l'id unique de la noisette. Il faut donc parcourir le tableau pour trouver la
				// noisette désirée
				// => ce n'est pas la méthode optimale pour le stockage N-Core.
				$noisette_trouvee = false;
				foreach ($meta_noisettes as $_noisettes_squelette) {
					foreach ($_noisettes_squelette as $_noisette) {
						if ($_noisette['id_noisette'] == $noisette) {
							$description = $_noisette;
							$noisette_trouvee = true;
							break;
						}
					}
					if ($noisette_trouvee) {
						break;
					}
				}
			} else {
				if (isset($noisette['squelette'], $noisette['contexte'], $noisette['rang'])) {
					// Détermination de l'identifiant du conteneur.
					$id_conteneur = ncore_conteneur_identifier(
						$plugin,
						$noisette['conteneur'],
						$stockage
					);
					if (!empty($meta_noisettes[$id_conteneur][$noisette['rang']])) {
					// L'identifiant est un tableau associatif fournissant le conteneur et le rang.
					// Comme la meta de N-Core est structurée ainsi c'est la méthode la plus adaptée pour adresser
					// le stockage de N-Core.
					$description = $meta_noisettes[$id_conteneur][$noisette['rang']];
					}
				}
			}
		}
	}

	return $description;
}


// -----------------------------------------------------------------------
// ----------------------------- CONTENEURS ------------------------------
// -----------------------------------------------------------------------

/**
 * Construit un identifiant unique pour le conteneur sous forme de chaine.
 * Cette fonction est juste un aiguillage vers la fonction éventuelle du plugin utilisateur
 * car N-Core ne fournit pas de calcul par défaut.
 *
 * @package SPIP\NCORE\SERVICE\CONTENEUR
 *
 * @uses ncore_chercher_service()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $conteneur
 *        Tableau associatif descriptif du conteneur accueillant la noisette. Un conteneur peut-être un squelette seul
 *        ou associé à un contexte d'utilisation et dans ce cas il possède un index `squelette` ou un objet quelconque
 *        sans lien avec un squelette. Dans tous les cas, les index, à l'exception de `squelette`, sont spécifiques
 *        à l'utilisation qui en est faite par le plugin.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin ni celui de N-Core
 *        ne seront utilisés. En général, cet identifiant est le préfixe du plugin fournissant le stockage.
 *
 * @return string
 *        Identifiant du conteneur ou chaine vide en cas d'erreur.
 */
function ncore_conteneur_identifier($plugin, $conteneur, $stockage) {

	// Il faut calculer l'identifiant du conteneur pour accéder à la bonne liste de noisettes.
	// N-Core ne propose pas de fonction par défaut car l'élaboration de l'identifiant est totalement spécifique
	// au plugin utilisateur.
	// Il est donc indispensable que le plugin utilisateur propose toujours une fonction de calcul de l'identifiant.
	$id_conteneur = '';
	if ($conteneur) {
		include_spip('inc/ncore_utils');
		if ($identifier = ncore_chercher_service($plugin, 'squelette_identifier', $stockage)) {
			// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
			$id_conteneur = $identifier($plugin, $conteneur);
		}
	}

	return $id_conteneur;
}
