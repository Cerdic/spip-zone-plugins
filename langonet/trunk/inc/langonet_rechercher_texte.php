<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Recherche d'une chaine dans le texte francais des items de langues de SPIP
 * (ecrire_fr, public_fr et spip_fr)
 *
 * @param string $pattern
 * @param string $correspondance
 * @return array
 */

// $pattern      	=> item (le raccourci) ou partie de l'item a rechercher
// $correspondance  => type de correspondance : egal, commence, ou contient
function inc_langonet_rechercher_texte($pattern, $correspondance, $modules) {

	// Initialisation du tableau des resultats
	// Si une erreur se produit lors du deroulement de la fonction, le tableau contient le libelle
	// de l'erreur dans $resultats['erreur'].
	// Sinon, cet index n'existe pas
	$resultats = array();

	// On construit la liste de tous les items definis
	// dans tous les fichiers de langue francais de SPIP (pour l'instant)
	// '/lang/spip_fr.php, /lang/ecrire_fr.php, /lang/public_fr.php'
	$langue = 'fr';
	$spip_trad = array();
	if ($modules) {
		foreach ($modules as $_valeur) {
			$valeurs = explode(':', $_valeur);
			$var_source = 'i18n_' . $valeurs[0] . '_' . $langue;
			if (empty($GLOBALS[$var_source])) {
				$GLOBALS['idx_lang'] = $var_source;
				include($valeurs[1] . $valeurs[0] . '_' . $langue . '.php');
			}
			$spip_trad[$valeurs[0]] = $GLOBALS[$var_source];
			ksort($spip_trad[$valeurs[0]]);
		}
	}

	// On cherche le pattern en fonction du type de correspondance
	$trouve = array();
	$pattern_html = htmlentities($pattern, ENT_COMPAT, 'UTF-8');
	if ($spip_trad) {
		foreach ($spip_trad as $_module => $_traductions) {
			$fichier = '../ecrire/lang/' . $_module . '_' . $langue . '.php';
			foreach ($_traductions as $_item => $_texte) {
				$_texte = strtolower($_texte);
				$egal = ((strcasecmp($_texte, $pattern) == 0) OR (strcasecmp($_texte, $pattern_html) == 0));
				$commence_par = false;
				$contient = false;
				if (!$egal AND ($correspondance != 'egal')) {
					$commence_par = (substr(strtolower($_texte), 0, strlen($pattern)) == strtolower($pattern));
					if (!$commence_par AND ($correspondance == 'contient'))
						$contient = (strpos(strtolower($_texte), strtolower($pattern)) !== false);
				}

				if ($egal) {
					$trouve['egal'][$_item]['fichier'][] = $fichier;
					$trouve['egal'][$_item]['traduction'][] = $_texte;
				}
				else if ($commence_par) {
					$trouve['commence'][$_item]['fichier'][] = $fichier;
					$trouve['commence'][$_item]['traduction'][] = $_texte;
				}
				else if ($contient) {
					$trouve['contient'][$_item]['fichier'][] = $fichier;
					$trouve['contient'][$_item]['traduction'][] = $_texte;
				}
			}
		}
	}

	// On prepare le tableau des resultats
	if (!$trouve)
		$resultats['erreur'] = _T('langonet:message_nok_item_trouve');
	$resultats['item_trouve']['egal'] = $trouve['egal'];
	$resultats['item_trouve']['commence'] = $trouve['commence'];
	$resultats['item_trouve']['contient'] = $trouve['contient'];

	return $resultats;
}

?>