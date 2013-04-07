<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Vérification de l'utilisation des items de langue
 *
 * @param string $pattern
 * 		le raccourci ou une partie de celui-ci à rechercher. Ce texte est un index de tableau associatif
 * @param string $correspondance
 * 		type de correspondance : egal, commence, ou contient
 * @param array  $modules
 * 		tableau des modules où effectuer la recherche
 * @return array
 */
function inc_langonet_rechercher_item($pattern, $correspondance, $modules) {

	// Initialisation du tableau des resultats
	// Si une erreur se produit lors du deroulement de la fonction, le tableau contient le libelle
	// de l'erreur dans $resultats['erreur'].
	// Sinon, cet index n'existe pas
	$resultats = array();

	// On construit la liste de tous les items definis
	// dans tous les fichiers de langue presents sur le site.
	$langue = 'fr';
	$tous_trad = array();
	$tous_lang = array();
	if ($modules) {
		foreach ($modules as $_valeur) {
			// L'index 0 correspond au module, l'index 1 au fichier
			$valeurs = explode(':', $_valeur);
			$var_source = 'i18n_' . $valeurs[0] . '_' . $langue;
			if (empty($GLOBALS[$var_source])) {
				$GLOBALS['idx_lang'] = $var_source;
				$fichier_lang = $valeurs[1] . $valeurs[0] . '_' . $langue . '.php';
				include($fichier_lang);
			}
			// On stocke les items dans des tableaux contenant chacun tous les items recenses
			foreach ($GLOBALS[$var_source] as $_item => $_traduction) {
				$tous_lang[$_item][] = $fichier_lang;
				$tous_trad[$_item][] = $_traduction;
			}
		}
		ksort($tous_lang);
	}

	// On cherche le pattern en fonction du type de correspondance
	$trouve= array();
	if ($tous_trad) {
		if ($correspondance == 'egal') {
			if ($tous_lang[$pattern]) {
				$trouve['egal'][$pattern]['fichier'] = $tous_lang[$pattern];
				$trouve['egal'][$pattern]['traduction'] = $tous_trad[$pattern];
			}
		}
		else {
			reset($tous_lang);
			while (list($_item, $_fichiers) = each($tous_lang)) {
				$commence_par = (substr($_item, 0, strlen($pattern)) == $pattern);
				$contient = false;
				if ($correspondance == 'contient')
					$contient = (strpos($_item, $pattern) !== false);

				if ($_item == $pattern) {
					$trouve['egal'][$_item]['fichier'] = $tous_lang[$_item];
					$trouve['egal'][$_item]['traduction'] = $tous_trad[$_item];
				}
				else if ($commence_par) {
					$trouve['commence'][$_item]['fichier'] = $tous_lang[$_item];
					$trouve['commence'][$_item]['traduction'] = $tous_trad[$_item];
				}
				else if ($contient) {
					$trouve['contient'][$_item]['fichier'] = $tous_lang[$_item];
					$trouve['contient'][$_item]['traduction'] = $tous_trad[$_item];
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