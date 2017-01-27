<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/bandeau');
include_spip('action/menu_rubriques');

/**
 * Retourne la liste des noms d'entrées de menus favoris de l'auteur connecté
 * @return array
 */
function obtenir_menus_favoris() {
	if (
		isset($GLOBALS['visiteur_session']['prefs']['menus_favoris'])
		and is_array($GLOBALS['visiteur_session']['prefs']['menus_favoris'])
	) {
		return $GLOBALS['visiteur_session']['prefs']['menus_favoris'];
	}
	return array();
}


function tri_menu_alpha($menu_complet) {
	foreach ($menu_complet as $menu) {
		if ($menu->sousmenu) {
			$libelles  = array();
			foreach ($menu->sousmenu as $key => $row) {
				$libelles[$key] = strtolower(translitteration(_T($row->libelle)));
			}
			array_multisort($libelles, SORT_ASC, $menu->sousmenu);
		}
	}
	return $menu_complet;
}

function tri_menu_favoris_alpha($menu_complet) {
	$menus_favoris = obtenir_menus_favoris();
	foreach ($menu_complet as $menu) {
		if ($menu->sousmenu) {
			$libelles  = array();
			foreach ($menu->sousmenu as $key => $row) {
				$libelles[$key] = strtolower(translitteration(_T($row->libelle)));
			}

			if ($menus_favoris) {
				$favoris = array();
				foreach ($menu->sousmenu as $key => $bouton) {
					$favoris[$key] = $bouton->favori = in_array($key, $menus_favoris);
				}
				array_multisort($favoris, SORT_DESC, $libelles, SORT_ASC, $menu->sousmenu);
			} else {
				array_multisort($libelles, SORT_ASC, $menu->sousmenu);
			}
		}
	}
	return $menu_complet;
}
