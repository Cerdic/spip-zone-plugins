<?php
/*
 * Copyright (C) 2006 Cedric Morin
 * Licence GPL
 *
 * Plugin SPIP 1.9 (c) 2006 par Notre-ville.net
 * Web : http://www.notre-ville.net
 * Cedric MORIN (cedric.morin@notre-ville.net)
 *
 */

function cron_comarquage_update_xml($t){
	include_spip('inc/comarquage');
	
	$file_liste = array();
	
	// Recherche des fichiers a mettre a jour
	if (isset($GLOBALS['meta']['comarquage_xml_to_update'])){
		$file_liste = unserialize($GLOBALS['meta']['comarquage_xml_to_update']);
		if (!is_array($file_liste)){
			$file_liste = array();
			effacer_meta('comarquage_xml_to_update');
			ecrire_metas();
		}
	}
	if (!isset($GLOBALS['meta']['comarquage_xml_to_update'])){
		// si liste existe pas, on la construit
		// et on rend la main pour que ce soit pas trop long
		$table = preg_files(_DIR_CACHE._DIR_CACHE_COMARQUAGE_XML, '[.]*\.xml$');
		$time = time();
		foreach ($table as $file){
	    if ( ($time - filemtime($file)) >$GLOBALS['meta']['comarquage_local_refresh']) {
	    	$file_liste[] = $file;
	    }
		}
		if (count($file_liste)){
			ecrire_meta('comarquage_xml_to_update',serialize($file_liste));
			ecrire_metas();
			spip_log("[comarquage] ".count($file_liste)." fichiers a mettre a jour ...");
			return (0 - $t); // revenir ...
		}
		else {
			effacer_meta('comarquage_xml_to_update');
			ecrire_metas();
			return 1; // fini
		}
	}
	
	$compteur = 4; // nombre maxi de pages mises a jour
	
	while (($compteur-->0) && is_array($file_liste) && count($file_liste)){
		$file = array_pop($file_liste);
		spip_log("[comarquage] mise a jour $file");

		$parametres = array();
		$parametres['xml_full_path'] = $file;
		$parametres['xml'] = basename($file);
		
		$ret = comarquage_recuperer_page_xml($parametres);
		if ($ret = 10){ // fichier non modifie
			@touch($file);
		}
  	// on nettoie les fichiers cache parses au passage
  	$file = basename($file,'.xml');
		$table = preg_files(_DIR_CACHE._DIR_CACHE_COMARQUAGE_CACHE, '^'.$file.'\.[0-9a-f]*\.cache$');
		foreach ($table as $file){
			@unlink($file);
		}
	}

	if (count($file_liste) && is_array($file_liste)){
		ecrire_meta('comarquage_xml_to_update',serialize($file_liste));
		ecrire_metas();
		spip_log("[comarquage] ".count($file_liste)." fichiers restant a mettre a jour ...");
		return (0 - $t); // revenir ...
	}
	else {
		effacer_meta('comarquage_xml_to_update');
		ecrire_metas();
		return 1; // fini
	}
}


?>
