<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Recherche d'une chaine UTF-8 dans le texte francais des items de langues de SPIP et des plugins disponibles.
 *
 * @param string $pattern
 * 		la traduction ou une partie de celle-ci à rechercher. Ce texte est en français au format UTF-8
 * @param string $correspondance
 * 		type de correspondance : egal, commence, ou contient
 * @param array  $modules
 * 		tableau des modules où effectuer la recherche
 * @return array
 */
function inc_rechercher_texte($pattern, $correspondance, $modules) {

	// Initialisation du tableau des resultats
	// Si une erreur se produit lors du deroulement de la fonction, le tableau contient le libelle
	// de l'erreur dans $resultats['erreur'].
	// Sinon, cet index n'existe pas.
	$resultats = array();

	// On construit la liste de tous les items definis
	// dans tous les fichiers de langue francais désignés par les modules choisis
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

			// On ajoute le fichier de langue chargé à la liste des traductions en indexant par le nom
			// du module et par celui du répertoire d'accueil du fichier de langue, ce qui rend l'indexation
			// forcément unique.
			$traductions[$nom_module][$plugin] = $items_langue;

			// On contruit le tableau d'association entre le répertoire d'accueil du fichier et le nom du fichier
			$fichiers_langue[$plugin] = $fichier_langue;

			ksort($traductions[$nom_module][$plugin]);
		}

		// On restaure l'index de langue global si besoin
		restaurer_index_langue_global();
	}

	// On cherche le pattern en fonction du type de correspondance
	$trouve = array();
	if ($traductions) {
		// -- Passage en entités HTML du pattern qui est censé toujours être en UTF-8 pour les tests d'égalité.
		$pattern_html = htmlentities($pattern, ENT_COMPAT, 'UTF-8');
		foreach ($traductions as $_module => $_plugins) {
			foreach ($_plugins as $_plugin => $_traductions) {
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
						$trouve['egal'][$_item]['fichier'][] = $fichiers_langue[$_plugin];
						$trouve['egal'][$_item]['traduction'][] = $_texte;
					}
					else if ($commence_par) {
						$trouve['commence'][$_item]['fichier'][] = $fichiers_langue[$_plugin];
						$trouve['commence'][$_item]['traduction'][] = $_texte;
					}
					else if ($contient) {
						$trouve['contient'][$_item]['fichier'][] = $fichiers_langue[$_plugin];
						$trouve['contient'][$_item]['traduction'][] = $_texte;
					}
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