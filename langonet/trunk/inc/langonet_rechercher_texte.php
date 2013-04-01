<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Recherche d'une chaine UTF-8 dans le texte francais des items de langues de SPIP et des plugins disponibles
 *
 * @param string $pattern
 * 		la traduction ou une partie de celle-ci à rechercher. Ce texte est en frainçais au format UTF-8
 * @param string $correspondance
 * 		type de correspondance : egal, commence, ou contient
 * @param array  $modules
 * 		tableau des modules où effectuer la recherche
 * @return array
 */
function inc_langonet_rechercher_texte($pattern, $correspondance, $modules) {

	// Initialisation du tableau des resultats
	// Si une erreur se produit lors du deroulement de la fonction, le tableau contient le libelle
	// de l'erreur dans $resultats['erreur'].
	// Sinon, cet index n'existe pas
	$resultats = array();

	// On construit la liste de tous les items definis
	// dans tous les fichiers de langue francais désignés par les modules choisis
	$langue = 'fr';
	$tous_trad = array();
	$tous_lang = array();
	if ($modules) {
		foreach ($modules as $_valeur) {
			$valeurs = explode(':', $_valeur);
			$var_source = 'i18n_' . $valeurs[0] . '_' . $langue;
			if (empty($GLOBALS[$var_source])) {
				$GLOBALS['idx_lang'] = $var_source;
				$fichier_lang = $valeurs[1] . $valeurs[0] . '_' . $langue . '.php';
				include($fichier_lang);
			}
			$tous_trad[$valeurs[0]] = $GLOBALS[$var_source];
			$tous_lang[$valeurs[0]] = $fichier_lang;
			ksort($tous_trad[$valeurs[0]]);
		}
	}

	// On cherche le pattern en fonction du type de correspondance
	$trouve = array();
	if ($tous_trad) {
		// -- Passage en entités HTML du pattern qui est censé toujours être en UTF-8 pour les tests d'égalité.
		$pattern_html = htmlentities($pattern, ENT_COMPAT, 'UTF-8');
		foreach ($tous_trad as $_module => $_traductions) {
			foreach ($_traductions as $_item => $_texte) {
				$_texte_html = htmlentities($_texte, ENT_COMPAT, 'UTF-8');
				$egal = ((strcasecmp($_texte, $pattern) == 0)
					OR (strcasecmp($_texte, $pattern_html) == 0)
					OR (strcasecmp($_texte_html, $pattern_html) == 0));

				$commence_par = false;
				$contient = false;
				if (!$egal AND ($correspondance != 'egal')) {
					$commence_par = ((strcasecmp(substr($_texte, 0, strlen($pattern)), $pattern) == 0)
						OR (strcasecmp(substr($_texte, 0, strlen($pattern_html)), $pattern_html) == 0)
						OR (strcasecmp(substr($_texte_html, 0, strlen($pattern_html)), $pattern_html) == 0));

					if (!$commence_par AND ($correspondance == 'contient'))
						$contient = ((stripos($_texte, $pattern) !== false)
							OR (stripos($_texte, $pattern_html) !== false)
							OR (stripos($_texte_html, $pattern_html) !== false));
				}

				if ($egal) {
					$trouve['egal'][$_item]['fichier'][] = $tous_lang[$_module];
					$trouve['egal'][$_item]['traduction'][] = $_texte;
				}
				else if ($commence_par) {
					$trouve['commence'][$_item]['fichier'][] = $tous_lang[$_module];
					$trouve['commence'][$_item]['traduction'][] = $_texte;
				}
				else if ($contient) {
					$trouve['contient'][$_item]['fichier'][] = $tous_lang[$_module];
					$trouve['contient'][$_item]['traduction'][] = $_texte;
				}
			}
		}
	}

	// On prepare le tableau des resultats
	if (!$trouve)
		$resultats['erreur'] = _T('langonet:message_nok_item_trouve');
	else {
		$resultats['item_trouve']['egal'] = $trouve['egal'];
		$resultats['item_trouve']['commence'] = $trouve['commence'];
		$resultats['item_trouve']['contient'] = $trouve['contient'];
	}

	return $resultats;
}

?>