<?php
/**
 * Ce fichier contient l'API N-Core de gestion des noisettes, c'est-à-dire les instances paramétrées
 * de types de noisette affectées à un conteneur.
 *
 * @package SPIP\NCORE\NOISETTE\API
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Ajoute dans un conteneur, à un rang donné ou en dernier rang, une noisette d'un type donné.
 * La fonction met à jour les rangs des autres noisettes si nécessaire.
 *
 * @api
 * @uses type_noisette_lire()
 * @uses ncore_noisette_completer()
 * @uses ncore_noisette_lister()
 * @uses ncore_noisette_stocker()
 * @uses ncore_noisette_ranger()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string $type_noisette
 *        Identifiant du type de noisette à ajouter au squelette.
 * @param array  $conteneur
 *        Tableau associatif descriptif du conteneur accueillant la noisette. Un conteneur peut-être un squelette seul
 *        ou associé à un contexte d'utilisation et dans ce cas il possède un index `squelette` ou un objet quelconque
 *        sans lien avec un squelette. Dans tous les cas, les index, à l'exception de `squelette`, sont spécifiques
 *        à l'utilisation qui en est faite par le plugin.
 * @param int    $rang
 *        Rang dans le squelette contextualisé où insérer la noisette. Si l'argument n'est pas fourni ou est égal à 0
 *        on insère la noisette en fin de bloc.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin
 *        ni celui de N-Core ne seront utilisés. En général, cet identifiant est le préfixe d'un plugin
 *        fournissant le service de stockage souhaité.
 *
 * @return int|string|bool
 *        Retourne l'identifiant de la nouvelle instance de noisette créée ou `false` en cas d'erreur.
 **/
function noisette_ajouter($plugin, $type_noisette, $conteneur, $rang = 0, $stockage = '') {

	// Initialisation de la valeur de sortie.
	$noisette_ajoutee = false;

	if ($type_noisette) {
		// On récupère les paramètres du type de noisette.
		include_spip('inc/ncore_type_noisette');
		$champs = type_noisette_lire(
			$plugin,
			$type_noisette,
			'parametres',
			false,
			$stockage
		);

		// Et on leur associe des valeurs par défaut.
		include_spip('inc/saisies');
		$parametres = saisies_lister_valeurs_defaut($champs);

		// On charge les services de N-Core.
		// Ce sont ces fonctions qui aiguillent ou pas vers un service spécifique du plugin.
		include_spip('ncore/ncore');

		// On initialise la description de la noisette à ajouter et en particulier on stocke l'id du conteneur
		// pour simplifier les traitements par la suite.
		$description = array(
			'plugin'        => $plugin,
			'type_noisette' => $type_noisette,
			'conteneur'     => serialize($conteneur),
			'id_conteneur'  => ncore_conteneur_identifier($plugin, $conteneur, $stockage),
			'rang_noisette' => intval($rang),
			'est_conteneur' => type_noisette_lire($plugin, $type_noisette, 'conteneur', false, $stockage),
			'parametres'    => serialize($parametres),
			'balise'        => 'defaut',
			'css'           => ''
		);

		// Mise à jour de la description pour les noisettes conteneur:
		// -- pas de div englobante.
		if ($description['est_conteneur'] == 'oui') {
			$description['balise'] = 'non';
		}

		// Complément à la description par défaut, spécifique au plugin utilisateur, si nécessaire.
		$description = ncore_noisette_completer($plugin, $description, $stockage);

		// On récupère les noisettes déjà affectées au conteneur sous la forme d'un tableau indexé
		// par le rang de chaque noisette.
		$noisettes = ncore_noisette_lister($plugin, $conteneur, '', 'rang_noisette', $stockage);

		// On calcule le rang max déjà utilisé.
		$rang_max = $noisettes ? max(array_keys($noisettes)) : 0;

		if (!$rang or ($rang and ($rang > $rang_max))) {
			// Si, le rang est nul ou si il est strictement supérieur au rang_max, on positionne la noisette
			// à ajouter au rang max + 1.
			// En effet, si le rang est supérieur au rang max c'est que la nouvelle noisette est ajoutée
			// après les noisettes existantes, donc cela revient à insérer la noisette en fin de liste.
			// Postionner le rang à max + 1 permet d'éviter d'avoir des trous dans la liste des rangs.
			$description['rang_noisette'] = $rang_max + 1;
		} else {
			// Si le rang est non nul et inférieur ou égal au rang max c'est qu'on insère la noisette dans la liste
			// existante : il faut décaler d'un rang les noisettes de rang supérieur ou égal si elle existent pour
			// libérer la position de la nouvelle noisette.
			if ($rang <= $rang_max) {
				krsort($noisettes);
				foreach ($noisettes as $_rang => $_description) {
					if ($_rang >= $rang) {
						ncore_noisette_ranger($plugin, $_description, $_rang + 1, $stockage);
					}
				}
			}
		}

		// La description de la nouvelle noisette est prête à être stockée à sa position.
		$noisette_ajoutee = ncore_noisette_stocker($plugin, $description, $stockage);
	}

	return $noisette_ajoutee;
}

/**
 * Met à jour les paramètres éditables d'une noisette donnée.
 * La fonction contrôle la liste des champs modifiables.
 *
 * @api
 * @uses ncore_noisette_decrire()
 * @uses ncore_noisette_stocker()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param mixed  $noisette
 *        Identifiant de la noisette qui peut prendre soit la forme d'un entier ou d'une chaine unique, soit la forme
 *        d'un couple (id conteneur, rang).
 * @param array  $modifications
 *        Tableau des couples (champ, valeur) à mettre à jour pour la noisette spécifiée.
 *        La fonction contrôle la liste des champs éditables en filtrant les champs standard comme `parametres`,
 *        `balise`, `css` et éventuellement ceux spécifiquement définis par le plugin utilisateur dans
 *        l'argument $editables_specifiques.
 * @param array  $editables_specifiques
 *        Liste de champs éditables spécifiques au plugin utilisateur ou tableau vide sinon.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin
 *        ni celui de N-Core ne seront utilisés. En général, cet identifiant est le préfixe d'un plugin
 *        fournissant le service de stockage souhaité.
 *
 * @return bool
 */
function noisette_parametrer($plugin, $noisette, $modifications, $editables_specifiques = array(), $stockage = '') {

	// Initialisation du retour
	$retour = false;

	// On charge les services de N-Core.
	// Ce sont ces fonctions qui aiguillent ou pas vers un service spécifique du plugin.
	include_spip('ncore/ncore');

	// L'identifiant d'une noisette peut être fourni de deux façons :
	// - par une valeur unique, celle créée lors de l'ajout et qui peut-être un entier (id d'une table SPIP) ou
	//   une chaine unique par exemple générée par uniqid().
	// - ou par un tableau à deux entrées fournissant le conteneur et le rang dans le conteneur
	//   (qui est unique pour un conteneur donné).
	if (!empty($noisette) and (is_string($noisette) or is_numeric($noisette) or is_array($noisette))) {
		// On récupère la description complète de la noisette avant de modifier les champs éditables spécifiés.
		$description = ncore_noisette_decrire($plugin, $noisette, $stockage);

		// On contrôle les champs éditables et on met à jour la description de la noisette.
		$parametres = array_merge(array('parametres', 'balise', 'css'), $editables_specifiques);
		$modifications = array_intersect_key($modifications, array_flip($parametres));
		$description = array_merge($description, $modifications);

		// La description est prête à être stockée en remplacement de l'existante.
		if ($id_noisette = ncore_noisette_stocker($plugin, $description, $stockage)) {
			$retour = true;
		}
	}

	return $retour;
}

/**
 * Supprime une noisette donnée du conteneur auquel elle est associée et, si cette noisette est un conteneur,
 * le vide de ses noisettes au préalable.
 * La fonction met à jour les rangs des autres noisettes si nécessaire.
 *
 * @api
 * @uses ncore_noisette_decrire()
 * @uses ncore_noisette_destocker()
 * @uses ncore_noisette_lister()
 * @uses ncore_noisette_ranger()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param mixed  $noisette
 *        Identifiant de la noisette qui peut prendre soit la forme d'un entier ou d'une chaine unique, soit la forme
 *        d'un couple (id conteneur, rang).
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin
 *        ni celui de N-Core ne seront utilisés. En général, cet identifiant est le préfixe d'un plugin
 *        fournissant le service de stockage souhaité.
 *
 * @return bool
 */
function noisette_supprimer($plugin, $noisette, $stockage = '') {

	// Initialisation du retour
	$retour = false;

	// On charge les services de N-Core.
	// Ce sont ces fonctions qui aiguillent ou pas vers un service spécifique du plugin.
	include_spip('ncore/ncore');

	// L'identifiant d'une noisette peut être fourni de deux façons :
	// - par une valeur unique, celle créée lors de l'ajout et qui peut-être un entier (id d'une table SPIP) ou
	//   une chaine unique par exemple générée par uniqid().
	// - ou par un tableau à deux entrées fournissant le conteneur et le rang dans le conteneur
	//   (qui est unique pour un conteneur donné).
	if (!empty($noisette) and (is_string($noisette) or is_numeric($noisette) or is_array($noisette))) {
		// Avant de supprimer la noisette on sauvegarde sa description.
		// Cela permet de conserver le rang et l'id du conteneur indépendamment de l'identifiant
		// utilisé pour spécifier la noisette.
		$description = ncore_noisette_decrire($plugin, $noisette, $stockage);

		// Si la noisette est de type conteneur, il faut d'abord vider le conteneur qu'elle représente
		// et ce de façon récursive. La récursivité est gérée par la fonction de service ncore_conteneur_destocker().
		if ($description['est_conteneur'] == 'oui') {
			// Inutile de redéfinir un conteneur car la description de la noisette contient les deux champs
			// essentiels, à savoir, type_noisette et id_noisette.
			ncore_conteneur_destocker($plugin, $description, $stockage);
		}
		// Suppression de la noisette. On passe la description complète ce qui permet à la fonction de
		// destockage de choisir la méthode d'identification la plus adaptée.
		$retour = ncore_noisette_destocker($plugin, $description, $stockage);

		// On récupère les noisettes restant affectées au conteneur sous la forme d'un tableau indexé par rang.
		$autres_noisettes = ncore_noisette_lister(
			$plugin,
			$description['id_conteneur'],
			'',
			'rang_noisette',
			$stockage
		);

		// Si il reste des noisettes, on tasse d'un rang les noisettes qui suivaient la noisette supprimée.
		if ($autres_noisettes) {
			// On lit les noisettes restantes dans l'ordre décroissant pour éviter d'écraser une noisette.
			ksort($autres_noisettes);
			foreach ($autres_noisettes as $_rang => $_autre_description) {
				if ($_rang > $description['rang_noisette']) {
					ncore_noisette_ranger($plugin, $_autre_description, $_autre_description['rang_noisette'] - 1, $stockage);
				}
			}
		}
	}

	return $retour;
}

/**
 * Retourne, pour une noisette donnée, la description complète ou seulement un champ précis.
 * Les champs textuels peuvent subir un traitement typo si demandé.
 *
 * @api
 * @uses ncore_noisette_decrire()
 *
 * @param string  $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param mixed   $noisette
 *        Identifiant de la noisette qui peut prendre soit la forme d'un entier ou d'une chaine unique, soit la forme
 *        d'un couple (id conteneur, rang).
 * @param string  $information
 *        Information spécifique à retourner ou vide pour retourner toute la description.
 * @param boolean $traiter_typo
 *        Indique si les données textuelles doivent être retournées brutes ou si elles doivent être traitées
 *        en utilisant la fonction _T_ou_typo. Par défaut l'indicateur vaut `false`.
 *        Les champs sérialisés sont eux toujours désérialisés.
 *        Pour l'instant il n'y a pas de champ textuel directement associé à une noisette.
 * @param string  $stockage
 *        Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin
 *        ni celui de N-Core ne seront utilisés. En général, cet identifiant est le préfixe d'un plugin
 *        fournissant le service de stockage souhaité.
 *
 * @return bool
 */
function noisette_lire($plugin, $noisette, $information = '', $traiter_typo = false, $stockage = '') {

	// On indexe le tableau des descriptions par le plugin appelant en cas d'appel sur le même hit
	// par deux plugins différents.
	// En outre, on gère un tableau par type d'identification, id noisette ou couple (id conteneur, rang).
	static $description_noisette_par_id = array();
	static $description_noisette_par_rang = array();

	// Initialisation de la description en sortie.
	$retour = array();

	if (!empty($noisette) and (is_string($noisette) or is_numeric($noisette) or is_array($noisette))) {
		// On charge les services de N-Core.
		// Ce sont ces fonctions qui aiguillent ou pas vers un service spécifique du plugin.
		include_spip('ncore/ncore');

		// On vérifie si la noisette est valide et si la description n'a pas déjà été enregistrée dans le tableau adéquat.
		$description_existe = false;
		$noisette_invalide = false;
		if (!is_array($noisette)) {
			$description_existe = isset($description_noisette_par_id[$plugin][$noisette]) ? true : false;
		} elseif (isset($noisette['id_conteneur'], $noisette['rang_noisette'])) {
			$description_existe = isset($description_noisette_par_rang[$plugin][$noisette['id_conteneur']][$noisette['rang_noisette']])
				? true
				: false;
		} else {
			$noisette_invalide = true;
		}

		if (!$noisette_invalide) {
			if (!$description_existe) {
				// Lecture de toute la configuration de la noisette: les données retournées sont brutes.
				$description = ncore_noisette_decrire($plugin, $noisette, $stockage);

				// Traitements des champs tableaux sérialisés si nécessaire
				if ($description) {
					if (isset($description['parametres']) and is_string($description['parametres'])) {
						$description['parametres'] = unserialize($description['parametres']);
					}
					if (isset($description['conteneur']) and is_string($description['conteneur'])) {
						$description['conteneur'] = unserialize($description['conteneur']);
					}
				}

				// Sauvegarde de la description de la noisette pour une consultation ultérieure dans le même hit
				// en suivant le type d'identification.
				if (!is_array($noisette)) {
					$description_noisette_par_id[$plugin][$noisette] = $description;
				} else {
					$description_noisette_par_rang[$plugin][$noisette['id_conteneur']][$noisette['rang_noisette']] = $description;
				}
			}

			if ($information) {
				if ((!is_array($noisette) and isset($description_noisette_par_id[$plugin][$noisette][$information]))
					or (is_array($noisette)
						and isset($description_noisette_par_rang[$plugin][$noisette['id_conteneur']][$noisette['rang_noisette']][$information]))) {
					$retour = is_array($noisette)
						? $description_noisette_par_rang[$plugin][$noisette['id_conteneur']][$noisette['rang_noisette']][$information]
						: $description_noisette_par_id[$plugin][$noisette][$information];
				} else {
					$retour = '';
				}
			} else {
				$retour = is_array($noisette)
					? $description_noisette_par_rang[$plugin][$noisette['id_conteneur']][$noisette['rang_noisette']]
					: $description_noisette_par_id[$plugin][$noisette];
			}
		}
	}

	return $retour;
}

/**
 * Déplace une noisette donnée au sein d’un conteneur.
 * La fonction met à jour les rangs des autres noisettes si nécessaire.
 *
 * @api
 * @uses ncore_noisette_decrire()
 * @uses ncore_noisette_lister()
 * @uses ncore_noisette_ranger()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param mixed  $noisette
 *        Identifiant de la noisette qui peut prendre soit la forme d'un entier ou d'une chaine unique, soit la forme
 *        d'un couple (id conteneur, rang).
 * @param int    $rang_destination
 *        Entier représentant le rang où repositionner la noisette dans le squelette contextualisé.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin
 *        ni celui de N-Core ne seront utilisés. En général, cet identifiant est le préfixe d'un plugin
 *        fournissant le service de stockage souhaité.
 *
 * @return bool
 */
function noisette_deplacer($plugin, $noisette, $rang_destination, $stockage = '') {

	// Initialisation du retour
	$retour = false;

	// On charge les services de N-Core.
	// Ce sont ces fonctions qui aiguillent ou pas vers un service spécifique du plugin.
	include_spip('ncore/ncore');

	// L'identifiant d'une noisette peut être fourni de deux façons :
	// - par une valeur unique, celle créée lors de l'ajout et qui peut-être un entier (id d'une table SPIP) ou
	//   une chaine unique par exemple générée par uniqid().
	// - ou par un tableau à deux entrées fournissant le conteneur et le rang
	//   (qui est unique pour un conteneur donné).
	if (!empty($noisette) and (is_string($noisette) or is_numeric($noisette) or is_array($noisette))) {
		// Avant de deplacer la noisette on sauvegarde sa description et son rang origine.
		$description = ncore_noisette_decrire($plugin, $noisette, $stockage);
		$rang_origine = $description['rang_noisette'];

		// Si les rangs origine et destination sont identiques on ne fait rien !
		if ($rang_destination != $rang_origine) {
			// On récupère les noisettes affectées au même conteneur sous la forme d'un tableau indexé par le rang.
			$noisettes = ncore_noisette_lister(
				$plugin,
				$description['id_conteneur'],
				'',
				'rang_noisette',
				$stockage
			);

			// On vérifie que le rang destination est bien compris entre 1 et le rang max, sinon on le force à l'une
			// des bornes.
			$rang_destination = max(1, $rang_destination);
			$rang_max = $noisettes ? max(array_keys($noisettes)) : 0;
			$rang_destination = min($rang_max, $rang_destination);

			// Suivant la position d'origine et de destination de la noisette déplacée on trie les noisettes
			// du conteneur.
			if ($rang_destination < $rang_origine) {
				krsort($noisettes);
			} else {
				ksort($noisettes);
			}

			// On déplace les noisettes impactées à l'exception de la noisette concernée par le déplacement
			// afin de créer une position libre.
			foreach ($noisettes as $_rang => $_description) {
				if ($rang_destination < $rang_origine) {
					// On "descend" les noisettes du rang destination au rang origine non compris.
					if (($_rang >= $rang_destination) and ($_rang < $rang_origine)) {
						ncore_noisette_ranger($plugin, $_description, $_rang + 1, $stockage);
					}
				} else {
					// On "remonte" les noisettes du rang destination au rang origine non compris.
					if (($_rang <= $rang_destination) and ($_rang > $rang_origine)) {
						ncore_noisette_ranger($plugin, $_description, $_rang - 1, $stockage);
					}
				}
			}

			// On positionne le rang de la noisette à déplacer au rang destination.
			// On met le rang à zéro pour indiquer que l'emplacement d'origine n'est plus occupé par la
			// noisette et qu'il ne faut pas le supprimer lors du rangement.
			$description['rang_noisette'] = 0;
			ncore_noisette_ranger($plugin, $description, $rang_destination, $stockage);
		}
	}

	return $retour;
}

/**
 * Renvoie une liste de descriptions de noisettes appartenant à un conteneur donné ou pas et éventuellement filtrée
 * sur certains champs.
 * Le tableau retourné est indexé soit par identifiant de noisette soit par identifiant du conteneur et rang.
 *
 * @api
 * @uses ncore_noisette_lister()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier
 *        ou un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array|string $conteneur
 *        Tableau descriptif du conteneur ou identifiant du conteneur ou vide si on souhaite adresser tous les
 *        conteneurs.
 * @param string $cle
 *        Champ de la description d'une noisette servant d'index du tableau. En général on utilisera soit `id_noisette`
 *        soit `rang`.
 * @param array  $filtres
 *        Tableau associatif `[champ] = valeur` de critères de filtres sur les descriptions de types de noisette.
 *        Le seul opérateur possible est l'égalité.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin
 *        ni celui de N-Core ne seront utilisés. En général, cet identifiant est le préfixe d'un plugin
 *        fournissant le service de stockage souhaité.
 *
 * @return array
 *        Tableau des descriptions des noisettes du conteneur indexé par le champ fourni en argument (par défaut le
 *        rang).
 */
function noisette_repertorier($plugin, $conteneur = array(), $cle = 'rang_noisette', $filtres = array(), $stockage = '') {

	// On indexe le tableau des noisettes par le plugin appelant en cas d'appel sur le même hit
	// par deux plugins différents.
	static $noisettes = array();

	if (!isset($noisettes[$plugin])) {
		// On charge l'API de N-Core.
		// Ce sont ces fonctions qui aiguillent ou pas vers une fonction spécifique du service.
		include_spip("ncore/ncore");

		// On récupère la description complète de tous les types de noisettes détectés par le plugin appelant
		$noisettes[$plugin] = ncore_noisette_lister($plugin, $conteneur, '', $cle, $stockage);
	}

	// Application des filtres éventuellement demandés en argument de la fonction
	$noisettes_filtrees = $noisettes[$plugin];
	if ($filtres) {
		foreach ($noisettes_filtrees as $_noisette => $_description) {
			foreach ($filtres as $_critere => $_valeur) {
				if (isset($_description[$_critere]) and ($_description[$_critere] != $_valeur)) {
					unset($noisettes_filtrees[$_noisette]);
					break;
				}
			}
		}
	}

	return $noisettes_filtrees;
}
