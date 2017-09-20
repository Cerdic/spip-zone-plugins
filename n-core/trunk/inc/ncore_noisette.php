<?php
/**
 * Ce fichier contient l'API N-Core de gestion des noisette, c'est-à-dire les instances paramétrées
 * de types de noisette affectées à un squelette.
 *
 * @package SPIP\NCORE\API\NOISETTE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Ajoute à un squelette, à un rang donné ou en dernier rang, une noisette d'un type donné.
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
function noisette_ajouter($plugin, $type_noisette, $squelette, $rang = 0, $stockage = '') {

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
			'rang'       => intval($rang),
			'parametres' => serialize($parametres),
			'balise'     => 'defaut',
			'css'        => ''
		);

		// On charge l'API de N-Core.
		// Ce sont ces fonctions qui aiguillent ou pas vers une fonction spécifique du service.
		include_spip("ncore/ncore");

		// On récupère les noisettes déjà affectées au squelette sous la forme d'un tableau indexé par l'identifiant
		// de la noisette stocké dans l'index 'id_noisette'.
		$noisettes = ncore_noisette_lister($plugin, $squelette, '', $stockage);

		// On calcule le rang max déjà utilisé.
		$rang_max = $noisettes ? max(array_column($noisettes, 'rang')) : 0;

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
				foreach ($noisettes as $_id_noisette => $_description) {
					if ($_description['rang'] >= $rang) {
						ncore_noisette_ranger($plugin, $_description, $_description['rang'] + 1, $stockage);
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
 * Supprime un noisette donnée d’un squelette.
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
 *        d'un couple (squelette, rang).
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
		$autres_noisettes = ncore_noisette_lister($plugin, $description['squelette'], '', $stockage);

		// Si il reste des noisettes, on tasse d'un rang les noisettes qui suivaient la noisette supprimée.
		if ($autres_noisettes) {
			foreach ($autres_noisettes as $_id_noisette => $_autre_description) {
				if ($_autre_description['rang'] > $description['rang']) {
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
 *        d'un couple (squelette, rang).
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
		// Stocker la description de la noisette si besoin
		if ((!is_array($noisette) and !isset($description_noisette[$plugin][$noisette]))
		or (is_array($noisette) and isset($noisette['squelette']) and isset($noisette['rang'])
			and !isset($description_noisette[$plugin][$noisette['squelette']][$noisette['rang']]))) {
			// On charge l'API de N-Core.
			// Ce sont ces fonctions qui aiguillent ou pas vers une fonction spécifique du service.
			include_spip("ncore/ncore");

			// Lecture de toute la configuration de la noisette: les données retournées sont brutes.
			$description = ncore_noisette_decrire($plugin, $noisette, $stockage);

			// Sauvegarde de la description de la page pour une consultation ultérieure dans le même hit.
			if ($description) {
				// Traitements des champs tableaux sérialisés si nécessaire
				if (is_string($description['parametres'])) {
					$description['parametres'] = unserialize($description['parametres']);
				}
			}

			// Stockage de la description
			if (is_array($noisette)) {
				$description_noisette_par_rang[$plugin][$noisette['squelette']][$noisette['rang']] = $description;
			} else {
				$description_noisette_par_id[$plugin][$noisette] = $description;
			}
		}

		if ($information) {
			if ((!is_array($noisette) and isset($description_noisette_par_id[$plugin][$noisette][$information]))
			or (is_array($noisette)
				and isset($description_noisette_par_rang[$plugin][$noisette['squelette']][$noisette['rang']][$information]))) {
				$retour = is_array($noisette)
					? $description_noisette_par_rang[$plugin][$noisette['squelette']][$noisette['rang']][$information]
					: $description_noisette_par_id[$plugin][$noisette][$information];
			} else {
				$retour = '';
			}
		} else {
			$retour = is_array($noisette)
				? $description_noisette_par_rang[$plugin][$noisette['squelette']][$noisette['rang']]
				: $description_noisette_par_id[$plugin][$noisette];
		}
	}

	return $retour;
}

/**
 * Supprime un noisette donnée d’un squelette.
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
			// On efface le rang de la noisette à déplacer pour permettre son affectation à une autre noisette.
			ncore_noisette_ranger($plugin, $description, 0, $stockage);

			// On récupère les noisettes affectées au même squelette sous la forme d'un tableau indexé par l'identifiant
			// de la noisette stocké dans l'index 'id_noisette' et on déplace les noisettes impactées.
			$noisettes = ncore_noisette_lister($plugin, $description['squelette'], '', $stockage);

			// Suivant la position d'origine et de destination de la noisette déplacée on renumérote les noisettes
			// du squelette.
			foreach ($noisettes as $_id_noisette => $_description) {
				if ($rang_destination < $rang_origine) {
					// On "descend" les noisettes du rang destination au rang origine non compris.
					if (($_description['rang'] >= $rang_destination) and ($_description['rang'] < $rang_origine)) {
						ncore_noisette_ranger($plugin, $_description, $_description['rang'] + 1, $stockage);
					}
				} else {
					// On "remonte" les noisettes du rang destination au rang origine non compris.
					if (($_description['rang'] <= $rang_destination) and ($_description['rang'] > $rang_origine)) {
						ncore_noisette_ranger($plugin, $_description, $_description['rang'] - 1, $stockage);
					}
				}
			}

			// On positionne le rang de la noisette à déplacer au rang destination.
			ncore_noisette_ranger($plugin, $description, $rang_destination, $stockage);
		}
	}

	return $retour;
}
