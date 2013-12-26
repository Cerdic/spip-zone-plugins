<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


// Renvoie la liste des archives disponibles au telechargement par date inverse
function saveauto_a_telecharger() {
	include_spip('inc/config');

	$prefixe = lire_config('saveauto/prefixe_save','sav');
	$laver_auto = (lire_config('saveauto/nettoyage_journalier', 'oui') == 'oui');
	$dir_dump = lire_config('saveauto/repertoire_save', _DIR_DUMP);

	$pattern = "${prefixe}.*\.(zip|sql)$";

	if ($laver_auto)
		$liste = preg_files($dir_dump, $pattern);
	else
		$liste = preg_files($dir_dump, $pattern, 50);

	// On filtre les fichiers vides ou corrompues qui sont des résultats d'erreur lors de l'archivage
	foreach ($liste as $_cle => $_sauvegarde) {
		if (!is_file($_sauvegarde)
		OR !is_readable($_sauvegarde)
		OR (filesize($_sauvegarde) == 0))
			unset($liste[$_cle]);
	}

	return array_reverse($liste);
}


// Renvoie l'information demandée sur le zip
function saveauto_informer($fichier, $demande) {
	$info = NULL;
	$extension = pathinfo($fichier, PATHINFO_EXTENSION);

	if ($extension == 'zip') {
		include_spip('inc/pclzip');
		$zip = new PclZip($fichier);
		$proprietes = $zip->properties();

		if ($proprietes == 0)
			spip_log("Impossible d'ouvrir les propriétés de l'archive (" . $fichier_zip->errorInfo(true) . ")", 'saveauto' . _LOG_ERREUR);
		else {
			$comment = unserialize($proprietes['comment']);
			if (isset($comment[$demande])) {
				$info = $comment[$demande];
			}
		}
	}
	else if ($extension == 'sql') {
		if (lire_fichier($fichier, $contenu)) {
			$regexp = '@#\s+' . _T('saveauto:info_sql_'.$demande) . '(.*)$@Uims';
			if (preg_match($regexp, $contenu, $m))
				$info = trim($m[1]);
		}
	}

	return $info;
}

?>
