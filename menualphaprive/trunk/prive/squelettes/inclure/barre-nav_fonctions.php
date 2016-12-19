<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/bandeau');
include_spip('action/menu_rubriques');

function sort_menu($menu_complet) {

	foreach ($menu_complet as $menu) {
		if ($menu->sousmenu) {
			$sous_menu = $menu->sousmenu;
			$libelles  = array();
			foreach ($sous_menu as $key => $row) {
				$libelles[$key] = strtolower(translitteration(_T($row->libelle)));
			}
			array_multisort($libelles, SORT_ASC, $sous_menu);
			$menu->sousmenu = $sous_menu;
		}
	}

	return $menu_complet;
}