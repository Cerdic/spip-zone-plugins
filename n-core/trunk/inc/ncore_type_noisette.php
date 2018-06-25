<?php
/**
 * Ce fichier contient l'API N-Core de gestion des types de noisette qui consiste à stocker les descriptions
 * dans un espace à accès rapide et à permettre leur lecture et leur mise à jour.
 *
 * @package SPIP\NCORE\TYPE_NOISETTE\API
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Charge ou recharge les descriptions des types de noisette à partir des fichiers YAML.
 * Les types de noisettes sont recherchés dans un répertoire relatif fourni en argument.
 * La fonction optimise le chargement en effectuant uniquement les traitements nécessaires
 * en fonction des modifications, ajouts et suppressions des types de noisettes identifiés en comparant
 * les md5 des fichiers YAML.
 *
 * @api
 * @uses ncore_type_noisette_initialiser_dossier()
 * @uses ncore_type_noisette_lister()
 * @uses ncore_type_noisette_completer()
 * @uses ncore_type_noisette_stocker()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *        ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param bool   $recharger
 *        Si `true` force le rechargement de toutes les types de noisettes, sinon le chargement se base sur le
 *        md5 des fichiers YAML. Par défaut vaut `false`.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin
 *        ni celui de N-Core ne seront utilisés. En général, cet identifiant est le préfixe d'un plugin
 *        fournissant le service de stockage souhaité.
 *
 * @return bool
 *        `false` si une erreur s'est produite, `true` sinon.
 */
function type_noisette_charger($plugin, $recharger = false, $stockage = '') {

	// Retour de la fonction
	$retour = true;

	// Initialiser le contexte de rechargement
	// TODO : voir si on ajoute un var_mode=recalcul_types_noisette ?

	// On charge l'API de stockge de N-Core.
	// Ce sont ces fonctions qui aiguillent ou pas vers une éventuelle fonction spécifique de stockage.
	include_spip("ncore/ncore");

	// On récupère la configuration du dossier relatif où chercher les types de noisettes à charger.
	$dossier = ncore_type_noisette_initialiser_dossier($plugin);

	// On recherche les types de noisette directement par leur fichier YAML de configuration car il est
	// obligatoire. La recherche s'effectue dans le path en utilisant le dossier relatif fourni.
	if ($fichiers = find_all_in_path($dossier, '.+[.]yaml$')) {
		// On s'assure que la noisette conteneur fournie par N-Core soit bien dans la liste ce qui peut ne pas être
		// le cas si le dossier relatif des types de noisette du plugin appelant est différent de celui de N-Core.
		$dossier_ncore = ncore_type_noisette_initialiser_dossier('ncore');
		if ($dossier != $dossier_ncore) {
			$fichiers['conteneur.yaml'] = find_in_path("${dossier_ncore}conteneur.yaml");
		}

		// Initialisation des tableaux de types de noisette.
		$types_noisette_a_ajouter = $types_noisette_a_changer = $types_noisette_a_effacer = array();

		// Récupération la description complète des types de noisette déjà enregistrés de façon :
		// - à gérer l'activité des types en fin de chargement
		// - de comparer les signatures md5 des noisettes déjà enregistrées. Si on force le rechargement il est inutile
		//   de gérer les signatures et les noisettes modifiées ou obsolètes.
		$types_noisettes_existantes = ncore_type_noisette_lister($plugin, '', $stockage);
		$signatures = array();
		if (!$recharger) {
			$signatures = array_column($types_noisettes_existantes, 'signature', 'type_noisette');
			// On initialise la liste des types de noisette à supprimer avec l'ensemble des types de noisette déjà stockés.
			$types_noisette_a_effacer = $signatures ? array_keys($signatures) : array();
		}

		foreach ($fichiers as $_squelette => $_chemin) {
			$type_noisette = basename($_squelette, '.yaml');
			// Si on a forcé le rechargement ou si aucun md5 n'est encore stocké pour le type de noisette
			// on positionne la valeur du md5 stocké à chaine vide.
			// De cette façon, on force la lecture du fichier YAML du type de noisette.
			$md5_stocke = (isset($signatures[$type_noisette]) and !$recharger)
				? $signatures[$type_noisette]
				: '';

			// Initialisation de la description par défaut du type de noisette
			// -- on y inclut le plugin appelant et la signature
			$description_defaut = array(
				'type_noisette' => $type_noisette,
				'nom'           => $type_noisette,
				'description'   => '',
				'icon'          => 'noisette-24.png',
				'necessite'     => array(),
				'actif'         => 'oui',
				'conteneur'     => 'non',
				'contexte'      => array(),
				'ajax'          => 'defaut',
				'inclusion'     => 'statique',
				'parametres'    => array(),
				'plugin'        => $plugin,
				'signature'     => '',
			);

			// On vérifie que le md5 du fichier YAML est bien différent de celui stocké avant de charger
			// le contenu. Sinon, on passe au fichier suivant.
			$md5 = md5_file($_chemin);
			if ($md5 != $md5_stocke) {
				include_spip('inc/yaml');
				$description = yaml_decode_file($_chemin, array('include' => true));

				// TODO : ne faudrait-il pas "valider" le fichier YAML ici ou alors lors du stockage ?
				// Traitements des champs pouvant être soit une chaine soit un tableau
				if (!empty($description['necessite']) and is_string($description['necessite'])) {
					$description['necessite'] = array($description['necessite']);
				}
				if (!empty($description['contexte']) and is_string($description['contexte'])) {
					$description['contexte'] = array($description['contexte']);
				}

				// On repère les types de noisette qui nécessitent des plugins explicitement dans leur
				// fichier de configuration :
				// -- si un plugin nécessité est inactif, on indique le type de noisette comme inactif mais on l'inclut
				//    dans la liste retournée.
				// Rappel: si un type de noisette est incluse dans un plugin non actif elle ne sera pas détectée
				//         lors du find_all_in_path() puisque le plugin n'est pas dans le path SPIP.
				//         Ce n'est pas ce cas qui est traité ici.
				if (!empty($description['necessite'])) {
					foreach ($description['necessite'] as $_plugin_necessite) {
						if (!defined('_DIR_PLUGIN_' . strtoupper($_plugin_necessite))) {
							$description['actif'] = 'non';
							break;
						}
					}
				}

				// En mode rechargement forcé toute noisette est considérée comme nouvelle.
				// Sinon, la noisette doit être retirée de la base car un plugin qu'elle nécessite a été désactivée:
				// => il suffit pour cela de la laisser dans la liste des noisettes obsolètes.
				// Mise à jour du md5
				$description['signature'] = $md5;
				// Complétude de la description avec les valeurs par défaut
				$description = array_merge($description_defaut, $description);
				// Traitement spécifique d'un type de noisette conteneur : son contexte est toujours env et
				// l'ajax et l'inclusion dynamique ne sont pas autorisés.
				if ($description['conteneur'] == 'oui') {
					$description['contexte'] = array('env');
					$description['ajax'] = 'non';
					$description['inclusion'] = 'statique';
				}
				// Sérialisation des champs 'necessite', 'contexte' et 'parametres' qui sont des tableaux
				$description['necessite'] = serialize($description['necessite']);
				$description['contexte'] = serialize($description['contexte']);
				$description['parametres'] = serialize($description['parametres']);
				// Complément spécifique au plugin utilisateur si nécessaire
				$description = ncore_type_noisette_completer($plugin, $description, $stockage);

				if (!$md5_stocke or $recharger) {
					// Le type de noisette est soit nouveau soit on est en mode rechargement forcé:
					// => il faut le rajouter.
					$types_noisette_a_ajouter[] = $description;
				} else {
					// La description stockée a été modifiée et le mode ne force pas le rechargement:
					// => il faut mettre à jour le type de noisette.
					$types_noisette_a_changer[] = $description;
					// => et il faut donc le supprimer de la liste de types de noisette obsolètes
					$types_noisette_a_effacer = array_diff($types_noisette_a_effacer, array($type_noisette));
				}
			} else {
				// Le type de noisette n'a pas changé et n'a donc pas été rechargé:
				// => Il faut donc juste indiquer qu'il n'est pas obsolète.
				$types_noisette_a_effacer = array_diff($types_noisette_a_effacer, array($type_noisette));
			}
		}

		// On complète la liste des types de noisette à changer avec les types de noisette dont l'indicateur
		// d'activité est modifié suite à l'activation ou à la désactivation d'un plugin (le fichier YAML lui
		// n'a pas changé). Il est inutile de le faire si on recharge tout.
		// -- on cherche ces types en excluant les pages obsolètes et celles à changer qui ont déjà recalculé
		//    l'indicateur lors de la lecture du fichier YAML.
		if (!$recharger) {
			$types_noisette_exclus = $types_noisette_a_changer
				? array_merge(array_column($types_noisette_a_changer, 'type_noisette'), $types_noisette_a_effacer)
				: $types_noisette_a_effacer;
			$types_noisette_a_verifier = $types_noisette_exclus
				? array_diff_key($types_noisettes_existantes, array_flip($types_noisette_exclus))
				: $types_noisettes_existantes;

			if ($types_noisette_a_verifier) {
				foreach ($types_noisette_a_verifier as $_type => $_description) {
					$actif = 'oui';
					$plugins_necessites = unserialize($_description['necessite']);
					if ($plugins_necessites) {
						foreach ($plugins_necessites as $_plugin_necessite) {
							if (!defined('_DIR_PLUGIN_' . strtoupper($_plugin_necessite))) {
								$actif = 'non';
								break;
							}
						}
					}
					if ($actif != $_description['actif']) {
						// On stocke la mise à jour dans les types à changer.
						$_description['actif'] = $actif;
						$types_noisette_a_changer[] = $_description;
					}

				}
			}
		}

		// Mise à jour du stockage des types de noisette si au moins un des 3 tableaux est non vide et que le chargement forcé
		// n'est pas demandé:
		// -- Suppression des types de noisettes obsolètes ou de tous les types de noisettes si on est en mode rechargement forcé.
		//    Pour permettre une optimisation du traitement en mode rechargement forcé on passe toujours le mode.
		// -- Update des types de noisette modifiés.
		// -- Insertion des nouveaux types de noisette.
		if ($recharger
		or (!$recharger and ($types_noisette_a_ajouter or $types_noisette_a_effacer or $types_noisette_a_changer))) {
			$types_noisette = array('a_ajouter' => $types_noisette_a_ajouter);
			if (!$recharger) {
				$types_noisette['a_effacer'] = $types_noisette_a_effacer;
				$types_noisette['a_changer'] = $types_noisette_a_changer;
			}
			$retour = ncore_type_noisette_stocker($plugin, $types_noisette, $recharger, $stockage);
		}
	}

	return $retour;
}


/**
 * Retourne, pour un type de noisette donné, la description complète ou seulement un champ précis.
 * Les champs textuels peuvent subir une traitement typo si demandé.
 *
 * @api
 * @uses ncore_type_noisette_decrire()
 *
 * @param string  $plugin
 *        Le service permet de distinguer l'appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 *        La fonction utilisera la fonction de lecture de la description brute d'un type de noisette, spécifique
 *        au service, ou à défaut, celle fournie par N-Core.
 * @param string  $type_noisette
 *        Identifiant du type de noisette.
 * @param string  $information
 *        Information spécifique à retourner ou vide pour retourner toute la description.
 * @param boolean $traiter_typo
 *        Indique si les données textuelles doivent être retournées brutes ou si elles doivent être traitées
 *        en utilisant la fonction _T_ou_typo. Par défaut l'indicateur vaut `false`.
 *        Les champs sérialisés sont eux toujours désérialisés.
 * @param string  $stockage
 *        Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin
 *        ni celui de N-Core ne seront utilisés. En général, cet identifiant est le préfixe d'un plugin
 *        fournissant le service de stockage souhaité.
 *
 * @return array|string
 *        La description complète ou champ précis demandé pour un type de noisette donné. Les champs
 *        de type tableau sont systématiquement désérialisés et si demandé, les champs textuels peuvent être
 *        traités avec la fonction _T_ou_typo().
 */
function type_noisette_lire($plugin, $type_noisette, $information = '', $traiter_typo = false, $stockage = '') {

	// On indexe le tableau des descriptions par le plugin appelant en cas d'appel sur le même hit
	// par deux plugins différents.
	static $donnees_typo = array('nom', 'description');
	static $description_noisette = array();

	// Stocker la description de la noisette si besoin
	if (!isset($description_noisette[$plugin][$type_noisette])) {
		// On charge l'API de N-Core.
		// Ce sont ces fonctions qui aiguillent ou pas vers une fonction spécifique du service.
		include_spip("ncore/ncore");

		// Lecture de toute la configuration de la noisette: les données retournées sont brutes.
		$description = ncore_type_noisette_decrire($plugin, $type_noisette, $stockage);

		// Sauvegarde de la description de la page pour une consultation ultérieure dans le même hit.
		if ($description) {
			// Traitements des champs tableaux sérialisés
			$description['contexte'] = unserialize($description['contexte']);
			$description['necessite'] = unserialize($description['necessite']);
			$description['parametres'] = unserialize($description['parametres']);
		}

		// Stockage de la description
		$description_noisette[$plugin][$type_noisette] = $description;
	}

	if ($information) {
		if (isset($description_noisette[$plugin][$type_noisette][$information])) {
			if (in_array($information, $donnees_typo) and $traiter_typo) {
				// Traitements de la donnée textuelle
				$retour = _T_ou_typo($description_noisette[$plugin][$type_noisette][$information]);
			} else {
				$retour = $description_noisette[$plugin][$type_noisette][$information];
			}
		} else {
			$retour = '';
		}
	} else {
		$retour = $description_noisette[$plugin][$type_noisette];
		// Traitements des données textuels
		if ($traiter_typo) {
			foreach ($donnees_typo as $_champ) {
				if (isset($retour[$_champ])) {
					$retour[$_champ] = _T_ou_typo($retour[$_champ]);
				}
			}
		}
	}

	return $retour;
}

/**
 * Renvoie une liste de descriptions de types de noisette éventuellement filtrée sur certains champs.
 *
 * @api
 * @uses ncore_type_noisette_lister()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *        ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $filtres
 *        Tableau associatif `[champ] = valeur` de critères de filtres sur les descriptions de types de noisette.
 *        Le seul opérateur possible est l'égalité.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin
 *        ni celui de N-Core ne seront utilisés. En général, cet identifiant est le préfixe d'un plugin
 *        fournissant le service de stockage souhaité.
 *
 * @return array
 *        Tableau des descriptions des types de noisette trouvés indexé par le type de noisette.
 */
function type_noisette_repertorier($plugin, $filtres = array(), $stockage = '') {

	// On indexe le tableau des des types de noisette par le plugin appelant en cas d'appel sur le même hit
	// par deux plugins différents.
	static $types_noisette = array();

	if (!isset($types_noisette[$plugin])) {
		// On charge l'API de N-Core.
		// Ce sont ces fonctions qui aiguillent ou pas vers une fonction spécifique du service.
		include_spip("ncore/ncore");

		// On récupère la description complète de tous les types de noisettes détectés par le plugin appelant
		$types_noisette[$plugin] = ncore_type_noisette_lister($plugin, '', $stockage);
	}

	// Application des filtres éventuellement demandés en argument de la fonction
	$types_noisette_filtres = $types_noisette[$plugin];
	if ($filtres) {
		foreach ($types_noisette_filtres as $_type_noisette => $_description) {
			foreach ($filtres as $_critere => $_valeur) {
				if (isset($_description[$_critere]) and ($_description[$_critere] != $_valeur)) {
					unset($types_noisette_filtres[$_type_noisette]);
					break;
				}
			}
		}
	}

	return $types_noisette_filtres;
}
