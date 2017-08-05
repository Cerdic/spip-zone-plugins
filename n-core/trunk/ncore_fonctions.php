<?php

// Securite
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

define('_NCORE_CACHE_AJAX_NOISETTES', _DIR_CACHE . 'noisettes_ajax.php');
define('_NCORE_CACHE_CONTEXTE_NOISETTES', _DIR_CACHE . 'noisettes_contexte.php');
define('_NCORE_CACHE_INCLUSION_NOISETTES', _DIR_CACHE . 'noisettes_inclusion.php');
define('_NCORE_NOISETTE_CACHE_MD5', _DIR_CACHE . 'noisettes_md5.php');
define('_NCORE_NOISETTE_CACHE_DESCRIPTION', _DIR_CACHE . 'noisettes_description.php');


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
 * @param string	$service
 *      Le service permet de distinguer l'appelant qui peut-être un plugin comme le noiZetier ou
 *      un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 *      La fonction utilisera les fonctions de lecture des md5 et de stockage des descriptions de noisettes
 * 		spécifiques au service.
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
	$retour = false;

	// Initialiser le contexte de rechargement
	// -- TODO : en attente de voir si on rajoute un var_mode=recalcul_noisettes
	$options['recharger'] = $recharger;

	// On recherche les noisettes directement par leur fichier YAML de configuration car il est obligatoire
	// -- la recherche s'effectue dans
	if ($fichiers = find_all_in_path($dossier, '.+[.]yaml$')) {
		// On charge l'API du service appelant
		include_spip("ncore/${service}");

		// Initialisation des tableaux de noisettes
		$noisettes_nouvelles = $noisettes_modifiees = $noisettes_obsoletes = array();

		// Récupération des signatures md5 des noisettes déjà enregistrées.
		// Si on force le rechargement il est inutile de gérer les signatures et les noisettes modifiées ou obsolètes.
		$signatures = array();
		if (!$options['recharger']) {
			$lire_md5 = "${service}_noisette_lire_signature";
			$signatures = $lire_md5();
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

		// Mise à jour du stockage des noisettes:
		// -- Suppression des noisettes obsolètes ou de toutes les noisettes si on est en mode rechargement forcé.
		//    Pour permettre une optimisation du traitement en mode rechargement forcé on passe toujours le mode.
		// -- Update des pages modifiées
		// -- Insertion des nouvelles pages
		$noisettes = array('nouvelles' => $noisettes_nouvelles);
		if (!$options['recharger']) {
			$noisettes['obsoletes'] = $noisettes_obsoletes;
			$noisettes['modifiees'] = $noisettes_modifiees;
		}
		$stocker = "${service}_noisette_stocker_description";
		$retour = $stocker($noisettes, $options['recharger']);
	}

	return $retour;
}


/**
 * Retourne la configuration de la noisette demandée.
 * La configuration est stockée en base de données, certains champs sont recalculés avant d'être fournis.
 *
 * @package SPIP\NOIZETIER\API\NOISETTE
 * @api
 * @filtre
 *
 * @param string	$noisette
 * 		Identifiant de la $noisette.
 * @param boolean	$traitement_typo
 *      Indique si les données textuelles doivent être retournées brutes ou si elles doivent être traitées
 *      en utilisant la fonction _T_ou_typo.
 * 		Les champs sérialisés sont toujours désérialisés.
 *
 * @return array
 */
function ncore_noisette_informer($noisette, $traitement_typo = true) {

	static $description_noisette = array();

	if (!isset($description_noisette[$traitement_typo][$noisette])) {
		// Chargement de toute la configuration de la noisette en base de données.
		$description = sql_fetsel('*', 'spip_noizetier_noisettes', array('noisette=' . sql_quote($noisette)));

		// Sauvegarde de la description de la page pour une consultation ultérieure dans le même hit.
		if ($description) {
			// Traitements des champs textuels
			if ($traitement_typo) {
				$description['nom'] = _T_ou_typo($description['nom']);
				if (isset($description['description'])) {
					$description['description'] = _T_ou_typo($description['description']);
				}
			}
			// Traitements des champs tableaux sérialisés
			$description['contexte'] = unserialize($description['contexte']);
			$description['necessite'] = unserialize($description['necessite']);
			$description['parametres'] = unserialize($description['parametres']);
			// Stockage de la description
			$description_noisette[$traitement_typo][$noisette] = $description;
		} else {
			$description_noisette[$traitement_typo][$noisette] = array();
		}
	}

	return $description_noisette[$traitement_typo][$noisette];
}


function ncore_noisette_ajax($noisette) {
	static $est_ajax = array();

	if (!isset($est_ajax[$noisette])) {
		// On détermine l'existence et le contenu du cache.
		if (lire_fichier_securise(_CACHE_AJAX_NOISETTES, $contenu)) {
			$est_ajax = unserialize($contenu);
		}

		// On doit recalculer le cache.
		if (!$est_ajax
		or (_request('var_mode') == 'recalcul')
		or (defined('_NO_CACHE') and (_NO_CACHE != 0))) {
			// On détermine la valeur par défaut de l'ajax des noisettes
			include_spip('inc/config');
			$defaut_ajax = lire_config('noizetier/ajax_noisette') == 'on' ? true : false;

			// On repertorie toutes les noisettes disponibles et on compare
			// avec la valeur par défaut configurée pour le noiZetier.
			if ($noisettes = sql_allfetsel('noisette, ajax', 'spip_noizetier_noisettes')) {
				$noisettes = array_column($noisettes, 'ajax', 'noisette');
				foreach ($noisettes as $_noisette => $_ajax) {
					$est_ajax[$_noisette] = ($_ajax == 'defaut')
						? $defaut_ajax
						: ($_ajax == 'non' ? false : true);
				}
			}

			// On vérifie que la noisette demandée est bien dans la liste.
			// Si non, on la rajoute en utilisant la valeur ajax par défaut afin de toujours renvoyer
			// quelque chose.
			if (!isset($est_ajax[$noisette])) {
				$est_ajax[$noisette] = $defaut_ajax;
			}

			// On met à jour in fine le cache
			if ($est_ajax) {
				ecrire_fichier_securise(_CACHE_AJAX_NOISETTES, serialize($est_ajax));
			}
		}
	}

	return $est_ajax[$noisette];
}


function ncore_noisette_dynamique($noisette) {
	static $est_dynamique = array();

	if (!isset($est_dynamique[$noisette])) {
		// On détermine l'existence et le contenu du cache.
		if (lire_fichier_securise(_CACHE_INCLUSIONS_NOISETTES, $contenu)) {
			$est_dynamique = unserialize($contenu);
		}

		// On doit recalculer le cache.
		if (!$est_dynamique
		or (_request('var_mode') == 'recalcul')
		or (defined('_NO_CACHE') and (_NO_CACHE != 0))) {
			// On repertorie toutes les types de noisettes disponibles et on compare la valeur
			// du champ inclusion.
			if ($noisettes = sql_allfetsel('noisette, inclusion', 'spip_noizetier_noisettes')) {
				$noisettes = array_column($noisettes, 'inclusion', 'noisette');
				foreach ($noisettes as $_noisette => $_inclusion) {
					$est_dynamique[$_noisette] = ($_inclusion == 'dynamique') ? true : false;
				}
			}

			// On vérifie que la noisette demandée est bien dans la liste.
			// Si non, on la rajoute en utilisant en positionnant l'inclusion dynamique à false.
			if (!isset($est_dynamique[$noisette])) {
				$est_dynamique[$noisette] = false;
			}

			// On met à jour in fine le cache
			if ($est_dynamique) {
				ecrire_fichier_securise(_CACHE_INCLUSIONS_NOISETTES, serialize($est_dynamique));
			}
		}
	}

	return $est_dynamique[$noisette];
}


function ncore_noisette_contexte($noisette) {
	static $contexte = array();

	if (!isset($contexte[$noisette])) {
		// On détermine l'existence et le contenu du cache.
		if (lire_fichier_securise(_CACHE_CONTEXTE_NOISETTES, $contenu)) {
			$contexte = unserialize($contenu);
		}

		// On doit recalculer le cache.
		if (!$contexte
		or (_request('var_mode') == 'recalcul')
		or (defined('_NO_CACHE') and (_NO_CACHE != 0))) {
			// On repertorie toutes les types de noisettes disponibles et on compare la valeur
			// du champ inclusion.
			if ($noisettes = sql_allfetsel('noisette, contexte', 'spip_noizetier_noisettes')) {
				$noisettes = array_column($noisettes, 'contexte', 'noisette');
				$contexte = array_map('unserialize', $noisettes);
			}

			// On vérifie que la noisette demandée est bien dans la liste.
			// Si non, on la rajoute en utilisant en positionnant le contexte à tableau vide.
			if (!isset($contexte[$noisette])) {
				$contexte[$noisette] = array();
			}

			// On met à jour in fine le cache
			if ($contexte) {
				ecrire_fichier_securise(_CACHE_CONTEXTE_NOISETTES, serialize($contexte));
			}
		}
	}

	return $contexte[$noisette];
}
