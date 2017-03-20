<?php

/**
 * Déterminer si un logo est le logo par défaut
 */
function est_logo_par_defaut($logo, $id_objet, $objet) {

	$chercher_logo = charger_fonction('chercher_logo', 'inc/');

	$logo_defaut = $chercher_logo($id_objet, id_table_objet($objet), 'on');

	return ($logo === $logo_defaut[0]);
}