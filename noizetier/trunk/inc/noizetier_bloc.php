<?php
/**
 * Ce fichier contient l'API de gestion des blocs Z configurables par le noiZetier.
 *
 * @package SPIP\NOIZETIER\BLOC\API
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * La liste des blocs par defaut d'une page peut etre modifiee via le pipeline noizetier_blocs_defaut.
 *
 * @api
 *
 * @return array
 */
function noizetier_bloc_defaut() {

	static $blocs_defaut = null;

	if (is_null($blocs_defaut)) {
		if (defined('_DIR_PLUGIN_ZCORE') and !empty($GLOBALS['z_blocs'])) {
			$blocs_defaut = $GLOBALS['z_blocs'];
		}
		else {
			$blocs_defaut = array('contenu', 'navigation', 'extra');
		}

		// Changer la liste au travers du pipeline. A priori le but est de supprimer
		// certains blocs comme head ou head_js si on ne veut pas les configurer.
		$blocs_defaut = pipeline('noizetier_blocs_defaut', $blocs_defaut);
	}

	return $blocs_defaut;
}

/**
 * Retourne la description complète d'un bloc, de tous les blocs ou une information
 * particulière d'un bloc donné.
 * La description complète de chaque bloc est inscrite dans un fichier YAML nommé bloc.yaml
 * stocké dans le dossier du bloc concerné.
 *
 * @api
 *
 * @param string $bloc
 * @param string $information
 *
 * @return array|string
 */
function noizetier_bloc_informer($bloc = '', $information = '') {

	static $blocs = null;
	static $description_bloc = array();

	if ((!$bloc and is_null($blocs))
	or ($bloc and !isset($description_bloc[$bloc]))) {
		// On détermine les blocs pour lesquels on retourne la description
		$liste_blocs = $bloc ? array($bloc) : noizetier_bloc_defaut();

		foreach ($liste_blocs as $_bloc) {
			if ($fichier = find_in_path("${_bloc}/bloc.yaml")) {
				// Il y a un fichier YAML de configuration dans le répertoire du bloc
				include_spip('inc/yaml');
				if ($description = yaml_charger_inclusions(yaml_decode_file($fichier))) {
					$description['nom'] = isset($description['nom']) ? _T_ou_typo($description['nom']) : ucfirst($bloc);
					if (isset($description['description'])) {
						$description['description'] = _T_ou_typo($description['description']);
					}
					if (!isset($description['icon'])) {
						$description['icon'] = 'bloc-24.png';
					}
				}
			} elseif (!defined('_DIR_PLUGIN_ZCORE') and in_array($_bloc, array('contenu', 'navigation', 'extra'))) {
				// Avec Zpip v1, les blocs sont toujours les mêmes : on en donne une description standard.
				$description = array(
					'nom' => _T("noizetier:nom_bloc_${_bloc}"),
					'description' => _T("noizetier:description_bloc_${_bloc}"),
					'icon' => "bloc-${_bloc}-24.png",
				);
			} else {
				// Aucune description, on renvoie juste le nom qui coincide avec l'identifiant du bloc
				$description = array('nom' => ucfirst($_bloc));
			}
			// Sauvegarde de la description du bloc pour une consultation ultérieure dans le même hit.
			if ($bloc) {
				$description_bloc[$_bloc] = $description;
			} else {
				$blocs[$_bloc] = $description;
			}
		}
	}

	if ($bloc) {
		if (!$information) {
			return $description_bloc[$bloc];
		} else {
			return isset($description_bloc[$bloc][$information])
				? $description_bloc[$bloc][$information]
				: '';
		}
	} else {
		return $blocs;
	}
}

/**
 *
 * @filtre
 *
 * @param       $page
 * @param array $blocs_exclus
 *
 * @return array
 */
function noizetier_bloc_lister($page, $blocs_exclus = array()) {

	// Initialisation des blocs avec la liste des blocs par défaut
	$blocs = noizetier_bloc_defaut();

	// Si la liste des blocs exclus n'a pas été passé en argument on les cherche dans la configuration
	// de la page
	if (!$blocs_exclus) {
		$where = array('page=' . sql_quote($page));
		$blocs_exclus = sql_getfetsel('blocs_exclus', 'spip_noizetier_pages', $where);
		$blocs_exclus = unserialize($blocs_exclus);
	}

	if ($blocs_exclus) {
		$blocs = array_diff($blocs, $blocs_exclus);
		sort($blocs);
	}

	return $blocs;
}

/**
 * Renvoie le nombre de noisettes de chaque bloc configurables d'une page, d'une composition
 * ou d'un objet.
 *
 * @api
 * @filtre
 *
 * @param string $page_ou_objet
 * 		L'identifiant de la page, de la composition ou de l'objet au format:
 * 		- pour une page : type
 * 		- pour une composition : type-composition
 * 		- pour un objet : type_objet-id_objet
 *
 * @return array
 */
function noizetier_bloc_compter_noisettes($page_ou_objet) {

	static $blocs_compteur = array();

	if (!isset($blocs_compteur[$page_ou_objet])) {
		// Initialisation des compteurs par bloc
		$nb_noisettes = array();

		// Le nombre de noisettes par bloc doit être calculé par une lecture de la table spip_noisettes.
		$from = array('spip_noisettes');
		$select = array('bloc', "count(type_noisette) as 'noisettes'");
		// -- Contruction du where identifiant précisément la page ou l'objet concerné
		$identifiants = explode('-', $page_ou_objet);
		if (isset($identifiants[1]) and ($id = intval($identifiants[1]))) {
			// L'identifiant est celui d'un objet
			$where = array('objet=' . sql_quote($identifiants[0]), 'id_objet=' . $id);
		} else {
			if (!isset($identifiants[1])) {
				// L'identifiant est celui d'une page
				$identifiants[1] = '';
			}
			$where = array('type=' . sql_quote($identifiants[0]), 'composition=' . sql_quote($identifiants[1]));
		}
		$group = array('bloc');
		$compteurs = sql_allfetsel($select, $from, $where, $group);
		if ($compteurs) {
			// On formate le tableau [bloc] = nb noisettes
			foreach ($compteurs as $_compteur) {
				$nb_noisettes[$_compteur['bloc']] = $_compteur['noisettes'];
			}
		}
		$blocs_compteur[$page_ou_objet] = $nb_noisettes;
	}

	return (isset($blocs_compteur[$page_ou_objet]) ? $blocs_compteur[$page_ou_objet] : array());
}
