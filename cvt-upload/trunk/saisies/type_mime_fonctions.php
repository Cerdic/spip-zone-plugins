<?php


// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Remplit un tableau de description de types mime
 * Sous la forme type_mime => 'Nom du type (extensions)'
 *
 * @param array $tableau le tableau en l'état actuel
 * @param string $mime le type mime qu'on veut mettre à jour
 * @param string $titre le titre associé au type mime
 * @param string $extension une extension associé au type mime
 * @return array le tableau modifié
 *
**/
function remplir_tableau_mime($tableau, $mime, $titre, $extension) {
	$txt = "$titre (.$extension)";
	if (array_key_exists($mime, $tableau)) {
		$tableau[$mime] .= " / $txt";
	} else {
		$tableau[$mime] = $txt;
	}
	return $tableau;
}
