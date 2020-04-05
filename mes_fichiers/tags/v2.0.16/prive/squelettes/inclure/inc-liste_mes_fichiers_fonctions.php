<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


// Renvoie la liste des archives disponibles au telechargement par date inverse
function mes_fichiers_a_telecharger() {
	include_spip('inc/config');

	$prefixe = lire_config('mes_fichiers/prefixe','mf2');
	$laver_auto = (lire_config('mes_fichiers/nettoyage_journalier', 'oui') == 'oui');

	$pattern = "${prefixe}.*\.zip$";

	if ($laver_auto)
		$liste = preg_files(_DIR_MES_FICHIERS, $pattern);
	else
		$liste = preg_files(_DIR_MES_FICHIERS, $pattern, 50);

	// On filtre les fichiers vides ou corrompues qui sont des résultats d'erreur lors de l'archivage
	foreach ($liste as $_cle => $_archive) {
		if (!is_file($_archive)
		OR !is_readable($_archive)
		OR (filesize($_archive) == 0))
			unset($liste[$_cle]);
	}

	return array_reverse($liste);
}


// Renvoie les informations sur le contenu de l'archive
function mes_fichiers_resumer_zip($zip) {
	include_spip('inc/pclzip');

	$fichier_zip = new PclZip($zip);
	$proprietes = $fichier_zip->properties();
	$resume = NULL;
	if ($proprietes == 0) {
		$resume .= _T('mes_fichiers:message_zip_propriete_nok');
		spip_log("Impossible d'ouvrir les propriétés de l'archive (" . $fichier_zip->errorInfo(true) . ")", 'mes_fichiers' . _LOG_ERREUR);
	}
	else {
		$comment = unserialize($proprietes['comment']);
		$liste = $comment['contenu'];
		$id_auteur = $comment['auteur'];

		// On gere la compatibilite avec la structure des commentaires des versions < 0.2
		$auteur = _T('mes_fichiers:message_zip_auteur_indetermine');
		if ((!$id_auteur) AND (!$liste))
			$liste = $comment;
		else
			if (intval($id_auteur)) {
				$auteur = sql_getfetsel('nom', 'spip_auteurs', 'id_auteur='.intval($id_auteur));
			}else{
				$auteur = $id_auteur;
			}
		$resume .= _T('mes_fichiers:resume_zip_statut').' : '.$proprietes['status'].'<br />';
		$resume .= _T('mes_fichiers:resume_zip_auteur').' : '.$auteur.'<br />';
		$resume .= _T('mes_fichiers:resume_zip_compteur').' : '.$proprietes['nb'].'<br />';
		$resume .= _T('mes_fichiers:resume_zip_contenu').' : '.'<br />';
		$resume .= '<ul class="spip">';
		if ($liste)
			foreach ($liste as $_fichier) {
				$resume .= '<li>' . $_fichier . '</li>';
			}
		else
			$resume .= '<li>' . _T('mes_fichiers:message_zip_sans_contenu') . '</li>';
		$resume .= '</ul>';
	}
	return $resume;
}

?>
