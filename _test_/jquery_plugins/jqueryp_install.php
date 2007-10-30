<?php
	
function jqueryp_install($action){
	switch ($action){
		case 'test':
			break;
		case 'install':
			return jqueryp_test_librairies();
			break;
		case 'uninstall':
			break;
	}
}

function jqueryp_test_librairies(){
	global $jquery_plugins;
	
	// liste plugins a installer
	$lpai = array();
	// si le dossier existe, on onsidere que la librairie est la.
	foreach ($jquery_plugins as $nom=>$lib){
		if (!is_dir(_DIR_RACINE . _DIR_LIB . $lib['dir']))
			$lpai[] = $nom;
	}
	if (empty($lpai)) 
		return true;
	else
		return jqueryp_install_librairies($lpai);
}



// force l'installation des librairies donnees
function jqueryp_install_librairies($liste_plugins){
	global $jquery_plugins;
	
	if (!is_array($liste_plugins)) $liste_plugins = array($liste_plugins);
	
	$lpai = array();
	foreach ($liste_plugins as $nom){
		$lpai[$nom] = $jquery_plugins[$nom];
	}
	
	$return = true;
	include_spip('inc/distant');
	include_spip('inc/flock');
	
	foreach ($lpai as $nom=>$lib){
		if (isset($lib['install'])){
			// dir racine car dans prive !!!
			$dir = _DIR_RACINE . _DIR_LIB . $lib['dir'];
			if (!is_dir($dir)
				AND !sous_repertoire($dir)){
				spip_log("Echec installation : Impossible de creer le repertoire $lib[dir] dans " . _DIR_LIB, 'jquery_plugins');
				$return = false;
			} else {
				// repertoire destination present
				foreach ($lib['install'] as $f=>$adresse){
					// si fichier deja present -> passer
					if (is_file($dest = $dir . '/' . $f))
						continue;
					// telecharger et recopier
					if ($c = recuperer_page($adresse)){
						if (!ecrire_fichier($dest, $c)) {
							spip_log("Echec installation : Impossible d'ecrire le fichier $f dans $dir", 'jquery_plugins');
							$return = false;
						} else {
							spip_log("+ Installation : Copie de $adresse dans $dir/$f", 'jquery_plugins');
						}
					} else {
						spip_log("- Echec installation : Impossible de rapatrier $adresse dans $nom, $f", 'jquery_plugins');
						$return = false;
					}
				}
			}
		}
	}
	return $return;
}


?>
