<?php

/**
 * Déterminer si un logo est le logo par défaut
 */
function est_logo_par_defaut($logo, $id_objet, $objet) {

	$chercher_logo = charger_fonction('chercher_logo', 'inc/');

	$logo_defaut = $chercher_logo($id_objet, id_table_objet($objet), 'on');

	return ($logo === $logo_defaut[0]);
}


/**
 * Gros hack : pour pouvoir fonctionner sans massicot, mais sans non plus
 * s'embêter à traiter les deux cas dans les squelettes, on définit ici la
 * fonction dont on a besoin, dans le cas où le plugin n'est pas installé.
 */
include_spip('inc/plugin');
if (! plugin_est_installe('massicot')) {

	function massicoter_objet($fichier, $objet, $id_objet, $role = null) {

		return $fichier;
	}
}
