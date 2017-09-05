<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Filtre genere un pseudo-titre à une image en se basant sur son nom de fichier
 *
 * ex. IMG/jpg/vu-expo-bains-douches-2.jpg -> Vu expo bains douches 2
 *
 *
 * @param string
 *     Nom complet du fichier
 * @return string
 *     Pseudo titre
 */
function titre_naturel($str) {
	$parts = explode("/", $str);
	$title = ucfirst(substr(end($parts),0,-4));
	$title = str_replace(array("-", "--", "_", "IMG_", "DSC_")," ",$title);

	return $title;
}

