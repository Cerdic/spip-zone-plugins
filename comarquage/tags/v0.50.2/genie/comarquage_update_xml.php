<?php
/* Plugin Comarquage -flux V2-
 * Copyright (C) 2006 Cedric Morin
 * Copyright (C) 2010 Vernalis Interactive
 *
 * Licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

function genie_comarquage_update_xml_dist($t) {
	include_spip('inc/comarquage');
	include_spip('inc/config');
	include_spip('inc/utils');

	$file_liste = array();

	// Recherche des fichiers a mettre a jour
	if (isset($GLOBALS['meta']['comarquage_xml_to_update'])) {
		$file_liste = unserialize($GLOBALS['meta']['comarquage_xml_to_update']);
		if (!is_array($file_liste)) {
			$file_liste = array();
			effacer_config('comarquage_xml_to_update');
		}
	}
	if (!isset($GLOBALS['meta']['comarquage_xml_to_update'])) {
		// si liste existe pas, on la construit
		// et on rend la main pour que ce soit pas trop long

		// On a des dossiers cache différents selon la catégorie
		$categories = array('particuliers', 'associations', 'entreprises');
		foreach ($categories as $cat) {
			$table = preg_files(_DIR_CACHE . _DIR_CACHE_COMARQUAGE_XML . "_" . $cat, '[.]*\.xml$');

			$time = time();
			foreach ($table as $file) {
				if ((count($file_liste) < 1000) && (($time - filemtime($file)) > $GLOBALS['meta']['comarquage_local_refresh'])) {
					$file_liste[] = $file;
				}
			}
		}
		if (count($file_liste)) {
			ecrire_config('comarquage_xml_to_update', serialize($file_liste));
			spip_log("[comarquage] " . count($file_liste) . " fichiers a mettre a jour ...", "comarquage_update");

			//return (0 - $t); // revenir ...
			return 1; // fini pour cette fois
		} else {
			spip_log("Pas de fichier pour la mise à jour", "comarquage_update");
			effacer_config('comarquage_xml_to_update');

			return 1; // fini
		}
	}

	$compteur = 10; // nombre maxi de pages mises a jour

	while (($compteur-- > 0) && is_array($file_liste) && count($file_liste)) {
		$file = array_pop($file_liste);
		spip_log("[comarquage] mise a jour $file", "comarquage_update");

		$parametres = array();
		$parametres['xml_full_path'] = $file;
		$parametres['xml'] = basename($file);

		$ret = comarquage_recuperer_page_xml($parametres);
		if ($ret = 10) { // fichier non modifie
			@touch($file);
		}
		// on nettoie les fichiers cache parses au passage
		$file = basename($file, '.xml');

		$table = preg_files(_DIR_CACHE . _DIR_CACHE_COMARQUAGE_CACHE, '^' . $file . '\.[0-9a-f]*\.cache$');
		foreach ($table as $file) {
			@unlink($file);
		}
	}

	if (count($file_liste) && is_array($file_liste)) {
		ecrire_config('comarquage_xml_to_update', serialize($file_liste));
		spip_log("[comarquage] " . count($file_liste) . " fichiers restant a mettre a jour ...", "comarquage_update");

		//return (0 - $t); // revenir ...
		return 1; // fini pour cette fois
	} else {
		effacer_config('comarquage_xml_to_update');

		return 1; // fini
	}
}

