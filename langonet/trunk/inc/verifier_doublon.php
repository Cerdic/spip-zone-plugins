<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Vérification de l'utilisation des items de langue
 *
 * @param string $verification
 * 		Type de vérification : 'item' pour la recherche de doublons dans les raccourcis ou
 * 		'texte' pour la recherche de doublons dans les traductions
 * @param array  $modules
 * 		Tableau des modules où effectuer la vérification
 * @return array
 */
function inc_verifier_doublon($verification, $modules) {

	// Initialisation du tableau des resultats
	// Si une erreur se produit lors du deroulement de la fonction, le tableau contient le libelle
	// de l'erreur dans $resultats['erreur'].
	// Sinon, cet index n'existe pas
	$resultats = array();

	// On construit la liste de tous les items definis
	// dans tous les fichiers de langue presents sur le site.
	$langue = 'fr';
	$items = array();
	$traductions = array();
	$doublons = array();
	$doublons_traductions = array();
	$index_doublon = 0;
	if ($modules) {
		// On sauvegarde l'index de langue global si il existe car on va le modifier pendant le traitement.
		$idx_lang_backup = '';
		if (isset($GLOBALS['idx_lang'])) {
			$idx_lang_backup = $GLOBALS['idx_lang'];
		}

		foreach ($modules as $_module) {
			// L'index 0 correspond au module, l'index 1 au chemin
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

			// On charge le fichier de langue du module en cours de traitement. Le fichier existe toujours
			// puisqu'on vient de le scanner.
			$GLOBALS['idx_lang'] = $idx_lang;
			$fichier_lang = $chemin . $nom_module . '_' . $langue . '.php';
			include($fichier_lang);

			// On stocke les items dans des tableaux contenant chacun tous les items recenses
			foreach ($GLOBALS[$idx_lang] as $_raccourci => $_traduction) {
				if ($verification == 'item') {
					// Vérification des doublons de raccourci
					// --------------------------------------

					if (isset($items[$_raccourci])) {
						if (count($items[$_raccourci]) == 1) {
							// C'est le premier doublon pour ce raccourci, on insère l'item déjà trouvé
							$doublons[$_raccourci][] = $items[$_raccourci][0];
						}
						// C'est un doublon de raccourci: on le stocke dans le tableau des doublons
						$doublons[$_raccourci][] = array($fichier_lang, $_traduction);
					}
					// On stocke l'item systématiquement dans le tableau de tous les items parcourus
					$items[$_raccourci][] = array($fichier_lang, $_traduction);
				}
				else {
					// Vérification des doublons de traduction
					// ---------------------------------------

					// On nettoie la traduction afin de comparer des chaines approchantes
					// - on supprime les espaces de debut et de fin
					// - on remplace des espaces multiples en un espace
					// - on supprime la ponctuation finale comme le point et les deux-points
					$traduction_nettoyee = strtolower(trim($_traduction));
					$traduction_nettoyee = preg_replace('/\s\s+/', ' ', $traduction_nettoyee);
					$traduction_nettoyee = trim(rtrim($traduction_nettoyee, '.:'));
					// On construit le tableau de l'occurrence qui sera stockée si doublon
					$occurrence = array($fichier_lang, $_traduction, $traduction_nettoyee, $_raccourci);

					if (in_array($traduction_nettoyee, $traductions)) {
						$cles_doublons = array_keys($traductions, $traduction_nettoyee);
						if (count($cles_doublons) == 1) {
							// C'est le premier doublon pour cette traduction, on insère l'item déjà trouvé
							$doublons[$index_doublon][] = $items[$cles_doublons[0]];
							$doublons_traductions[$index_doublon] = $traduction_nettoyee;
							// Et on insère l'item en cours qui est le doublon au même index
							$doublons[$index_doublon][] = $occurrence;
							// En fin on incrémente l'index des doublons
							$index_doublon++;
						}
						else {
							// L'item a déjà été détecté comme un doublon, il faut donc retrouver son
							// index avant d'insérer l'item en cours
							$i = array_search($traduction_nettoyee, $doublons_traductions);
							$doublons[$i][] = $occurrence;
						}
					}
					// On stocke l'item systématiquement dans le tableau de tous les items parcourus
					// ainsi que sa traduction nettoyée dans un tableau synchronisé en index
					$traductions[] = $traduction_nettoyee;
					$items[] = $occurrence;
				}
			}

			// On rétablit le module backupé si besoin
			unset($GLOBALS[$idx_lang]);
			if ($backup_trad) {
				$GLOBALS[$idx_lang] = $backup_trad;
			}

		}
		ksort($doublons);

		// On restaure l'index de langue global si besoin
		if ($idx_lang_backup) {
			$GLOBALS['idx_lang'] = $idx_lang_backup;
		}
		else {
			unset($GLOBALS['idx_lang']);
		}
	}

	// On prepare le tableau des resultats
	// Il n'y a pas de cas d'erreur aujourd'hui
	$resultats['total'] = count($doublons);
	$resultats['doublons'] = $doublons;

	return $resultats;
}

?>