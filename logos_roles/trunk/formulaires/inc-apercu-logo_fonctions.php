<?php

/**
 * Gros hack : pour pouvoir fonctionner sans massicot, mais sans non plus
 * s'embêter à traiter les deux cas dans les squelettes, on définit ici la
 * fonction dont on a besoin, dans le cas où le plugin n'est pas installé.
 */
if (! function_exists('massicoter_objet')) {

	function massicoter_objet($fichier, $objet, $id_objet, $role = null) {

		return $fichier;
	}
}
