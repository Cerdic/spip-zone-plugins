<?php

/**
 * Vérification de l'utilisation des items de langue
 *
 * @param string $pattern
 * @param string $recherche
 * @return array
 */

// $pattern      => item (le raccourci) ou partie de l'item a rechercher
// $recherche    => type de cherche, egal, commence, ou contient
function inc_langonet_rechercher_item($pattern, $recherche) {

	// Initialisation du tableau des resultats
	// Si une erreur se produit lors du deroulement de la fonction, le tableau contient le libelle
	// de l'erreur dans $resultats['erreur'].
	// Sinon, cet index n'existe pas
	$resultats = array();

	// On construit la liste de tous les items definis
	// dans tous les fichiers de langue presents sur le site.
	// Par economie, on se limite au scan des '/lang/xxxx_fr.php'
	foreach (preg_files(_DIR_RACINE, '/lang/[^/]+_fr\.php$') as $_fichier) {
		foreach ($contenu = file($_fichier) as $ligne => $texte) {
			if (preg_match_all("#^[\s\t]*['\"]([a-z0-9_]+)['\"][\s\t]*=>#i", $texte, $matches)) {
				foreach ($matches[1] as $cet_item) {
					$tous_lang[$cet_item][] = $_fichier;
				}
			}
		}
	}
	ksort($tous_lang);

	// On cherche le pattern en fonction du type de recherche
	$trouve= array();
	if ($recherche == 'egal') {
		if ($tous_lang[$pattern]) {
			$trouve['egal'][$pattern] = $tous_lang[$pattern];
		}
	}
	else {
		reset($tous_lang);
		while (list($_item, $_fichiers) = each($tous_lang)) {
			$commence_par = (substr($_item, 0, strlen($pattern)) == $pattern);
			$contient = false;
			if ($recherche == 'contient')
				$contient = (strpos($_item, $pattern) !== false);

			if ($_item == $pattern)
				$trouve['egal'][$_item] = $tous_lang[$_item];
			else if ($commence_par)
				$trouve['commence'][$_item] = $tous_lang[$_item];
			else if ($contient)
				$trouve['contient'][$_item] = $tous_lang[$_item];
		}
	}

	// On prepare le tableau des resultats
	if (!$trouve)
		$resultats['erreur'] = _T('langonet:message_nok_item_trouve');;
	$resultats['item_trouve']['egal'] = $trouve['egal'];
	$resultats['item_trouve']['commence'] = $trouve['commence'];
	$resultats['item_trouve']['contient'] = $trouve['contient'];

	return $resultats;
}

?>