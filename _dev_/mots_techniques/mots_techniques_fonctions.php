<?php

/**
 *  recherche une icone dans le path de spip, stockee dans le dossier $dossier ("images/" par defaut)
 * si get_icone('nom','categorie','16');
 * cherche dans le path :
 * - images/nom-categorie-16.png
 * - images/nom-categorie-16.gif
 * - images/nom-16.png
 * - images/nom-16.gif
 * 
 * @param string $nom_base prefixe du nom de l'icone
 * @param string $categorie corps du nom de l'icone
 * @param int $taille suffixe du nom de l'icone
 * @param string $dossier dossier dans lequel chercher l'icone
 * @return string/"" adresse de l'icone
 */
function mt_get_icone($nom_base, $categorie='', $taille=24, $dossier='images/'){
	$exts = array('png','gif');
	// recherche du nom de base
	if ($categorie) {
		foreach ($exts as $ext){
			if ($f = find_in_path("$dossier$nom_base-$categorie-$taille.$ext")) return $f;
		}
	}
	foreach ($exts as $ext){
		if ($f = find_in_path("$dossier$nom_base-$taille.$ext")) return $f;
	}	
	return "";
}


?>
