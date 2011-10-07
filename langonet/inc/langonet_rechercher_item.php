<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Vérification de l'utilisation des items de langue
 *
 * @param string $pattern
 * @param string $correspondance
 * @return array
 */

// $pattern      	=> item (le raccourci) ou partie de l'item a rechercher
// $correspondance  => type de correspondance : egal, commence, ou contient
function inc_langonet_rechercher_item($pattern, $correspondance) {

	// Initialisation du tableau des resultats
	// Si une erreur se produit lors du deroulement de la fonction, le tableau contient le libelle
	// de l'erreur dans $resultats['erreur'].
	// Sinon, cet index n'existe pas
	$resultats = array();

	// On construit la liste de tous les items definis
	// dans tous les fichiers de langue presents sur le site.
	// Par economie, on se limite au scan des '/lang/xxxx_fr.php'
	foreach (preg_files(_DIR_RACINE, '/lang/[^/]+_fr\.php$') as $_fichier) {
		// On extrait le module
		preg_match(',/lang/([^/]+)_fr\.php$,i', $_fichier, $module);
		// On recupere le tableau global des items du module
		$var_source = 'i18n_' . $module[1] . '_fr';
		if (empty($GLOBALS[$var_source])) {
			$GLOBALS['idx_lang'] = $var_source;
			include($_fichier);
		}
		// On stocke les items dans des tableaux contenant chacun tous les items recenses
		foreach ($GLOBALS[$var_source] as $_item => $_traduction) {
			$tous_lang[$_item][] = $_fichier;
			$tous_trad[$_item][] = $_traduction;
		}
	}
	ksort($tous_lang);

	// On cherche le pattern en fonction du type de correspondance
	$trouve= array();
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

	// On prepare le tableau des resultats
	if (!$trouve)
		$resultats['erreur'] = _T('langonet:message_nok_item_trouve');
	$resultats['item_trouve']['egal'] = $trouve['egal'];
	$resultats['item_trouve']['commence'] = $trouve['commence'];
	$resultats['item_trouve']['contient'] = $trouve['contient'];

	return $resultats;
}

?>