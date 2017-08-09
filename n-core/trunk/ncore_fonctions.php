<?php

// Securite
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// -------------------------------------------------------------------
// ------------------------- API NOISETTES ---------------------------
// -------------------------------------------------------------------

/**
 * Chargement ou rechargement de descriptions de noisettes à partir de leurs fichiers YAML.
 * Les noisettes sont recherchées dans un répertoire relatif fourni en argument.
 * La fonction optimise le chargement en effectuant uniquement les traitements nécessaires
 * en fonction des modifications, ajouts et suppressions de noisettes identifiés en comparant les md5
 * des fichiers YAML.
 *
 * @package SPIP\NCORE\NOISETTE
 * @api
 * @filtre
 *
 * @param string	$service
 *      Le service permet de distinguer l'appelant qui peut-être un plugin comme le noiZetier ou
 *      un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 *      La fonction utilisera les fonctions de lecture des md5 et de stockage des descriptions de noisettes,
 * 		spécifiques au service, ou à défaut, celles fournies par N-Core.
 * @param string	$dossier
 * 		Chemin relatif (avec un `/` final) dans lequel chercher les fichiers YAML de description des noisettes.
 * 		Par défaut, les noisettes seront recherchées dans le dossier `noisettes/`.
 * @param bool		$recharger
 *      Si `true` forcer le rechargement de toutes les noisettes, sinon le traitement se base sur
 *		le md5 des fichiers YAML.
 *
 * @return bool
 * 		`false` si une erreur s'est produite, `true` sinon.
 */
function ncore_noisette_charger($service, $dossier = 'noisettes/', $recharger = false) {

	// Retour de la fonction
	$retour = true;

	// Initialiser le contexte de rechargement
	// -- TODO : en attente de voir si on rajoute un var_mode=recalcul_noisettes
	$options['recharger'] = $recharger;

	// On recherche les noisettes directement par leur fichier YAML de configuration car il est obligatoire
	// -- la recherche s'effectue dans
	if ($fichiers = find_all_in_path($dossier, '.+[.]yaml$')) {
		// On charge l'API de NCore.
		// Ce sont ces fonctions qui aiguillent ou pas vers une fonction spécifique du service.
		include_spip("ncore/ncore");

		// Initialisation des tableaux de noisettes
		$noisettes_nouvelles = $noisettes_modifiees = $noisettes_obsoletes = array();

		// Récupération des signatures md5 des noisettes déjà enregistrées.
		// Si on force le rechargement il est inutile de gérer les signatures et les noisettes modifiées ou obsolètes.
		$signatures = array();
		if (!$options['recharger']) {
			$signatures = ncore_noisette_lister_signatures($service);
			// On initialise la liste des noisettes à supprimer avec l'ensemble des noisettes en base de données.
			$noisettes_obsoletes = $signatures ? array_keys($signatures) : array();
		}

		include_spip('inc/ncore_noisette');
		foreach ($fichiers as $_squelette => $_chemin) {
			$noisette = basename($_squelette, '.yaml');
			// On passe le md5 de la page si il existe sinon la chaine vide. Cela permet de déterminer
			// si on doit ajouter la page ou la mettre à jour.
			// Si le md5 est le même et qu'il n'est donc pas utile de recharger la page, la configuration
			// retournée est vide.
			$options['md5'] = isset($signatures[$noisette]) ? $signatures[$noisette] : '';
			$options['yaml'] = $_chemin;
			if ($configuration = noisette_phraser($noisette, $options)) {
				if (empty($configuration['identique'])) {
					// La noisette a été chargée (nouvelle) ou rechargée (modifiée).
					// Néanmoins, on n'inclue cette noisette que si les plugins qu'elle nécessite explicitement dans son
					// fichier de configuration sont bien tous activés.
					// Rappel: si une noisette est incluse dans un plugin non actif elle ne sera pas détectée
					//         lors du find_all_in_path() puisque le plugin n'est pas dans le path SPIP.
					//         Ce n'est pas ce cas qui est traité ici.
					$noisette_a_garder = true;
					$necessite = unserialize($configuration['necessite']);
					if (!empty($necessite)) {
						foreach ($necessite as $plugin) {
							if (!defined('_DIR_PLUGIN_'.strtoupper($plugin))) {
								$noisette_a_garder = false;
								break;
							}
						}
					}

					// Si la noisette est à garder on détermine si elle est nouvelle ou modifiée.
					// En mode rechargement forcé toute noisette est considérée comme nouvelle.
					// Sinon, la noisette doit être retirée de la base car un plugin qu'elle nécessite a été désactivée:
					// => il suffit pour cela de la laisser dans la liste des noisettes obsolètes.
					if ($noisette_a_garder) {
						if (!$options['md5'] or $options['recharger']) {
							// La noisette est soit nouvelle soit on est en mode rechargement forcé:
							// => il faut la rajouter dans la table.
							$noisettes_nouvelles[] = $configuration;
						} else {
							// La configuration stockée dans la table a été modifiée et le mode ne force pas le rechargement:
							// => il faut mettre à jour la noisette dans la table.
							$noisettes_modifiees[] = $configuration;
							// => il faut donc la supprimer de la liste des noisettes obsolètes
							$noisettes_obsoletes = array_diff($noisettes_obsoletes, array($noisette));
						}
					}
				} else {
					// La noisette n'a pas changée et n'a donc pas été réchargée:
					// => Il faut donc juste indiquer qu'elle n'est pas obsolète.
					$noisettes_obsoletes = array_diff($noisettes_obsoletes, array($noisette));
				}
			} else {
				// Il y a eu une erreur sur lors du rechargement de la noisette.
				// => il faut donc ne rien faire pour laisser la noisette dans les obsolètes
				continue;
			}
		}

		// Mise à jour du stockage des noisettes si au moins un des 3 tableaux est non vide et que le chargement forcé
		// n'est pas demandé:
		// -- Suppression des noisettes obsolètes ou de toutes les noisettes si on est en mode rechargement forcé.
		//    Pour permettre une optimisation du traitement en mode rechargement forcé on passe toujours le mode.
		// -- Update des pages modifiées
		// -- Insertion des nouvelles pages
		if ($recharger
		or (!$recharger and ($noisettes_nouvelles or $noisettes_obsoletes or $noisettes_modifiees))) {
			$noisettes = array('nouvelles' => $noisettes_nouvelles);
			if (!$options['recharger']) {
				$noisettes['obsoletes'] = $noisettes_obsoletes;
				$noisettes['modifiees'] = $noisettes_modifiees;
			}
			$retour = ncore_noisette_stocker($service, $noisettes, $options['recharger']);
		}
	}

	return $retour;
}


/**
 * Retourne la description complète ou seulement une information précise pour une noisette donnée.
 * Les données textuelles peuvent subir une traitement typo si demandé.
 *
 * @package SPIP\NCORE\NOISETTE
 * @api
 * @filtre
 *
 * @param string	$service
 *      Le service permet de distinguer l'appelant qui peut-être un plugin comme le noiZetier ou
 *      un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 *      La fonction utilisera la fonction de lecture de la description brute d'une noisette, spécifique
 *      au service, ou à défaut, celle fournie par N-Core.
 * @param string	$noisette
 * 		Identifiant de la $noisette.
 * @param string	$information
 * 		Information spécifique à retourner ou vide pour retourner toute la description.
 * @param boolean	$traiter_typo
 *      Indique si les données textuelles doivent être retournées brutes ou si elles doivent être traitées
 *      en utilisant la fonction _T_ou_typo. Par défaut l'indicateur vaut `false`.
 * 		Les champs sérialisés sont eux toujours désérialisés.
 *
 * @return array|string
 */
function ncore_noisette_informer($service, $noisette, $information = '', $traiter_typo = false) {

	// On indexe le tableau des indicateurs ajax par le service appelant en cas d'appel sur le même hit
	// par deux services différents.
	static $donnees_typo = array('nom', 'description');
	static $description_noisette = array();

	// Stocker la description de la noisette si besoin
	if (!isset($description_noisette[$service][$noisette])) {
		// On charge l'API de NCore.
		// Ce sont ces fonctions qui aiguillent ou pas vers une fonction spécifique du service.
		include_spip("ncore/ncore");

		// Lecture de toute la configuration de la noisette: les données retournées sont brutes.
		$description = ncore_noisette_decrire($service, $noisette);

		// Sauvegarde de la description de la page pour une consultation ultérieure dans le même hit.
		if ($description) {
			// Traitements des champs tableaux sérialisés
			$description['contexte'] = unserialize($description['contexte']);
			$description['necessite'] = unserialize($description['necessite']);
			$description['parametres'] = unserialize($description['parametres']);

			// Stockage de la description
			$description_noisette[$service][$noisette] = $description;
		} else {
			$description_noisette[$service][$noisette] = array();
		}
	}

	if ($information) {
		if (isset($description_noisette[$service][$noisette][$information])) {
			if (in_array($information, $donnees_typo) and $traiter_typo) {
				// Traitements de la donnée textuelle
				$retour = _T_ou_typo($description_noisette[$service][$noisette][$information]);
			} else {
				$retour = $description_noisette[$service][$noisette][$information];
			}
		} else {
			$retour = '';
		}
	} else {
		$retour = $description_noisette[$service][$noisette];
		// Traitements des données textuels
		if ($traiter_typo) {
			$retour['nom'] = _T_ou_typo($retour['nom']);
			if (isset($retour['description'])) {
				$retour['description'] = _T_ou_typo($retour['description']);
			}
		}
	}

	return $retour;
}


/**
 * Détermine si la noisette spécifiée doit être incluse en AJAX ou pas.
 *
 * @package SPIP\NCORE\NOISETTE
 * @api
 * @filtre
 *
 * @param string	$service
 *      Le service permet de distinguer l'appelant qui peut-être un plugin comme le noiZetier ou
 *      un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 *      La fonction utilisera les fonctions de lecture de la configuration globale de l'ajax et de
 *      lecture du paramètre ajax de la noisette, spécifiques au service, ou à défaut, celles fournies par N-Core.
 * @param string	$noisette
 * 		Identifiant de la $noisette.
 *
 * @return bool
 * 		`true` si la noisette doit être ajaxée, `false` sinon.
 */
function ncore_noisette_est_ajax($service, $noisette) {

	// On indexe le tableau des indicateurs ajax par le service appelant en cas d'appel sur le même hit
	// par deux services différents.
	static $est_ajax = array();

	if (!isset($est_ajax[$service][$noisette])) {
		// On détermine le cache en fonction du service, puis son existence et son contenu.
		include_spip('inc/ncore_cache');
		$est_ajax[$service] = cache_lire($service, _NCORE_NOMCACHE_NOISETTE_AJAX);

		// On doit recalculer le cache.
		if (!$est_ajax[$service]
		or (_request('var_mode') == 'recalcul')
		or (defined('_NO_CACHE') and (_NO_CACHE != 0))) {
			// On charge l'API de NCore.
			// Ce sont ces fonctions qui aiguillent ou pas vers une fonction spécifique du service.
			include_spip("ncore/ncore");

			// On détermine la valeur par défaut de l'ajax des noisettes pour le service appelant.
			$defaut_ajax = ncore_noisette_config_ajax($service);

			// On repertorie la configuration ajax de toutes les noisettes disponibles et on compare
			// avec la valeur par défaut configurée pour le service appelant.
			if ($ajax_noisettes = ncore_noisette_lister($service,'ajax')) {
				foreach ($ajax_noisettes as $_noisette => $_ajax) {
					$est_ajax[$service][$_noisette] = ($_ajax == 'defaut')
						? $defaut_ajax
						: ($_ajax == 'non' ? false : true);
				}
			}

			// On vérifie que la noisette demandée est bien dans la liste.
			// Si non, on la rajoute en utilisant la valeur ajax par défaut afin de toujours renvoyer
			// quelque chose.
			if (!isset($est_ajax[$service][$noisette])) {
				$est_ajax[$service][$noisette] = $defaut_ajax;
			}

			// In fine, on met à jour le cache
			cache_ecrire($service, _NCORE_NOMCACHE_NOISETTE_AJAX, $est_ajax[$service]);
		}
	}

	return $est_ajax[$service][$noisette];
}


/**
 * Détermine si la noisette spécifiée doit être incluse dynamiquement ou pas.
 *
 * @package SPIP\NCORE\NOISETTE
 * @api
 * @filtre
 *
 * @param string	$service
 *      Le service permet de distinguer l'appelant qui peut-être un plugin comme le noiZetier ou
 *      un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 *      La fonction utilisera les fonctions de lecture du paramètre d'inclusion de la noisette,
 *      spécifique au service, ou à défaut, celle fournie par N-Core.
 * @param string	$noisette
 * 		Identifiant de la $noisette.
 *
 * @return bool
 * 		`true` si la noisette doit être incluse dynamiquement, `false` sinon.
 */
function ncore_noisette_est_dynamique($service, $noisette) {

	// On indexe le tableau des indicateurs ajax par le service appelant en cas d'appel sur le même hit
	// par deux services différents.
	static $est_dynamique = array();

	if (!isset($est_dynamique[$service][$noisette])) {
		// On détermine le cache en fonction du service, puis son existence et son contenu.
		include_spip('inc/ncore_cache');
		$est_dynamique[$service] = cache_lire($service, _NCORE_NOMCACHE_NOISETTE_INCLUSION);

		// On doit recalculer le cache.
		if (!$est_dynamique[$service]
		or (_request('var_mode') == 'recalcul')
		or (defined('_NO_CACHE') and (_NO_CACHE != 0))) {
			// On charge l'API de N-Core.
			// Ce sont ces fonctions qui aiguillent ou pas vers une fonction spécifique du service.
			include_spip("ncore/ncore");

			// On repertorie la configuration d'inclusion de toutes les noisettes disponibles et on
			// détermine si celle-ci est dynamique ou pas.
			if ($inclusion_noisettes = ncore_noisette_lister($service,'inclusion')) {
				foreach ($inclusion_noisettes as $_noisette => $_inclusion) {
					$est_dynamique[$service][$_noisette] = ($_inclusion == 'dynamique') ? true : false;
				}
			}

			// On vérifie que la noisette demandée est bien dans la liste.
			// Si non, on la rajoute en utilisant en positionnant l'inclusion dynamique à false.
			if (!isset($est_dynamique[$service][$noisette])) {
				$est_dynamique[$service][$noisette] = _NCORE_DYNAMIQUE_DEFAUT;
			}

			// In fine, on met à jour le cache
			cache_ecrire($service, _NCORE_NOMCACHE_NOISETTE_INCLUSION, $est_dynamique[$service]);
		}
	}

	return $est_dynamique[$service][$noisette];
}


/**
 * Renvoie le contexte de la noisette sous la forme d'un tableau éventuellement vide.
 *
 * @package SPIP\NCORE\NOISETTE
 * @api
 * @filtre
 *
 * @param string	$service
 *      Le service permet de distinguer l'appelant qui peut-être un plugin comme le noiZetier ou
 *      un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 *      La fonction utilisera les fonctions de lecture du contexte de la noisette, spécifique au service,
 *      ou à défaut, celle fournie par N-Core.
 * @param string	$noisette
 * 		Identifiant de la $noisette.
 *
 * @return array
 * 		Le tableau éventuellement vide des éléments de contexte de la noisette.
 */
function ncore_noisette_contexte($service, $noisette) {

	// On indexe le tableau des indicateurs ajax par le service appelant en cas d'appel sur le même hit
	// par deux services différents.
	static $contexte = array();

	if (!isset($contexte[$service][$noisette])) {
		// On détermine le cache en fonction du service, puis son existence et son contenu.
		include_spip('inc/ncore_cache');
		$contexte[$service] = cache_lire($service, _NCORE_NOMCACHE_NOISETTE_CONTEXTE);

		// On doit recalculer le cache.
		if (!$contexte[$service]
		or (_request('var_mode') == 'recalcul')
		or (defined('_NO_CACHE') and (_NO_CACHE != 0))) {
			// On charge l'API de N-Core.
			// Ce sont ces fonctions qui aiguillent ou pas vers une fonction spécifique du service.
			include_spip("ncore/ncore");

			// On repertorie la configuration du contexte de toutes les noisettes disponibles et on
			// le renvoie le résultat tel quel.
			$contexte[$service] = ncore_noisette_lister($service, 'contexte');
			$contexte[$service] = unserialize($contexte[$service]);

			// On vérifie que la noisette demandée est bien dans la liste.
			// Si non, on la rajoute en utilisant en positionnant le contexte à tableau vide.
			if (!isset($contexte[$service][$noisette])) {
				$contexte[$service][$noisette] = array();
			}

			// In fine, on met à jour le cache
			cache_ecrire($service, _NCORE_NOMCACHE_NOISETTE_CONTEXTE, $contexte[$service]);
		}
	}

	return $contexte[$service][$noisette];
}
