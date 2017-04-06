<?php

/**
 * Déterminer si un logo est le logo par défaut d'un objet donné
 *
 * @param string $logo : le logo en question
 * @param integer $id_objet : l'identifiant de l'objet
 * @param string $objet : le type de l'objet
 * @param string $role : le rôle du logo
 *
 * @return boolean : true si oui, false sinon…
 */
function est_logo_par_defaut($logo, $id_objet, $objet, $role) {

	$chercher_logo = charger_fonction('chercher_logo', 'inc/');

	$def_logo = lister_roles_logos($objet, $role);

	if (isset($def_logo['defaut'])) {
		$logo_defaut = find_in_path($def_logo['defaut']);
	}

	if (! isset($logo_defaut)) {
		$logo_defaut = $chercher_logo($id_objet, id_table_objet($objet), 'on');
		$logo_defaut = $logo_defaut[0];
	}

	return ($logo === $logo_defaut);
}


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
