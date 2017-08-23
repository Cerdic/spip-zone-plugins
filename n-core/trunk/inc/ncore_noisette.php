<?php
/**
 * Ce fichier contient l'API N-Core de gestion des noisette, c'est-à-dire les instances paramétrées
 * de types de noisettes affectées à un squelette.
 *
 * @package SPIP\NCORE\NOISETTE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Ajoute à un squelette, à un rang donné ou en dernier rang, une noisette d'un type donné.
 *
 * @param string	$plugin
 *      Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *      un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string	$type_noisette
 * 		Identifiant du type de noisette à ajouter au squelette.
 * @param string	$squelette
 * 		Nom du bloc où ajouter la noisette.
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
			'squelette'  => $squelette,
			'noisette'   => $type_noisette,
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

		if (!$rang) {
			// Si, le rang est nul, on positionne la noisette à ajouter au rang max + 1.
			$description['rang'] = $rang_max + 1;
		} else {
			// Si le rang est non nul c'est qu'on insère la noisette dans la liste existante. Néanmoins, si le rang
			// est strictement supérieur au rang_max c'est que la nouvelle noisette est ajoutée après les noisettes
			// existantes, donc on ne fait rien.
			// Sinon, il faut décaler les noisettes de rang supérieur ou égal.
			if ($rang <= $rang_max) {
				foreach ($noisettes as $_id_noisette => $_description) {
					if ($_description['rang'] >= $rang) {
						$_description['rang'] += 1;
 						ncore_noisette_stocker($plugin, 'modification', $_description, $stockage);
					}
				}
			}
		}

		// La description de la nouvelle noisette est prête à être stockée.
		$noisette_ajoutee = ncore_noisette_stocker($plugin,'creation', $description, $stockage);
	}

	return $noisette_ajoutee;
}

function noisette_supprimer($plugin, $identifiant, $stockage = '') {

	// Initialisation du retour
	$retour = false;

	if (intval($identifiant)) {
		// Suppression d'un noisette connue par son id.
		$retour = ncore_noisette_destocker($plugin, $identifiant, $stockage);
	} elseif (is_string($identifiant) and $identifiant) {
		// Suppression de toutes les noisettes affectées à un squelette.
		$retour = ncore_noisette_destocker_squelette($plugin, $identifiant, $stockage);
	}

	return $retour;
}