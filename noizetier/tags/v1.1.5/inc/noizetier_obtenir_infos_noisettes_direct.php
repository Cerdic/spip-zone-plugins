<?php
/**
 * Obtenir les infos de toutes les noisettes disponibles dans les dossiers noisettes/
 * C'est un GROS calcul lorsqu'il est a faire.
 *
 * @return array
 */
function inc_noizetier_obtenir_infos_noisettes_direct_dist(){

	$liste_noisettes = array();
		
	$match = "[^-]*[.]html$";
	$liste = find_all_in_path('noisettes/', $match);
		
	if (count($liste)){
		foreach($liste as $squelette=>$chemin) {
			$noisette = preg_replace(',[.]html$,i', '', $squelette);
			$dossier = str_replace($squelette, '', $chemin);
			// On ne garde que les squelettes ayant un fichier YAML de config
			if (file_exists("$dossier$noisette.yaml")
				AND ($infos_noisette = noizetier_charger_infos_noisette_yaml($dossier.$noisette))
			){
				$liste_noisettes[$noisette] = $infos_noisette;
			}
		}
	}
	
	// supprimer de la liste les noisettes necissant un plugin qui n'est pas actif
	foreach ($liste_noisettes as $noisette => $infos_noisette)
		if (isset($infos_noisette['necessite']))
			foreach ($infos_noisette['necessite'] as $plugin)
				if (!defined('_DIR_PLUGIN_'.strtoupper($plugin)))
					unset($liste_noisettes[$noisette]);
	
	return $liste_noisettes;
}

?>