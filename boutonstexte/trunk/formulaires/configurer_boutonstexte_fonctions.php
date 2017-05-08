<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


// https://code.spip.net/@find_all_in_path
function find_boutons_skins(){
	$liste_fichiers=array();
	$maxfiles = 10000;

	$dir = "boutonstexte/themes/";
	$themes = array();
	// Parcourir le chemin
	foreach (creer_chemin() as $d) {
		$f = $d.$dir;
		if (@is_dir($f)){
			$liste = preg_files($f,"fontsizeup.png",$maxfiles-count($liste_fichiers),array());
			foreach($liste as $chemin){
				$nom = substr(dirname($chemin),strlen($f));
				$themes[$nom] = $nom;
			}
		}
	}
	return $themes;
}