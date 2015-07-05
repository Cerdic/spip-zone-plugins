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
		$idx_lang_backup = '';
		if (isset($GLOBALS['idx_lang'])) {
			$idx_lang_backup = $GLOBALS['idx_lang'];
		}

		foreach ($modules as $_module) {
			// L'index 0 : nom du module de langue
			// l'index 1 : nom du répertoire contenant lang/
			// l'index 2 : chemin du fichier de langue
			list($nom_module, $plugin, $chemin) = explode(':', $_module);
			$idx_lang = 'i18n_' . $nom_module . '_' . $langue;

			$backup_trad = array();
			// Si les traductions correspondant à l'index de langue sont déjà chargées on les sauvegarde pour
			// les restaurer en fin de traitement. En effet, si l'index en cours de traitement est
			// déjà chargé, on ne peut pas présumer du fichier de langue source car il est possible d'avoir un même
			// module dans plusieurs plugins.
			if (!empty($GLOBALS[$idx_lang])) {
				$backup_trad = $GLOBALS[$idx_lang];
				unset($GLOBALS[$idx_lang]);
			}

			// On charge le fichier de langue du module en cours de traitement
			$GLOBALS['idx_lang'] = $idx_lang;
			$fichier_lang = $chemin . $nom_module . '_' . $langue . '.php';
			include($fichier_lang);

			// On stocke les items dans des tableaux contenant chacun tous les items recenses
			foreach ($GLOBALS[$idx_lang] as $_item => $_traduction) {
				$fichiers_langue[$_item][] = $fichier_lang;
				$traductions[$_item][] = $_traduction;
			}

			// On rétablit le module backupé si besoin
			unset($GLOBALS[$idx_lang]);
			if ($backup_trad) {
				$GLOBALS[$idx_lang] = $backup_trad;
			}
		}
		ksort($fichiers_langue);

		// On restaure l'index de langue global si besoin
		if ($idx_lang_backup) {
			$GLOBALS['idx_lang'] = $idx_lang_backup;
		}
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