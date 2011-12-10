<?php
/**
 * Plugin S.P
 * Licence IV
 * (c) 2011 vers l'infini et au dela
 */

/**
 * Teleporter et deballer un composant
 * @param string $methode
 *   http|git|svn|...
 * @param string $source
 * @param string $dest
 * @param array $options
 *   non utilise ici
 * @return bool
 */
function teleporter_http_dist($methode,$source,$dest,$options=array()){

	$tmp = $options['dir_tmp'];
	# on ne se contente pas du basename qui peut etre un simple v1
	# exemple de l'url http://nodeload.github.com/kbjr/Git.php/zipball/v0.1.1-rc
	$fichier = $tmp . (basename($dest)."-".substr(md5($source),0,8)."-".basename($source));

	$res = teleporter_http_recuperer_source($source,$fichier);
	if (!is_array($res))
		return $res;

	list($fichier,$extension) = $res;
	if (!$deballe = charger_fonction("http_deballe_$extension","teleporter",true))
		return _T('svp:erreur_teleporter_format_archive_non_supporte',array('extension' => $extension));

	$old = "";
	if (is_dir($dest)){
		$dir = dirname($dest);
		$base = basename($dest);
		$old="$dir/.$base.bck";
		if (is_dir($old))
			supprimer_repertoire($old);
		rename($dest,$old);
	}

	if (!$target = $deballe($fichier, $dest, $tmp)){
		// retablir l'ancien sinon
		if ($old)
			rename($old,$dest);
		return _T('svp:erreur_teleporter_echec_deballage_archive',array('fichier' => $fichier));
	}

	return true;
}

/**
 * Recuperer la source et detecter son extension
 *
 * @param string $source
 * @param string $dest_tmp
 * @return array|string
 */
function teleporter_http_recuperer_source($source,$dest_tmp){

	# securite : ici on repart toujours d'une source neuve
	if (file_exists($dest_tmp))
		spip_unlink($dest_tmp);

	$extension = "";

	# si on ne dispose pas encore du fichier
	# verifier que le zip en est bien un (sans se fier a son extension)
	#	en chargeant son entete car l'url initiale peut etre une simple
	# redirection et ne pas comporter d'extension .zip
	include_spip('inc/distant');
	$head = recuperer_page($source, false, true, 0);

	if (preg_match(",^Content-Type:\s*application/zip$,Uims",$head))
		$extension = "zip";
	elseif (preg_match(",^Content-Disposition:\s*attachment;\s*filename=(.*)$,Uims",$head,$m)){
		$f = $m[1];
		if (pathinfo($f, PATHINFO_EXTENSION)=="zip"){
			$extension = "zip";
		}
	}
	// au cas ou, si le content-type n'est pas la
	// mais que l'extension est explicite
	elseif(pathinfo($source, PATHINFO_EXTENSION)=="zip")
		$extension = "zip";

	# format de fichier inconnu
	if (!$extension) {
		spip_log("Type de fichier inconnu pour la source $source","teleport"._LOG_ERREUR);
		return _T('svp:erreur_teleporter_type_fichier_inconnu',array('source' => $source));
	}

	$dest_tmp = preg_replace(";\.[\w]{2,3}$;i","",$dest_tmp).".$extension";

	include_spip('inc/distant');
	$dest_tmp = copie_locale($source,'force',$dest_tmp);
	if (!$dest_tmp
	  OR !file_exists($dest_tmp = _DIR_RACINE . $dest_tmp)) {
		spip_log("Chargement impossible de la source $source","teleport"._LOG_ERREUR);
		return _T('svp:erreur_teleporter_chargement_source_impossible',array('source' => $source));
	}

	return array($dest_tmp,$extension);
}