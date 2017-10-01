<?php
/**
 * Ce fichier contient l'API N-Core de gestion des noisettes, c'est-à-dire les instances paramétrées
 * de types de noisette affectées à un squelette.
 *
 * @package SPIP\NCORE\API\NOISETTE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Ajoute à un squelette contextualisé, à un rang donné ou en dernier rang, une noisette d'un type donné.
 *
 * @api
 * @uses type_noisette_lire()
 * @uses ncore_noisette_lister()
 * @uses ncore_noisette_stocker()
 * @uses ncore_noisette_ranger()
 *
 * @param string	$plugin
 *      Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *      un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string	$type_noisette
 * 		Identifiant du type de noisette à ajouter au squelette.
 * @param string	$squelette
 * 		Chemin relatif du squelette où ajouter la noisette.
 * @param array     $contexte
 * 		Tableau éventuellement vide matérialisant le contexte d'utilisation du squelette.
 * @param int		$rang
 * 		Rang dans le squelette où insérer la noisette. Si l'argument n'est pas fourni ou est égal à 0 on insère la
 *      noisette en fin de bloc.
 * @param string	$stockage
 *      Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin
 *      ni celui de N-Core ne seront utilisés. En général, cet identifiant est le préfixe d'un plugin
 * 		fournissant le service de stockage souhaité.
 *
 * @return mixed
 * 		Retourne l'identifiant de la nouvelle instance de noisette créée ou `false` en cas d'erreur.
 **/
function noisette_ajouter($plugin, $type_noisette, $squelette, $contexte, $rang = 0, $stockage = '') {

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
			$stockage);

		// Et on leur associe des valeurs par défaut.
		include_spip('inc/saisies');
		$parametres = saisies_lister_valeurs_defaut($champs);

		// On initialise la description de la noisette à ajouter
		$description = array(
			'plugin'     => $plugin,
			'noisette'   => $type_noisette,
			'squelette'  => $squelette,
			'contexte'   => serialize($contexte),
			'rang'       => intval($rang),
			'parametres' => serialize($parametres),
			'balise'     => 'defaut',
			'css'        => ''
		);

		// On charge l'API de N-Core.
		// Ce sont ces fonctions qui aiguillent ou pas vers une fonction spécifique du service.
		include_spip("ncore/ncore");

		// On récupère les noisettes déjà affectées au squelette contextualisé sous la forme d'un tableau indexé
		// par le rang de chaque noisette.
		$noisettes = ncore_noisette_lister($plugin, $squelette, $contexte, '', 'rang',  $stockage);

		// On calcule le rang max déjà utilisé.
		$rang_max = $noisettes ? max(array_keys($noisettes)) : 0;

		if (!$rang or ($rang and ($rang > $rang_max))) {
			// Si, le rang est nul ou si il est strictement supérieur au rang_max, on positionne la noisette
			// à ajouter au rang max + 1.
			// En effet, si le rang est supérieur au rang max c'est que la nouvelle noisette est ajoutée
			// après les noisettes existantes, donc cela revient à insérer la noisette en fin de liste.
			// On positionne le rang à max + 1 permet d'éviter d'avoir des trous dans la liste des rangs.
			$description['rang'] = $rang_max + 1;
		} else {
			// Si le rang est non nul c'est qu'on insère la noisette dans la liste existante.
			// Il faut décaler les noisettes de rang supérieur ou égal si elle existent.
			if ($rang <= $rang_max) {
				krsort($noisettes);
				foreach ($noisettes as $_rang => $_description) {
					if ($_rang >= $rang) {
						ncore_noisette_ranger($plugin, $_description, $_rang + 1, $stockage);
					}
				}
			}
		}

		// La description de la nouvelle noisette est prête à être stockée.
		$noisette_ajoutee = ncore_noisette_stocker($plugin, $description, $stockage);
	}

	return $noisette_ajoutee;
}

/**
 * Supprime une noisette donnée du squelette contextualisé auquel elle est associée.
 * La fonction met à jour les rangs des autres noisettes si nécessaire.
 *
 * @api
 * @uses ncore_noisette_decrire()
 * @uses ncore_noisette_destocker()
 * @uses ncore_noisette_lister()
 * @uses ncore_noisette_ranger()
 *
 * @param string	$plugin
 *      Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *      un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param mixed		$noisette
 *        Identifiant de la noisette qui peut prendre soit la forme d'un entier ou d'une chaine unique, soit la forme
 *        d'un triplet (squelette, contexte, rang).
 * @param string	$stockage
 *      Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin
 *      ni celui de N-Core ne seront utilisés. En général, cet identifiant est le préfixe d'un plugin
 * 		fournissant le service de stockage souhaité.
 *
 * @return bool
 */
function noisette_supprimer($plugin, $noisette, $stockage = '') {

	// Initialisation du retour
	$retour = false;

	// On charge l'API de N-Core.
	// Ce sont ces fonctions qui aiguillent ou pas vers une fonction spécifique du service.
	include_spip("ncore/ncore");

	// L'identifiant d'une noisette peut être fourni de deux façons :
	// - par une valeur unique, celle créée lors de l'ajout et qui peut-être un entier (id d'une table SPIP) ou
	//   une chaine unique par exemple générée par uniqid().
	// - ou par un tableau à deux entrées fournissant le squelette et le rang (qui est unique pour un squelette donné).
	if (!empty($noisette) and (is_string($noisette) or is_numeric($noisette) or is_array($noisette))) {
		// Avant de supprimer la noisette on sauvegarde sa description.
		$description = ncore_noisette_decrire($plugin, $noisette, $stockage);

		// Suppression de la noisette. On passe la description complète ce qui permet à la fonction de
		// destockage de choisir la méthode pour identifier la noisette.
		$retour = ncore_noisette_destocker($plugin, $description, $stockage);

		// On récupère les noisettes restant affectées au squelette sous la forme d'un tableau indexé par l'identifiant
		// de la noisette stocké dans l'index 'id_noisette' mais toujours trié de façon à ce que les rangs soient croissants.
		$autres_noisettes = ncore_noisette_lister($plugin, $description['squelette'], $description['contexte'], '', 'rang', $stockage);

		// Si il reste des noisettes, on tasse d'un rang les noisettes qui suivaient la noisette supprimée.
		if ($autres_noisettes) {
			ksort($autres_noisettes);
			foreach ($autres_noisettes as $_rang => $_autre_description) {
				if ($_rang > $description['rang']) {
					ncore_noisette_ranger($plugin, $_autre_description, $_autre_description['rang'] - 1, $stockage);
				}
			}
		}
	}

	return $retour;
}

/**
 * Retourne, pour une noisette donnée, la description complète ou seulement un champ précis.
 * Les champs textuels peuvent subir une traitement typo si demandé.
 *
 * @api
 * @uses ncore_noisette_decrire()
 *
 * @param string	$plugin
 *      Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *      un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param mixed		$noisette
 *        Identifiant de la noisette qui peut prendre soit la forme d'un entier ou d'une chaine unique, soit la forme
 *        d'un triplet (squelette, contexte, rang).
 * @param string	$information
 * 		Information spécifique à retourner ou vide pour retourner toute la description.
 * @param boolean	$traiter_typo
 *      Indique si les données textuelles doivent être retournées brutes ou si elles doivent être traitées
 *      en utilisant la fonction _T_ou_typo. Par défaut l'indicateur vaut `false`.
 * 		Les champs sérialisés sont eux toujours désérialisés.
 * 		Pour l'instant il n'y a pas de champ textuel directement associé à une noisette.
 * @param string	$stockage
 *      Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin
 *      ni celui de N-Core ne seront utilisés. En général, cet identifiant est le préfixe d'un plugin
 * 		fournissant le service de stockage souhaité.
 *
 * @return bool
 */
function noisette_lire($plugin, $noisette, $information = '', $traiter_typo = false, $stockage = '') {

	// On indexe le tableau des descriptions par le plugin appelant en cas d'appel sur le même hit
	// par deux plugins différents. On gère un tableau par type d'identification (id noisette ou couple squelette/rang).
	static $description_noisette_par_id = array();
	static $description_noisette_par_rang = array();

	// Initialisation de la description en sortie.
	$retour = array();

	if (!empty($noisette) and (is_string($noisette) or is_numeric($noisette) or is_array($noisette))) {
		// On charge l'API de N-Core.
		// Ce sont ces fonctions qui aiguillent ou pas vers une fonction spécifique du service.
		include_spip("ncore/ncore");

		// On vérifie si la description n'a pas déjà été enregistrée dans le tableau adéquat.
		$description_existe = isset($description_noisette_par_id[$plugin][$noisette]) ? true : false;
		if (!is_array($noisette)) {
			$description_existe = isset($description_noisette_par_id[$plugin][$noisette]) ? true : false;
		} else {
			if (isset($noisette['squelette']) and isset($noisette['contexte']) and isset($noisette['rang'])) {
				$squelette_contextualise = ncore_squelette_identifier(
					$plugin,
					$noisette['squelette'],
					$noisette['contexte'],
					$stockage);
				$description_existe = isset($description_noisette_par_rang[$plugin][$squelette_contextualise][$noisette['rang']])
					? true
					: false;
			}
		}

		if (!$description_existe) {
			// Lecture de toute la configuration de la noisette: les données retournées sont brutes.
			$description = ncore_noisette_decrire($plugin, $noisette, $stockage);

			// Traitements des champs tableaux sérialisés si nécessaire
			if ($description) {
				if (is_string($description['parametres'])) {
					$description['parametres'] = unserialize($description['parametres']);
				}
				if (is_string($description['contexte'])) {
					$description['contexte'] = unserialize($description['contexte']);
				}
			}

			// Sauvegarde de la description de la page pour une consultation ultérieure dans le même hit.
			if (is_array($noisette)) {
				$description_noisette_par_rang[$plugin][$squelette_contextualise][$noisette['rang']] = $description;
			} else {
				$description_noisette_par_id[$plugin][$noisette] = $description;
			}
		}

		if ($information) {
			if ((!is_array($noisette) and isset($description_noisette_par_id[$plugin][$noisette][$information]))
			or (is_array($noisette)
				and isset($description_noisette_par_rang[$plugin][$squelette_contextualise][$noisette['rang']][$information]))) {
				$retour = is_array($noisette)
					? $description_noisette_par_rang[$plugin][$squelette_contextualise][$noisette['rang']][$information]
					: $description_noisette_par_id[$plugin][$noisette][$information];
			} else {
				$retour = '';
			}
		} else {
			$retour = is_array($noisette)
				? $description_noisette_par_rang[$plugin][$squelette_contextualise][$noisette['rang']]
				: $description_noisette_par_id[$plugin][$noisette];
		}
	}

	return $retour;
}

/**
 * Déplace une noisette donnée au sein d’un squelette contextualisé.
 * La fonction met à jour les rangs des autres noisettes si nécessaire.
 *
 * @api
 * @uses ncore_noisette_decrire()
 * @uses ncore_noisette_lister()
 * @uses ncore_noisette_ranger()
 *
 * @param string	$plugin
 *      Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *      un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param mixed		$noisette
 *        Identifiant de la noisette qui peut prendre soit la forme d'un entier ou d'une chaine unique, soit la forme
 *        d'un couple (squelette, rang).
 * @param string	$stockage
 *      Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin
 *      ni celui de N-Core ne seront utilisés. En général, cet identifiant est le préfixe d'un plugin
 * 		fournissant le service de stockage souhaité.
 *
 * @return bool
 */
function noisette_deplacer($plugin, $noisette, $rang_destination, $stockage = '') {

	// Initialisation du retour
	$retour = false;

	// On charge l'API de N-Core.
	// Ce sont ces fonctions qui aiguillent ou pas vers une fonction spécifique du service.
	include_spip("ncore/ncore");

	// L'identifiant d'une noisette peut être fourni de deux façons :
	// - par une valeur unique, celle créée lors de l'ajout et qui peut-être un entier (id d'une table SPIP) ou
	//   une chaine unique par exemple générée par uniqid().
	// - ou par un tableau à deux entrées fournissant le squelette et le rang (qui est unique pour un squelette donné).
	if (!empty($noisette) and (is_string($noisette) or is_numeric($noisette) or is_array($noisette))) {
		// Avant de deplacer la noisette on sauvegarde sa description et son rang origine.
		$description = ncore_noisette_decrire($plugin, $noisette, $stockage);
		$rang_origine = $description['rang'];

		// Si les rangs origine et destination sont identiques on ne fait rien !
		if ($rang_destination != $rang_origine) {
			// On récupère les noisettes affectées au même squelette sous la forme d'un tableau indexé par le rang
			// et on déplace les noisettes impactées.
			$noisettes = ncore_noisette_lister($plugin, $description['squelette'], $description['contexte'], '', 'rang', $stockage);

			// On vérifie que le rang destination soit bien compris entre 1 et le rang max, sinon on le force à une
			// des bornes.
			$rang_destination = max(1, $rang_destination);
			$rang_max = $noisettes ? max(array_keys($noisettes)) : 0;
			$rang_destination = min($rang_max, $rang_destination);

			// Suivant la position d'origine et de destination de la noisette déplacée on trie les noisettes
			// du squelette.
			if ($rang_destination < $rang_origine) {
				krsort($noisettes);
			} else {
				ksort($noisettes);
			}

			// On déplace les noisettes impactées.
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
			ncore_noisette_ranger($plugin, $description, $rang_destination, $stockage);
		}
	}

	return $retour;
}


/**
 * Supprime toutes les noisettes d’un squelette contextualisé.
 *
 * @api
 * @uses ncore_noisette_destocker()
 *
 * @param string	$plugin
 *      Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *      un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param mixed		$squelette
 * 		Chemin relatif du squelette où ajouter la noisette.
 * @param array     $contexte
 * 		Tableau éventuellement vide matérialisant le contexte d'utilisation du squelette.
 * @param string	$stockage
 *      Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin
 *      ni celui de N-Core ne seront utilisés. En général, cet identifiant est le préfixe d'un plugin
 * 		fournissant le service de stockage souhaité.
 *
 * @return bool
 */
function noisette_vider($plugin, $squelette, $contexte, $stockage = '') {

	// Initialisation du retour
	$retour = false;

	// On charge l'API de N-Core.
	// Ce sont ces fonctions qui aiguillent ou pas vers une fonction spécifique du service.
	include_spip("ncore/ncore");

	if ($squelette) {
		// On construit un tableau avec le squelette et son contexte et on le passe à la fonction.
		$description = array('squelette' => $squelette, 'contexte' => serialize($contexte));
		$retour = ncore_noisette_destocker($plugin, $description, $stockage);
	}

	return $retour;
}
