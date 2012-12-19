<?php
/**
 *      @file inc_hyd/section.php
 *      Listes des champs concernant les sections paramétrées
 */

/*      Copyright 2012 David Dorchies <dorch@dorch.fr>, Médéric Dulondel
 *
 *      This program is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation; either version 2 of the License, or
 *      (at your option) any later version.
 *
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *
 *      You should have received a copy of the GNU General Public License
 *      along with this program; if not, write to the Free Software
 *      Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 *      MA 02110-1301, USA.
 */

/*
 * Caractéristiques des différents types de section. 
 */
function caract_communes() {

    $caract_com = array(
        'FT' => array(
            'def_section_trap',
            array(
                'rLargeurFond'  =>array('largeur_fond',2.5),
                'rFruit'        =>array('fruit', 0.56)
            )
        ),

        'FR' => array(
            'def_section_rect',
            array(
                'rLargeurBerge'  =>array('largeur_fond',2.5),
            )
        ),

        'FC' => array(
            'def_section_circ',
            array(
                'rD'  =>array('diametre',2)
            )
        ),

        'FP' => array(
            'def_section_parab',
            array(
                'rk' =>array('coef',0.5),
                'rLargeurBerge' =>array('largeur_berge', 4)
            )
        )
    );

    return $caract_com;
}

?>
