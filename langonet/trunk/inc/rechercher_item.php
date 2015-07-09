<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Recherche d'une chaine représentant tout ou partie d'un item de langue de SPIP et des plugins disponibles.
 *
 * @param string $pattern
 * 		le raccourci ou une partie de celui-ci à rechercher.
 * @param string $correspondance
 * 		type de correspondance : egal, commence, ou contient
 * @param array  $modules
 * 		tableau des modules où effectuer la recherche
 * @return array
 */
function inc_rechercher_item($pattern, $correspondance, $modules) {

	// Initialisation du tableau des resultats
	// Si une erreur se produit lors du deroulement de la fonction, le tableau contient le libelle
	// de l'erreur dans $resultats['erreur'].
	// Sinon, cet index n'existe pas
	$resultats = array();

	// On construit la liste de tous les items definis
	// dans tous les fichiers de langue presents sur le site.
	$langue = 'fr';
	$traductions = array();
	$fichiers_langue = array();
	if ($modules) {
		// On sauvegarde l'index de langue global si il existe car on va le modifier pendant le traitement.
		include_spip('inc/outiller');
		sauvegarder_index_langue_global();

		foreach ($modules as $_module) {
			// L'index 0 : nom du module de langue
			// l'index 1 : nom du répertoire contenant lang/
			// l'index 2 : chemin du fichier de langue
			list($nom_module, $plugin, $chemin) = explode(':', $_module);

			// On charge le fichier de langue a lister si il existe dans l'arborescence $chemin
			// (evite le mecanisme standard de surcharge SPIP)
			list($items_langue, $fichier_langue) = charger_module_langue($nom_module, $langue, $chemin);

			// On stocke les items dans des tableaux contenant chacun tous les items recensés
			foreach ($items_langue as $_item => $_traduction) {
				$fichiers_langue[$_item][] = $fichier_langue;
				$traductions[$_item][] = $_traduction;
			}
		}
		ksort($fichiers_langue);

		// On restaure l'index de langue global si besoin
		restaurer_index_langue_global();
	}

	// On cherche le pattern en fonction du type de correspondance
	$trouve= array();
	if ($traductions) {
		if ($correspondance == 'egal') {
			if ($fichiers_langue[$pattern]) {
				$trouve['egal'][$pattern]['fichier'] = $fichiers_langue[$pattern];
				$trouve['egal'][$pattern]['traduction'] = $traductions[$pattern];
			}
		}
		else {
			reset($fichiers_langue);
			while (list($_item, $_fichiers) = each($fichiers_langue)) {
				$commence_par = (substr($_item, 0, strlen($pattern)) == $pattern);
				$contient = false;
				if ($correspondance == 'contient')
					$contient = (strpos($_item, $pattern) !== false);

				if ($_item == $pattern) {
					$trouve['egal'][$_item]['fichier'] = $_fichiers;
					$trouve['egal'][$_item]['traduction'] = $traductions[$_item];
				}
				else if ($commence_par) {
					$trouve['commence'][$_item]['fichier'] = $_fichiers;
					$trouve['commence'][$_item]['traduction'] = $traductions[$_item];
				}
				else if ($contient) {
					$trouve['contient'][$_item]['fichier'] = $_fichiers;
					$trouve['contient'][$_item]['traduction'] = $traductions[$_item];
				}
			}
		}
	}

	// On prepare le tableau des resultats
	if (!$trouve)
		$resultats['erreur'] = _T('langonet:message_nok_item_trouve');
	else {
		$resultats['trouves']['egal'] = isset($trouve['egal']) ? $trouve['egal'] : array();
		$resultats['trouves']['commence'] = isset($trouve['commence']) ? $trouve['commence'] : array();
		$resultats['trouves']['contient'] = isset($trouve['contient']) ? $trouve['contient'] : array();
		$resultats['total'] = count($resultats['trouves']['egal'])
							+ count($resultats['trouves']['commence'])
							+ count($resultats['trouves']['contient']);
	}

	return $resultats;
}

?>