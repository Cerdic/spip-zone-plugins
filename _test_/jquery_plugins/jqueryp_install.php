<?php

function jqueryp_install($action){
	switch ($action){
		case 'test':
			break;
		case 'install':
			return jqueryp_install_librairies();
			break;
		case 'uninstall':
			break;
	}
}
	
function jqueryp_install_librairies(){
	global $jquery_plugins;
	
	$return = true;
	include_spip('inc/distant');
	include_spip('inc/flock');
	
	foreach ($jquery_plugins as $nom=>$lib){
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
					// telecharger et recopier
					if ($c = recuperer_page($adresse)){
						if (!ecrire_fichier($dir . '/' . $f, $c)) {
							spip_log("Echec installation : Impossible d'ecrire le fichier $f dans $dir", 'jquery_plugins');
							$return = false;
						} else {
							spip_log("* Installation : Copie de $adresse dans $dir/$f", 'jquery_plugins');
						}
					} else {
						spip_log("Echec installation : Impossible de rapatrier $adresse dans $nom, $f", 'jquery_plugins');
						$return = false;
					}
				}
			}
		}
	}
	return $return;
}

?>
