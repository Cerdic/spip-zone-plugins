<?php
/**
 * Ce fichier contient les fonctions de service N-Core pour les types de noisettes, les conteneurs
 * et les noisettes.
 *
 * Chaque fonction, soit aiguille, si elle existe, vers une fonction "homonyme" propre au plugin appelant
 * ou à un autre service de stockage, soit déroule sa propre implémentation.
 * Ainsi, les plugins externes peuvent, si elle leur convient, utiliser l'implémentation proposée par N-Core
 * en codant un minimum de fonctions.
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
 * @package SPIP\NCORE\TYPE_NOISETTE\SERVICE
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
 *        Identifiant du service de stockage à utiliser si précisé.
 *
 * @return bool
 *        `true` si le traitement s'est bien déroulé, `false` sinon.
 */
function ncore_type_noisette_stocker($plugin, $types_noisette, $recharger, $stockage = '') {

	// Si le plugin utilisateur possède un stockage propre il doit proposer un service spécifique pour enregistrer
	// le type de noisette.
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

		// Initialisation de la sortie.
		$retour = true;
		
		// Initialisation des identifiants des caches
		$cache_descriptions = array(
			'sous_dossier' => $plugin,
			'objet'        => 'type_noisette',
			'fonction'     => 'descriptions'
		);
		$cache_signatures = $cache_descriptions;
		$cache_signatures['fonction'] = 'signatures';

		include_spip('inc/cache');
		if ($recharger) {
			// Si le rechargement est forcé, tous les types de noisette sont nouveaux, on peut donc écraser les caches
			// existants sans s'en préoccuper.
			$descriptions = array_column($types_noisette['a_ajouter'], null, 'type_noisette');
			cache_ecrire('ncore', $cache_descriptions, $descriptions);

			$signatures = array_column($types_noisette['a_ajouter'], 'signature', 'type_noisette');
			cache_ecrire('ncore', $cache_signatures, $signatures);
		} else {
			// On lit les cache existants et on applique les modifications.
			$descriptions = cache_lire('ncore', $cache_descriptions);
			$signatures = cache_lire('ncore', $cache_signatures);

			// On supprime les noisettes obsolètes
			if (!empty($types_noisette['a_effacer'])) {
				$descriptions_obsoletes = array_column($types_noisette['a_effacer'], null, 'type_noisette');
				$descriptions = array_diff($descriptions, $descriptions_obsoletes);

				$signatures_obsoletes = array_column($types_noisette['a_effacer'], 'signature', 'type_noisette');
				$signatures = array_diff($signatures, $signatures_obsoletes);
			}

			// On remplace les noisettes modifiées et on ajoute les noisettes nouvelles. Cette opération peut-être
			// réalisée en une action avec la fonction array_merge.
			if (!empty($types_noisette['a_changer']) or !empty($types_noisette['a_ajouter'])) {
				$descriptions_modifiees = array_column($types_noisette['a_changer'], null, 'type_noisette');
				$descriptions_nouvelles = array_column($types_noisette['a_ajouter'], null, 'type_noisette');
				$descriptions = array_merge($descriptions, $descriptions_modifiees, $descriptions_nouvelles);

				$signatures_modifiees = array_column($types_noisette['a_changer'], 'signature', 'type_noisette');
				$signatures_nouvelles = array_column($types_noisette['a_ajouter'], 'signature', 'type_noisette');
				$signatures = array_merge($signatures, $signatures_modifiees, $signatures_nouvelles);
			}

			// On recrée les caches.
			cache_ecrire('ncore', $cache_descriptions, $descriptions);
			cache_ecrire('ncore', $cache_signatures, $signatures);
		}
	}

	return $retour;
}

/**
 * Complète la description d'un type de noisette issue de la lecture de son fichier YAML.
 *
 * Le plugin N-Core ne complète pas les types de noisette.
 *
 * @package SPIP\NCORE\TYPE_NOISETTE\SERVICE
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
 *        Identifiant du service de stockage à utiliser si précisé.
 *
 * @return array
 *        Description du type de noisette éventuellement complétée par le plugin utilisateur.
 */
function ncore_type_noisette_completer($plugin, $description, $stockage = '') {

	$description_complete = $description;

	// Si le plugin utilisateur complète la description avec des champs spécifiques il doit proposer un service
	// de complément propre.
	if ($completer = ncore_chercher_service($plugin, 'type_noisette_completer', $stockage)) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		$description_complete = $completer($plugin, $description);
	}

	return $description_complete;
}

/**
 * Traite les champs textuels de la description brute d'un type de noisette issue de la lecture de l'espace de stockage
 * avec la fonction typo(). Si le plugin utilisateur complète la description du type de noisette avec de tels champs
 * textuels il doit donc les traiter dans son service dédié.
 *
 * Le plugin N-Core traite toujours les champs `nom` et `description.
 *
 * @package SPIP\NCORE\TYPE_NOISETTE\SERVICE
 *
 * @uses ncore_chercher_service()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $description
 *        Description brute du type de noisette issue de la lecture dans l'espace de stockage du plugin utilisateur.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé.
 *
 * @return array
 *        Description du type de noisette dont les champs textuels ont été traités avec la fonction typo().
 */
function ncore_type_noisette_traiter_typo($plugin, $description, $stockage = '') {

	// N-Core traite toujours les champs nom et description provenant du fichier YAML. On les traite donc
	// systématiquement avant d'appeler le service éventuel du plugin appelant.
	$description['nom'] = typo($description['nom']);
	if ($description['description']) {
		$description['description'] = typo($description['description']);
	}

	// Si le plugin appelant complète la description du type de noisette avec des champs textuels il doit
	// proposer un service propre de traitement de ces champs.
	if ($traiter_typo = ncore_chercher_service($plugin, 'type_noisette_traiter_typo', $stockage)) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		$description = $traiter_typo($plugin, $description);
	}

	return $description;
}

/**
 * Renvoie la description brute d'un type de noisette sans traitement typo ni désérialisation des champs de type
 * tableau sérialisé.
 *
 * Le service N-Core lit la description du type de noisette concerné dans le cache des descriptions.
 *
 * @package SPIP\NCORE\TYPE_NOISETTE\SERVICE
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
 *        Identifiant du service de stockage à utiliser si précisé.
 *
 * @return array
 *        Tableau de la description du type de noisette. Les champs textuels et les champs de type tableau sérialisé
 *        sont retournés en l'état.
 */
function ncore_type_noisette_decrire($plugin, $type_noisette, $stockage = '') {

	// Si le plugin utilisateur possède un stockage propre il doit proposer un service spécifique pour récupérer
	// la description brute d'un type de noisette.
	if ($decrire = ncore_chercher_service($plugin, 'type_noisette_decrire', $stockage)) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		$description = $decrire($plugin, $type_noisette);
	} else {
		// Le plugin ne propose pas de fonction propre ou le stockage N-Core est explicitement demandé.
		// -- Lecture de toute la description du type de noisette à partir du cache.
		// -- Les données sont renvoyées brutes sans traitement sur les textes ni sur les tableaux sérialisés.

		// Initialisation de la description à renvoyer.
		$description = array();
				
		// Initialisation des identifiants des caches
		$cache_descriptions = array(
			'sous_dossier' => $plugin,
			'objet'        => 'type_noisette',
			'fonction'     => 'descriptions'
		);

		include_spip('inc/cache');
		$descriptions = cache_lire('ncore', $cache_descriptions);
		if (isset($descriptions[$type_noisette])) {
			$description = $descriptions[$type_noisette];
		}
	}

	return $description;
}

/**
 * Renvoie, pour l'ensemble des types de noisette, l'information demandée
 * ou toute la description. Les données sont renvoyées brutes.
 *
 * @package SPIP\NCORE\TYPE_NOISETTE\SERVICE
 *
 * @uses ncore_chercher_service()
 * @uses cache_lire()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string $information
 *        Identifiant d'un champ de la description d'un type de noisette y compris le champ `signature`.
 *        Si l'argument est vide, la fonction renvoie les descriptions complètes et si l'argument est
 *        un champ invalide la fonction renvoie un tableau vide.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé.
 *
 * @return array
 *        Tableau de la forme `[type_noisette] = information ou description complète` ou tableau vide.
 */
function ncore_type_noisette_lister($plugin, $information = '', $stockage = '') {

	// Si le plugin utilisateur possède un stockage propre il doit proposer un service spécifique pour récupérer
	// les données requises de tous les types de noisette disponibles.
	if ($lister = ncore_chercher_service($plugin, 'type_noisette_lister', $stockage)) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		$types_noisettes = $lister($plugin, $information);
	} else {
		// Le plugin ne propose pas de fonction propre ou le stockage N-Core est explicitement demandé.
		// -- On lit le cache des descriptions ou des signatures suivant la demande.
		// -- Initialisation du tableau de sortie
		$types_noisettes = array();

		// Initialisation de l'identifiant du cache des descriptions
		$cache = array(
			'sous_dossier' => $plugin,
			'objet'        => 'type_noisette',
			'fonction'     => 'descriptions'
		);

		include_spip('inc/cache');
		if ($information == 'signature') {
			// Les signatures md5 sont sockées dans un fichier cache séparé de celui des descriptions de noisettes.
			$cache['fonction'] = 'signatures';
			$types_noisettes = cache_lire('ncore', $cache);

		} elseif ($descriptions = cache_lire('ncore', $cache)) {
			if ($information) {
				// Si $information n'est pas une colonne valide array_column retournera un tableau vide.
				$types_noisettes = array_column($descriptions, $information, 'type_noisette');
			} else {
				$types_noisettes = $descriptions;
			}
		}
	}

	return $types_noisettes;
}

/**
 * Renvoie la configuration par défaut de l'ajax à appliquer pour la compilation des noisettes.
 * Cette information est utilisée si la description YAML d'un type noisette ne contient pas de tag ajax
 * ou contient un tag ajax à `defaut`.
 *
 * Le service N-Core considère que toute noisette est par défaut insérée en ajax.
 *
 * @package SPIP\NCORE\TYPE_NOISETTE\SERVICE
 *
 * @uses ncore_chercher_service()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 *
 * @return bool
 * 		`true` si par défaut une noisette est insérée en ajax, `false` sinon.
 */
function ncore_type_noisette_initialiser_ajax($plugin) {

	// Si le plugin utilisateur permet la configuration du défaut Ajax ou ne suit pas la configuration de N-Core, il
	// doit proposer un service pour fournir cette valeur.
	if ($configurer = ncore_chercher_service($plugin, 'type_noisette_initialiser_ajax', '')) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		// On autorise la fonction du plugin à retourner autre chose que true ou false si tant est que l'on puisse
		// en déduire un booléen (par exemple, 'on' et '' comme le retourne une case à cocher du plugin Saisies).
		$defaut_ajax = $configurer($plugin) ? true : false;
	} else {
		// Le service ne propose pas de fonction propre, on utilise celle de N-Core.
		$defaut_ajax = true;
	}

	return $defaut_ajax;
}

/**
 * Renvoie la configuration par défaut de l'inclusion dynamique à appliquer pour la compilation des noisettes.
 * Cette information est utilisée si la description YAML d'un type noisette ne contient pas de tag inclusion
 * ou contient un tag inclusion à `defaut`.
 *
 * Le service N-Core considère que toute noisette est par défaut insérée en statique.
 *
 * @package SPIP\NCORE\TYPE_NOISETTE\SERVICE
 *
 * @uses ncore_chercher_service()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 *
 * @return bool
 * 		`true` si par défaut une noisette est insérée en dynamique, `false` sinon.
 */
function ncore_type_noisette_initialiser_inclusion($plugin) {

	// Si le plugin utilisateur permet la configuration du défaut d'inclusion ou ne suit pas la configuration de N-Core,
	// il doit proposer un service pour fournir cette valeur.
	if ($configurer = ncore_chercher_service($plugin, 'type_noisette_initialiser_inclusion', '')) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		// On autorise la fonction du plugin à retourner autre chose que true ou false si tant est que l'on puisse
		// en déduire un booléen (par exemple, 'on' et '' comme le retourne une case à cocher du plugin Saisies).
		$defaut_inclusion = $configurer($plugin) ? true : false;
	} else {
		// Le service ne propose pas de fonction propre, on utilise celle de N-Core.
		$defaut_inclusion = false;
	}

	return $defaut_inclusion;
}

/**
 * Renvoie la configuration par défaut du dossier relatif où trouver les types de noisettes.
 * Cette information est utilisée a minima au chargement des types de noisettes disponibles.
 *
 * Le service N-Core considère que par défaut le dossier relatif des types de noisette est `noisettes/`.
 *
 * @package SPIP\NCORE\TYPE_NOISETTE\SERVICE
 *
 * @uses ncore_chercher_service()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 *
 * @return string
 * 		Chemin relatif du dossier où chercher les types de noisette.
 */
function ncore_type_noisette_initialiser_dossier($plugin) {

	// Si le plugin utilisateur permet la configuration du dossier des types de noisette ou ne suit pas la configuration
	// de N-Core, il doit proposer un service pour fournir cette valeur.
	if ($configurer = ncore_chercher_service($plugin, 'type_noisette_initialiser_dossier', '')) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		// On autorise la fonction du plugin à retourner autre chose que true ou false si tant est que l'on puisse
		// en déduire un booléen (par exemple, 'on' et '' comme le retourne une case à cocher du plugin Saisies).
		$dossier = $configurer($plugin);
	} else {
		// Le service ne propose pas de fonction propre, on utilise celle de N-Core.
		$dossier = 'noisettes/';
	}

	return $dossier;
}


// -----------------------------------------------------------------------
// ----------------------------- NOISETTES -------------------------------
// -----------------------------------------------------------------------

/**
 * Stocke la description d'une nouvelle noisette et calcule son identifiant unique, ou met à jour les paramètres
 * d'affichage d'une noisette existante.
 *
 * @package SPIP\NCORE\NOISETTE\SERVICE
 *
 * @uses ncore_chercher_service()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $description
 *        Description de la noisette. Soit la description ne contient pas l'id de la noisette et c'est un ajout,
 *        soit la description contient l'id et c'est une mise à jour.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé.
 *
 * @return int|string
 *        Id de la noisette de type entier ou chaine.
 *        Le stockage N-Core renvoie lui une chaine construite à partir du plugin et de la fonction uniqid()
 *        ou chaine vide en cas d'erreur.
 */
function ncore_noisette_stocker($plugin, $description, $stockage = '') {

	// Si le plugin utilisateur possède un stockage propre il doit proposer un service spécifique pour stocker
	// les données d'une noisette.
	if ($stocker = ncore_chercher_service($plugin, 'noisette_stocker', $stockage)) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		$id_noisette = $stocker($plugin, $description);
	} else {
		// Le plugin ne propose pas de fonction propre ou le stockage N-Core est explicitement demandé.
		// -- N-Core stocke les noisettes dans une meta propre au plugin appelant contenant un tableau au format
		//    [id_conteneur][rang] = description
		//    N-Core calcule un identifiant unique pour la noisette qui sera stocké à l'index 'id_noisette' de sa
		//    description et qui vaudra uniqid() avec comme préfixe le plugin appelant.

		// Initialisation de l'id de la noisette qui sera fourni en sortie. La valeur chaine vide indique une erreur.
		$id_noisette = '';

		// On lit la meta de stockage des noisettes pour le plugin appelant.
		include_spip('inc/config');
		$noisettes = lire_config("${plugin}_noisettes", array());

		// Détermination de l'identifiant du conteneur qui est inclus dans la description et jamais vide.
		if (!empty($description['id_conteneur'])) {
			$id_conteneur = $description['id_conteneur'];

			if (empty($description['id_noisette'])) {
				// Ajout de la noisette :
				// -- la description est complète à l'exception de l'id unique qui est créé à la volée
				// -- et on range la noisette avec les noisettes affectées au même conteneur en fonction de son rang.
				$description['id_noisette'] = uniqid("${plugin}_");
				$noisettes[$id_conteneur][$description['rang_noisette']] = $description;
			} else {
				// Modification de la noisette :
				// -- les identifiants de la noisette sont toujours fournies, à savoir, l'id, le conteneur et le rang.
				// -- on utilise le conteneur et le rang pour se positionner sur la noisette concernée.
				// -- Les modifications ne concernent que les paramètres d'affichage, cette fonction n'est jamais utilisée
				//    pour le changement de rang.
				if (isset($noisettes[$id_conteneur][$description['rang_noisette']])) {
					$noisettes[$id_conteneur][$description['rang_noisette']] = array_merge(
						$noisettes[$id_conteneur][$description['rang_noisette']],
						$description
					);
				}
			}

			// On met à jour la meta
			ecrire_config("${plugin}_noisettes", $noisettes);

			// On renvoie l'id de la noisette ajoutée ou modifiée.
			$id_noisette = $description['id_noisette'];
		}
	}

	return $id_noisette;
}

/**
 * Transfère une noisette d'un conteneur vers un autre à un rang donné et met à jour la profondeur.
 * Le rang destination n'est pas vérifié lors du rangement dans le conteneur destination. Il convient
 * à l'appelant de vérifier que le rang est libre.
 *
 * @package SPIP\NCORE\NOISETTE\SERVICE
 *
 * @uses ncore_chercher_service()
 * @uses ncore_noisette_destocker()
 * @uses ncore_conteneur_construire()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $description
 *        Description de la noisette à changer de conteneur.
 * @param string $id_conteneur
 *        Identifiant unique sous forme de chaine du conteneur destination.
 * @param int    $rang
 *        Rang où positionner la noisette dans le conteneur destination. Il faut toujours vérifier au préalable
 *        que ce rang est libre.
 * @param int    $profondeur
 *        Profondeur de la noisette à sa nouvelle position.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé.
 *
 * @return array
 */
function ncore_noisette_changer_conteneur($plugin, $description, $id_conteneur, $rang, $profondeur, $stockage = '') {

	// Si le plugin utilisateur possède un stockage propre il doit proposer un service spécifique pour effectuer
	// le changement de conteneur d'une noisette.
	if ($changer = ncore_chercher_service($plugin, 'noisette_changer_conteneur', $stockage)) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		$description = $changer($plugin, $description, $id_conteneur, $rang, $profondeur);
	} else {
		// Le plugin ne propose pas de fonction propre ou le stockage N-Core est explicitement demandé.
		// -- N-Core stocke les noisettes dans une meta propre au plugin appelant contenant un tableau au format
		//    [id_conteneur][rang] = description

		// On supprime la noisette de son emplacement actuel en utilisant la description.
		ncore_noisette_destocker('ncore', $description, $stockage);

		// On lit la meta de stockage des noisettes pour le plugin appelant.
		include_spip('inc/config');
		$noisettes = lire_config("${plugin}_noisettes", array());

		// On rajoute la description à son emplacement destination en prenant soin de modifier les index id_conteneur,
		// conteneur, rang_noisette et profondeur qui doivent représenter le conteneur destination.
		$description['id_conteneur'] = $id_conteneur;
		$description['conteneur'] = ncore_conteneur_construire($plugin, $id_conteneur, $stockage);
		$description['rang_noisette'] = $rang;
		$description['profondeur'] = $profondeur;
		$noisettes[$id_conteneur][$rang] = $description;

		// On met à jour la meta
		ecrire_config("${plugin}_noisettes", $noisettes);
	}

	return $description;
}

/**
 * Complète la description d'une noisette avec des champs spécifiques au plugin utilisateur, si besoin.
 *
 * Le plugin N-Core ne complète pas les descriptions de noisette.
 *
 * @package SPIP\NCORE\NOISETTE\SERVICE
 *
 * @uses ncore_chercher_service()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $description
 *        Description standard de la noisette. Suivant le plugin utilisateur elle nécessite d'être compléter
 *        avant son stockage.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé.
 *
 * @return array
 *        Description de la noisette éventuellement complétée par le plugin utilisateur.
 */
function ncore_noisette_completer($plugin, $description, $stockage = '') {

	// Si le plugin utilisateur complète la description avec des champs spécifiques il doit proposer un service
	// de complément propre.
	if ($completer = ncore_chercher_service($plugin, 'noisette_completer', $stockage)) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		$description = $completer($plugin, $description);
	}

	return $description;
}

/**
 * Traite les champs textuels de la description brute d'une noisette issue de la lecture de l'espace de stockage
 * avec la fonction typo(). Si le plugin utilisateur complète la description de la noisette avec de tels champs
 * textuels il doit donc les traiter dans son service dédié.
 *
 * Le plugin N-Core n'a aucun champ textuel à traiter dans la description de base d'une noisette.
 *
 * @package SPIP\NCORE\NOISETTE\SERVICE
 *
 * @uses ncore_chercher_service()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $description
 *        Description brute de la noisette issue de la lecture dans l'espace de stockage du plugin utilisateur.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé.
 *
 * @return array
 *        Description du type de noisette dont les champs textuels ont été traités avec la fonction typo().
 */
function ncore_noisette_traiter_typo($plugin, $description, $stockage = '') {

	// N-Core n'a aucun champ textuel à traiter dans la description de base d'une noisette.
	// Si le plugin appelant complète la description du type de noisette avec des champs textuels il doit
	// proposer un service propre de traitement typo de ces champs.
	if ($traiter_typo = ncore_chercher_service($plugin, 'noisette_traiter_typo', $stockage)) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		$description = $traiter_typo($plugin, $description);
	}

	return $description;
}


/**
 * Positionne une noisette à un rang différent de celui qu'elle occupe dans le conteneur.
 *
 * @package SPIP\NCORE\NOISETTE\SERVICE
 *
 * @uses ncore_chercher_service()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $description
 *        Description complète de la noisette.
 * @param int    $rang_destination
 *        Position à laquelle ranger la noisette au sein du conteneur.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé.
 *
 * @return bool
 *        `true` si le traitement s'est bien déroulé, `false` sinon.
 */
function ncore_noisette_ranger($plugin, $description, $rang_destination, $stockage = '') {

	// Si le plugin utilisateur possède un stockage propre il doit proposer un service spécifique pour changer
	// la position d'une noisette au sein d'un conteneur.
	if ($ranger = ncore_chercher_service($plugin, 'noisette_ranger', $stockage)) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		$retour = $ranger($plugin, $description, $rang_destination);
	} else {
		// Le plugin ne propose pas de fonction propre ou le stockage N-Core est explicitement demandé.
		// -- N-Core stocke les noisettes dans une meta propre au plugin appelant contenant un tableau au format
		//    [id_conteneur][rang] = description

		// Initialisation de la sortie.
		$retour = false;

		// On lit la meta de stockage des noisettes pour le plugin appelant.
		include_spip('inc/config');
		$noisettes = lire_config("${plugin}_noisettes", array());

		if (!empty($description['id_conteneur'])) {
			$id_conteneur = $description['id_conteneur'];

			// On ajoute la noisette au rang choisi même si on doit écraser un index existant:
			// -- Il est donc nécessaire de gérer la collision en amont de cette fonction.
			// -- Par contre, l'ancien rang de la noisette est supprimé sauf si celui-ci est à zéro.
			$rang_source = $description['rang_noisette'];
			$description['rang_noisette'] = $rang_destination;
			$noisettes[$id_conteneur][$rang_destination] = $description;
			if ($rang_source != 0) {
				unset($noisettes[$id_conteneur][$rang_source]);
			}

			// On met à jour la meta
			ecrire_config("${plugin}_noisettes", $noisettes);
			$retour = true;
		}
	}

	return $retour;
}

/**
 * Retire, de l'espace de stockage, une noisette donnée de son conteneur.
 *
 * @package SPIP\NCORE\NOISETTE\SERVICE
 *
 * @uses ncore_chercher_service()
 *
 * @param string       $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array|string $description
 *        Description complète de la noisette.
 * @param string       $stockage
 *        Identifiant du service de stockage à utiliser si précisé.
 *
 * @return bool
 *        `true` si le traitement s'est bien déroulé, `false` sinon.
 */
function ncore_noisette_destocker($plugin, $description, $stockage = '') {

	// Si le plugin utilisateur possède un stockage propre il doit proposer un service spécifique pour supprimer
	// une noisette de son conteneur.
	if ($destocker = ncore_chercher_service($plugin, 'noisette_destocker', $stockage)) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		$retour = $destocker($plugin, $description);
	} else {
		// Le plugin ne propose pas de fonction propre ou le stockage N-Core est explicitement demandé.
		// -- N-Core stocke les noisettes dans une meta propre au plugin appelant contenant un tableau au format
		//    [id_conteneur][rang] = description

		// Initialisation de la sortie.
		$retour = false;

		include_spip('inc/config');
		$meta_noisettes = lire_config("${plugin}_noisettes", array());

		if (isset($meta_noisettes[$description['id_conteneur']][$description['rang_noisette']])) {
			// On supprime une noisette donnée.
			unset($meta_noisettes[$description['id_conteneur']][$description['rang_noisette']]);
			// Si c'est la dernière noisette du conteneur il faut aussi supprimer l'index correspondant au conteneur.
			if (!$meta_noisettes[$description['id_conteneur']]) {
				unset($meta_noisettes[$description['id_conteneur']]);
			}
			ecrire_config("${plugin}_noisettes", $meta_noisettes);
			$retour = true;
		}
	}

	return $retour;
}


/**
 * Renvoie un champ ou toute la description des noisettes d'un conteneur ou de tous les conteneurs.
 * Le tableau retourné est indexé soit par identifiant de noisette soit par identifiant du conteneur et rang
 * de noisette. Les données sont renvoyées brutes.
 *
 * @package SPIP\NCORE\NOISETTE\SERVICE
 *
 * @uses ncore_chercher_service()
 * @uses ncore_conteneur_identifier()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array|string $conteneur
 *        Tableau descriptif du conteneur ou identifiant du conteneur ou vide si on souhaite adresser tous les
 *        conteneurs.
 * @param string $information
 *        Identifiant d'un champ de la description d'une noisette.
 *        Si l'argument est vide, la fonction renvoie les descriptions complètes et si l'argument est
 *        un champ invalide la fonction renvoie un tableau vide.
 * @param string $cle
 *        Champ de la description d'une noisette servant d'index du tableau. En général on utilisera soit `id_noisette`
 *        soit `rang_noisette`.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé.
 *
 * @return array
 *        Tableau de la liste des informations demandées indexé par identifiant de noisette ou par rang. Les données
 *        sont renvoyées brutes.
 */
function ncore_noisette_lister($plugin, $conteneur = array(), $information = '', $cle = 'rang_noisette', $stockage = '') {

	// Si le plugin utilisateur possède un stockage propre il doit proposer un service spécifique pour récupérer
	// les données demandées d'une liste noisette.
	if ($lister = ncore_chercher_service($plugin, 'noisette_lister', $stockage)) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		$noisettes = $lister($plugin, $conteneur, $information, $cle);
	} else {
		// Le plugin ne propose pas de fonction propre ou le stockage N-Core est explicitement demandé.
		// -- N-Core stocke les noisettes dans une meta propre au plugin appelant contenant un tableau au format
		//    [id_conteneur][rang] = description

		// Initialisation du tableau de sortie.
		$noisettes = array();

		include_spip('inc/config');
		$meta_noisettes = lire_config("${plugin}_noisettes", array());

		if ($conteneur) {
			// On détermine l'id du conteneur en fonction du mode d'identification du conteneur lors de l'appel.
			if (is_array($conteneur)) {
				$id_conteneur = ncore_conteneur_identifier($plugin, $conteneur, $stockage);
			} else {
				$id_conteneur = $conteneur;
			}
			if (!empty($meta_noisettes[$id_conteneur])) {
				$noisettes = $meta_noisettes[$id_conteneur];
				$noisettes = $information
					? array_column($noisettes, $information, $cle)
					: array_column($noisettes, null, $cle);
			}
		} elseif ($meta_noisettes) {
			if ($cle == 'rang_noisette') {
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
 * @package SPIP\NCORE\NOISETTE\SERVICE
 *
 * @uses ncore_chercher_service()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param mixed  $noisette
 *        Identifiant de la noisette qui peut prendre soit la forme d'un entier ou d'une chaine unique, soit la forme
 *        d'un couple (conteneur, rang de noisette).
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé.
 *
 * @return array
 *        Tableau de la description du type de noisette. Les champs textuels et les champs de type tableau sérialisé
 *        sont retournés en l'état.
 */
function ncore_noisette_decrire($plugin, $noisette, $stockage = '') {

	// Si le plugin utilisateur possède un stockage propre il doit proposer un service spécifique pour récupérer
	// la description brute d'une noisette.
	if ($decrire = ncore_chercher_service($plugin, 'noisette_decrire', $stockage)) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		$description = $decrire($plugin, $noisette);
	} else {
		// Le plugin ne propose pas de fonction propre ou le stockage N-Core est explicitement demandé.
		// -- N-Core stocke les noisettes dans une meta propre au plugin appelant contenant un tableau au format
		//    [id_conteneur][rang] = description

		// Initialisation de la description à retourner.
		$description = array();

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
				if (isset($noisette['id_conteneur'], $noisette['rang_noisette'])) {
					// Détermination de l'identifiant du conteneur.
					$id_conteneur = $noisette['id_conteneur'];
					if (!empty($meta_noisettes[$id_conteneur][$noisette['rang_noisette']])) {
					// L'identifiant est un tableau associatif fournissant le conteneur et le rang.
					// Comme la meta de N-Core est structurée ainsi c'est la méthode la plus adaptée pour adresser
					// le stockage de N-Core.
					$description = $meta_noisettes[$id_conteneur][$noisette['rang_noisette']];
					}
				}
			}
		}
	}

	return $description;
}

/**
 * Renvoie la configuration par défaut de l'encapsulation d'une noisette.
 * Cette information est utilisée si le champ `encapsulation` de la noisette vaut `defaut`.
 *
 * Le service N-Core encapsule toujours les noisettes.
 *
 * @package SPIP\NCORE\NOISETTE\SERVICE
 *
 * @uses ncore_chercher_service()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 *
 * @return bool
 * 		`true` si par défaut une noisette est encapsulée, `false` sinon.
 */
function ncore_noisette_initialiser_encapsulation($plugin) {

	// Si le plugin utilisateur permet la configuration du défaut d'encapsulaiton ou ne suit pas la configuration
	// de N-Core, il doit proposer un service pour fournir cette valeur.
	if ($configurer = ncore_chercher_service($plugin, 'noisette_initialiser_encapsulation', '')) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		// On autorise la fonction du plugin à retourner autre chose que true ou false si tant est que l'on puisse
		// en déduire un booléen (par exemple, 'on' et '' comme le retourne une case à cocher du plugin Saisies).
		$defaut_capsule = $configurer($plugin) ? true : false;
	} else {
		// Le service ne propose pas de fonction propre, on utilise celle de N-Core.
		$defaut_capsule = true;
	}

	return $defaut_capsule;
}


// -----------------------------------------------------------------------
// ----------------------------- CONTENEURS ------------------------------
// -----------------------------------------------------------------------

/**
 * Vérifie la conformité des index du tableau représentant le conteneur et supprime les index inutiles, si besoin.
 * N-Core vérifie que pour les noisettes conteneur les seuls index sont le type et l'id de la noisette.
 * Pour les autres conteneurs, c'est au plugin utilisateur de vérifier le conteneur.
 *
 * @package SPIP\NCORE\CONTENEUR\SERVICE
 *
 * @uses ncore_chercher_service()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $conteneur
 *        Tableau associatif descriptif du conteneur dont les index doivent être vérifiés.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé.
 *
 * @return array
 *        Tableau du conteneur dont tous les index sont conformes ou tableau vide si non conforme.
 */
function ncore_conteneur_verifier($plugin, $conteneur, $stockage = '') {

	static $index_conteneur_noisette = array('type_noisette', 'id_noisette');

	// N-Core ne vérifie pas les conteneurs spécifiques aux plugins utilisateur
	// sauf pour les noisettes conteneur qui ne sont déterminées que par leur type et leur id.
	// Il est donc indispensable que le plugin utilisateur propose toujours une fonction de vérification
	// pour les conteneurs hors noisette conteneur.
	$conteneur_verifie = array();
	if ($conteneur) {
		if (isset($conteneur['type_noisette'], $conteneur['id_noisette'])
		and $conteneur['type_noisette']
		and intval($conteneur['id_noisette'])) {
			// Le conteneur est une noisette, N-Core effectue le filtre des index.
			$conteneur = array_intersect_key($conteneur, array_flip($index_conteneur_noisette));
			if (count($conteneur) == 2) {
				$conteneur_verifie = $conteneur;
			}
		} else {
			// Le conteneur est spécifique au plugin utilisateur, c'est donc au plugin faire la vérification des index.
			if ($verifier = ncore_chercher_service($plugin, 'conteneur_verifier', $stockage)) {
				$conteneur_verifie = $verifier($plugin, $conteneur);
			}
		}
	}

	return $conteneur_verifie;
}

/**
 * Construit un identifiant unique pour le conteneur sous forme de chaine.
 * N-Core ne fournit d'identifiant que pour les noisettes conteneur.
 * Pour les autres conteneurs, c'est au plugin utilisateur de calculer l'identifiant.
 *
 * @package SPIP\NCORE\CONTENEUR\SERVICE
 *
 * @uses ncore_chercher_service()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $conteneur
 *        Tableau associatif descriptif du conteneur. Les index sont spécifiques à l'utilisation qui en est faite
 *        par le plugin utilisateur. Néanmoins, pour une noisette conteneur, le tableau est limité aux index
 *        type de noisette et id de noisette.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé.
 *
 * @return string
 *        Identifiant du conteneur ou chaine vide en cas d'erreur.
 */
function ncore_conteneur_identifier($plugin, $conteneur, $stockage = '') {

	// Il faut calculer l'identifiant du conteneur pour accéder à la bonne liste de noisettes.
	// N-Core ne propose pas de fonction par défaut pour les conteneurs spécifiques aux plugins utilisateur
	// sauf pour les noisettes conteneur car elles ne sont déterminées que par leur type et leur id.
	// Il est donc indispensable que le plugin utilisateur propose toujours une fonction de calcul de l'identifiant
	// pour les conteneurs hors noisette conteneur.
	$id_conteneur = '';
	if ($conteneur) {
		if (isset($conteneur['type_noisette'], $conteneur['id_noisette'])
		and $conteneur['type_noisette']
		and intval($conteneur['id_noisette'])) {
			// Le conteneur est une noisette, N-Core effectue le calcul de l'id.
			$id_conteneur = $conteneur['type_noisette'] . '|noisette|' . $conteneur['id_noisette'];
		} else {
			// Le conteneur est spécifique au plugin utilisateur, c'est donc au plugin de le calculer.
			if ($identifier = ncore_chercher_service($plugin, 'conteneur_identifier', $stockage)) {
				$id_conteneur = $identifier($plugin, $conteneur);
			}
		}
	}

	return $id_conteneur;
}

/**
 * Reconstruit le conteneur sous forme de tableau canonique à partir de son identifiant unique (fonction inverse
 * de `ncore_conteneur_identifier`).
 * N-Core ne fournit le tableau que pour les noisettes conteneur.
 * Pour les autres conteneurs, c'est au plugin utilisateur de calculer le tableau.
 *
 * @package SPIP\NCORE\CONTENEUR\SERVICE
 *
 * @uses ncore_chercher_service()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string $id_conteneur
 *        Identifiant unique du conteneur. Si l'id correspond à une noisette conteneur le traitement sera fait
 *        par N-Core, sinon par le plugin utilisateur
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé.
 *
 * @return array
 *        Tableau représentatif du conteneur ou tableau vide en cas d'erreur.
 */
function ncore_conteneur_construire($plugin, $id_conteneur, $stockage = '') {

	// Il faut recomposer le tableau du conteneur à partir de son id.
	// N-Core ne propose pas de fonction par défaut pour les conteneurs spécifiques aux plugins utilisateur
	// mais uniquement pour les noisettes conteneur.
	// Il est donc indispensable que le plugin utilisateur propose toujours une fonction de calcul du tableau
	// pour les conteneurs hors noisette conteneur.
	$conteneur = array();
	if ($id_conteneur) {
		$elements = explode('|', $id_conteneur);
		if ((count($elements) == 3) and ($elements[1] == 'noisette')) {
			// C'est une noisette conteneur : les index sont le type et l'id de noisette.
			$conteneur['type_noisette'] = $elements[0];
			$conteneur['id_noisette'] = intval($elements[2]);
		} else {
			// Le conteneur est spécifique au plugin utilisateur, c'est donc au plugin de le calculer.
			if ($construire = ncore_chercher_service($plugin, 'conteneur_construire', $stockage)) {
				$conteneur = $construire($plugin, $id_conteneur);
			}
		}
	}

	return $conteneur;
}

/**
 * Détermine si un conteneur est une noisette ou pas. Le conteneur a été vérifié au préalable.
 * Ce service est le seul a ne pas être surchargeable par un plugin utilisateur car les noisettes conteneur
 * sont gérées entièrement par N-Core.
 *
 * @package SPIP\NCORE\CONTENEUR\SERVICE
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array $conteneur
 *        Identifiant du conteneur sous forme de tableau canonique.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé.
 *
 * @return bool
 *        `true` si le conteneur est une noisette `false` sinon.
 */
function ncore_conteneur_est_noisette($plugin, $conteneur, $stockage = '') {

	// Initialiser la sortie
	$est_noisette = false;

	// On détermine à partir du tableau si le conteneur est une noisette.
	if (isset($conteneur['type_noisette'], $conteneur['id_noisette'])
	and $conteneur['type_noisette']
	and intval($conteneur['id_noisette'])) {
		$est_noisette = true;
	}

	return $est_noisette;
}

/**
 * Retire, de l'espace de stockage, toutes les noisettes d'un conteneur et ce de façon récursive si
 * il existe une imbrication de conteneurs.
 *
 * @package SPIP\NCORE\CONTENEUR\SERVICE
 *
 * @uses ncore_noisette_lister()
 * @uses ncore_conteneur_destocker()
 * @uses ncore_chercher_service()
 * @uses ncore_conteneur_identifier()
 *
 * @param string       $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array|string $conteneur
 *        Tableau descriptif du conteneur ou identifiant du conteneur.
 * @param string       $stockage
 *        Identifiant du service de stockage à utiliser si précisé.
 *
 * @return bool
 *        `true` si le traitement s'est bien déroulé, `false` sinon.
 */
function ncore_conteneur_destocker($plugin, $conteneur, $stockage = '') {

	// Initialisation de la sortie.
	$retour = false;

	// On liste les noisettes du conteneur concerné et on repère les noisettes conteneur.
	// Chaque conteneur imbriqué est vidé et ce de façon récursive.
	foreach (ncore_noisette_lister($plugin, $conteneur, '', 'rang_noisette', $stockage) as $_noisette) {
		if ($_noisette['est_conteneur'] == 'oui') {
			// On vide récursivement les noisettes de type conteneur.
			ncore_conteneur_destocker($plugin, $_noisette, $stockage);
		}
	}

	// Si le plugin utilisateur possède un stockage propre il doit proposer un service spécifique pour supprimer
	// les noisettes d'un conteneur.
	if ($destocker = ncore_chercher_service($plugin, 'conteneur_destocker', $stockage)) {
		// On passe le plugin appelant à la fonction car cela permet ainsi de mutualiser les services de stockage.
		$retour = $destocker($plugin, $conteneur);
	} else {
		// Le plugin ne propose pas de fonction propre ou le stockage N-Core est explicitement demandé.
		// -- N-Core stocke les noisettes dans une meta propre au plugin appelant contenant un tableau au format
		//    [conteneur][rang] = description
		include_spip('inc/config');
		$meta_noisettes = lire_config("${plugin}_noisettes", array());

		// On détermine l'id du conteneur en fonction du mode d'identification du conteneur lors de l'appel.
		if (is_array($conteneur)) {
			$id_conteneur = ncore_conteneur_identifier($plugin, $conteneur, $stockage);
		} else {
			$id_conteneur = $conteneur;
		}

		if (isset($meta_noisettes[$id_conteneur])) {
			// On supprime toutes les noisettes du conteneur.
			unset($meta_noisettes[$id_conteneur]);
			ecrire_config("${plugin}_noisettes", $meta_noisettes);
			$retour = true;
		}
	}

	return $retour;
}


// -----------------------------------------------------------------------
// ---------------- UTILITAIRE PROPRE AU PLUGIN N-CORE -------------------
// -----------------------------------------------------------------------

/**
 * Cherche une fonction donnée en se basant sur le service de stockage ou à défaut sur le plugin appelant.
 * Si ni le service de stockage ni le plugin ne fournissent la fonction demandée la chaîne vide est renvoyée.
 *
 * @internal
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param bool   $fonction
 *        Nom de la fonction à chercher.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin
 *        ni celui de N-Core ne seront utilisés. En général, cet identifiant est le préfixe d'un plugin
 *        fournissant le service de stockage souhaité.
 *
 * @return string
 *        Nom complet de la fonction si trouvée ou chaine vide sinon.
 */
function ncore_chercher_service($plugin, $fonction, $stockage = '') {

	$fonction_trouvee = '';

	// Si le stockage n'est pas précisé on cherche la fonction dans le plugin appelant.
	if (!$stockage) {
		$stockage = $plugin;
	}

	// Eviter la réentrance si on demande explicitement le stockage N-Core
	if ($stockage != 'ncore') {
		include_spip("ncore/${stockage}");
		$fonction_trouvee = "${stockage}_${fonction}";
		if (!function_exists($fonction_trouvee)) {
			$fonction_trouvee = '';
		}
	}

	return $fonction_trouvee;
}
