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
 *   url source du composant
 * @param string $dest
 *   chemin du repertoire ou deballer le composant. Inclus le dernier segment
 * @param array $options
 *   revision => ''
 *   --ignore-externals
 * @return bool\string
 */
function action_teleporter_composant_dist($methode,$source,$dest,$options=array()){

	// verifier que la methode est connue
	if (!$teleporter =  charger_fonction($methode,"teleporter",true)){
		spip_log("Methode $methode inconnue pour teleporter $source vers $dest","teleport"._LOG_ERREUR);
		return _T('svp:erreur_teleporter_methode_inconue',array('methode' => $methode));
	}

	if (!$dest = teleporter_verifier_destination($dest)){
		spip_log("Rerpertoire $dest non accessible pour teleporter $source vers $dest","teleport"._LOG_ERREUR);
		return _T('svp:erreur_teleporter_destination_erreur',array('dir' => $dest));
	}

	return $teleporter($methode,$source,$dest,$options);
}


/**
 * Verifier et preparer l'arborescence jusqu'au repertoire parent
 *
 * @param string $dest
 * @return bool|string
 */
function teleporter_verifier_destination($dest){
	$dest = rtrim($dest,"/");
	$final = basename($dest);
	$base = dirname($dest);
	$create = array();
	// on cree tout le chemin jusqu'a dest non inclus
	while (!is_dir($base)){
		$create[] = basename($base);
		$base = dirname($base);
	}
	while (count($create)){
		if (!is_writable($base))
			return false;
		$base = sous_repertoire($base,array_pop($create));
		if (!$base)
			return false;
	}

	if (!is_writable($base))
		return false;

	return $base."/$final";
}