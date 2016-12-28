<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/bandeau');

if(!function_exists('tri_menu_alpha')) {
    function tri_menu_alpha($menu_complet)
    {

        foreach ($menu_complet as $menu) {
            if ($menu->sousmenu) {
                $sous_menu = $menu->sousmenu;
                $libelles = [];
                foreach ($sous_menu as $key => $row) {
                    $libelles[$key] = strtolower(translitteration(_T($row->libelle)));
                }
                array_multisort($libelles, SORT_ASC, $sous_menu);
                $menu->sousmenu = $sous_menu;
            }
        }

        return $menu_complet;
    }
}