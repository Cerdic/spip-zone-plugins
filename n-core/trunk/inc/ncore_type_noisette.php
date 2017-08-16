<?php
/**
 * Ce fichier contient l'API N-Core de gestion des types de noisette, c'est-à-dire les squelettes et leur YAML.
 *
 * @package SPIP\NCORE\TYPE_NOISETTE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Charge ou recharge les descriptions des types de noisette à partir des fichiers YAML.
 * Les types de noisettes (squelettes) sont recherchés dans un répertoire relatif fourni en argument.
 * La fonction optimise le chargement en effectuant uniquement les traitements nécessaires
 * en fonction des modifications, ajouts et suppressions des types de noisettes identifiés en comparant
 * les md5 des fichiers YAML.
 *
 * @api
 * @uses ncore_type_noisette_lister()
 * @uses ncore_type_noisette_stocker()
 *
 * @param string	$plugin
 *      Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *      ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string	$dossier
 * 		Chemin relatif (avec un `/` final) dans lequel chercher les fichiers YAML de description des noisettes.
 * 		Par défaut, les noisettes seront recherchées dans le dossier `noisettes/`.
 * @param bool		$recharger
 *      Si `true` force le rechargement de toutes les types de noisettes, sinon le chargement se base sur le
 * 		md5 des fichiers YAML. Par défaut vaut `false`.
 * @param string	$stockage
 *      `stockage` : impose le type de stockage à utiliser pour les descriptions d'types de noisette et
 *      les signatures des fichiers YAML. Par défaut, la fonction cherche à utiliser le stockage fourni par le
 * 		le plugin appelant et, à défaut, celui fourni par N-Core.
 *
 * @return bool
 * 		`false` si une erreur s'est produite, `true` sinon.
 */
function ncore_type_noisette_charger($plugin, $dossier = 'noisettes/', $recharger = false, $stockage = '') {

	// Retour de la fonction
	$retour = true;

	// Initialiser le contexte de rechargement
	// TODO : voir si on ajoute un var_mode=recalcul_noisettes ?

	// On recherche les types de noisette directement par leur fichier YAML de configuration car il est
	// obligatoire. La recherche s'effectue dans le path en utilisant le dossier relatif fourni.
	if ($fichiers = find_all_in_path($dossier, '.+[.]yaml$')) {
		// On charge l'API de stockge de N-Core.
		// Ce sont ces fonctions qui aiguillent ou pas vers une éventuelle fonction spécifique de stockage.
		include_spip("ncoredata/ncore");

		// Initialisation des tableaux de types de noisette.
		$types_noisette_a_ajouter = $types_noisette_a_changer = $types_noisette_a_effacer = array();

		// Récupération des signatures md5 des noisettes déjà enregistrées.
		// Si on force le rechargement il est inutile de gérer les signatures et les noisettes modifiées ou obsolètes.
		$signatures = array();
		if (!$recharger) {
			$signatures = ncore_type_noisette_lister($plugin, 'signature', $stockage);
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

			// Initialiser les composants de l'identifiant du type de noisette:
			// - type_page-type_noisette si le type de noisette est dédié uniquement à une page
			// - type_page-composition-type_noisette si le type de noisette est dédié uniquement à une composition
			// - type_noisette sinon
			$type = '';
			$composition = '';
			$identifiants = explode('-', $type_noisette);
			if (isset($identifiants[1])) {
				$type = $identifiants[0];
			}
			if (isset($identifiants[2])) {
				$composition = $identifiants[1];
			}

			// Initialisation de la description par défaut du type de noisette
			// -- on y inclut le plugin appelant et la signature
			$description_defaut = array(
				'noisette'       => $type_noisette,
				'plugin'         => $plugin,
				'type'           => $type,
				'composition'    => $composition,
				'nom'            => $type_noisette,
				'description'    => '',
				'icon'           => 'noisette-24.png',
				'necessite'      => array(),
				'contexte'       => array(),
				'ajax'           => 'defaut',
				'inclusion'      => 'statique',
				'parametres'     => array(),
				'signature'      => '',
			);

			// On vérifie que le md5 du fichier YAML est bien différent de celui stocké avant de charger
			// le contenu. Sinon, on passe au fichier suivant.
			$md5 = md5_file($_chemin);
			if ($md5 != $md5_stocke) {
				include_spip('inc/yaml');
				$description = yaml_charger_inclusions(yaml_decode_file($_chemin));

					// Traitements des champs pouvant être soit une chaine soit un tableau
				if (!empty($description['necessite']) and is_string($description['necessite'])) {
					$description['necessite'] = array($description['necessite']);
				}
				if (!empty($description['contexte']) and is_string($description['contexte'])) {
					$description['contexte'] = array($description['contexte']);
				}

				// On n'inclue ce type de noisette que si les plugins qu'il nécessite explicitement dans son
				// fichier de configuration sont bien tous activés.
				// Rappel: si un type de noisette est incluse dans un plugin non actif elle ne sera pas détectée
				//         lors du find_all_in_path() puisque le plugin n'est pas dans le path SPIP.
				//         Ce n'est pas ce cas qui est traité ici.
				$type_noisette_a_garder = true;
				if (!empty($description['necessite'])) {
					foreach ($description['necessite'] as $_plugin_necessite) {
						if (!defined('_DIR_PLUGIN_'.strtoupper($_plugin_necessite))) {
							$type_noisette_a_garder = false;
							break;
						}
					}
				}

				// Si la noisette est à garder on finalise sa description et on détermine si elle est nouvelle ou modifiée.
				// En mode rechargement forcé toute noisette est considérée comme nouvelle.
				// Sinon, la noisette doit être retirée de la base car un plugin qu'elle nécessite a été désactivée:
				// => il suffit pour cela de la laisser dans la liste des noisettes obsolètes.
				if ($type_noisette_a_garder) {
					// Mise à jour du md5
					$description['signature'] = $md5;
					// Complétude de la description avec les valeurs par défaut
					$description = array_merge($description_defaut, $description);
					// Sérialisation des champs necessite, contexte et parametres qui sont des tableaux
					$description['necessite'] = serialize($description['necessite']);
					$description['contexte'] = serialize($description['contexte']);
					$description['parametres'] = serialize($description['parametres']);

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
					// Le type de noisette ne peut plus être utilisé car un des plugins qu'il nécessite n'est plus actif.
					// => il faut le laisser dans la liste des obsolètes.
					continue;
				}
			} else {
				// Le type de noisette n'a pas changé et n'a donc pas été réchargé:
				// => Il faut donc juste indiquer qu'il n'est pas obsolète.
				$types_noisette_a_effacer = array_diff($types_noisette_a_effacer, array($type_noisette));
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
 * Retourne la description complète ou seulement une information précise pour une noisette donnée.
 * Les données textuelles peuvent subir une traitement typo si demandé.
 *
 * @api
 *
 * @param string	$plugin
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
 * 		La description complète ou l'information précise demandée pour une noisette donnée. Les champs
 * 		de type tableau sont systématiquement désérialisés et si demandé, les champs textuels peuvent être
 * 		traités avec la fonction _T_ou_typo().
 */
function ncore_type_noisette_informer($plugin, $noisette, $information = '', $traiter_typo = false, $stockage = '') {

	// On indexe le tableau des indicateurs ajax par le plugin appelant en cas d'appel sur le même hit
	// par deux plugins différents.
	static $donnees_typo = array('nom', 'description');
	static $description_noisette = array();

	// Stocker la description de la noisette si besoin
	if (!isset($description_noisette[$plugin][$noisette])) {
		// On charge l'API de N-Core.
		// Ce sont ces fonctions qui aiguillent ou pas vers une fonction spécifique du service.
		include_spip("ncoredata/ncore");

		// Lecture de toute la configuration de la noisette: les données retournées sont brutes.
		$description = ncore_type_noisette_decrire($plugin, $noisette, $stockage);

		// Sauvegarde de la description de la page pour une consultation ultérieure dans le même hit.
		if ($description) {
			// Traitements des champs tableaux sérialisés
			$description['contexte'] = unserialize($description['contexte']);
			$description['necessite'] = unserialize($description['necessite']);
			$description['parametres'] = unserialize($description['parametres']);

			// Stockage de la description
			$description_noisette[$plugin][$noisette] = $description;
		} else {
			$description_noisette[$plugin][$noisette] = array();
		}
	}

	if ($information) {
		if (isset($description_noisette[$plugin][$noisette][$information])) {
			if (in_array($information, $donnees_typo) and $traiter_typo) {
				// Traitements de la donnée textuelle
				$retour = _T_ou_typo($description_noisette[$plugin][$noisette][$information]);
			} else {
				$retour = $description_noisette[$plugin][$noisette][$information];
			}
		} else {
			$retour = '';
		}
	} else {
		$retour = $description_noisette[$plugin][$noisette];
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
