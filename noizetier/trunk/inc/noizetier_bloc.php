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
 * Renvoie la liste par défaut des identifiants des blocs d'une page.
 * Cette liste peut être modifiée via le pipeline noizetier_blocs_defaut, en particulier pour
 * supprimer certains blocs pour l'ensemble des pages et objets.
 *
 * @api
 *
 * @return array
 */
function noizetier_bloc_lister_defaut() {

	// Stocker la liste des blocs par défaut pour éviter le recalcul sur le même hit.
	static $blocs_defaut = null;

	if (is_null($blocs_defaut)) {
		if (defined('_DIR_PLUGIN_ZCORE') and !empty($GLOBALS['z_blocs'])) {
			// Z en version v2
			$blocs_defaut = $GLOBALS['z_blocs'];
		} else {
			// Z en version v1
			$blocs_defaut = array('contenu', 'navigation', 'extra');
		}

		// Changer la liste au travers du pipeline. A priori le but est de supprimer
		// certains blocs comme head ou head_js si on ne veut pas les configurer.
		$blocs_defaut = pipeline('noizetier_blocs_defaut', $blocs_defaut);
	}

	return $blocs_defaut;
}

/**
 * Retourne la description complète ou une information particulière d'un bloc donné.
 * La description complète du bloc est inscrite dans un fichier YAML nommé `bloc.yaml`
 * stocké dans le dossier du bloc concerné.
 *
 * @api
 *
 * @param string $bloc
 *        Identifiant du bloc, à savoir, le nom du dossier sous Z.
 * @param string $information
 *        Champ précis à renvoyer ou cha^ne vide pour renvoyer tous les champs de l'objet.
 *
 * @return array|string
 *         La description complète sous forme de tableau ou l'information précise demandée.
 */
function noizetier_bloc_lire($bloc, $information = '') {

	static $description_bloc = array();
	$retour = $information ? '' : array();

	if (in_array($bloc, noizetier_bloc_lister_defaut())) {
		if (!isset($description_bloc[$bloc])) {
			if ($fichier = find_in_path("${bloc}/bloc.yaml")) {
				// Il y a un fichier YAML de configuration dans le répertoire du bloc, on le lit.
				// Un YAML de bloc ne peut pas contenir d'inclusion YAML.
				include_spip('inc/yaml');
				if ($description = yaml_decode_file($fichier)) {
					$description['nom'] = isset($description['nom']) ? _T_ou_typo($description['nom']) : ucfirst($bloc);
					if (isset($description['description'])) {
						$description['description'] = _T_ou_typo($description['description']);
					}
					if (!isset($description['icon'])) {
						$description['icon'] = 'bloc-24.png';
					}
				}
			} elseif (!defined('_DIR_PLUGIN_ZCORE')) {
				// Avec Zpip v1, les blocs sont toujours les mêmes : on en donne une description standard.
				$description = array(
					'nom' => _T("noizetier:nom_bloc_${bloc}"),
					'description' => _T("noizetier:description_bloc_${bloc}"),
					'icon' => "bloc-${bloc}-24.png",
				);
			} else {
				// Aucune description, on renvoie juste le nom qui coincide avec l'identifiant du bloc
				$description = array('nom' => ucfirst($bloc));
			}

			// Sauvegarde de la description du bloc pour une consultation ultérieure dans le même hit.
			$description_bloc[$bloc] = $description;
		}
	}

	if (isset($description_bloc[$bloc])) {
		if (!$information) {
			$retour = $description_bloc[$bloc];
		} elseif (isset($description_bloc[$bloc][$information])) {
			$retour = $description_bloc[$bloc][$information];
		}
	}

	return $retour;
}
