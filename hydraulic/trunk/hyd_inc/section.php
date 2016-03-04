<?php
/**
 *      @file inc_hyd/section.php
 *      Listes des champs concernant les sections paramétrées
 */

/*      Copyright 2012, 2015 David Dorchies <dorch@dorch.fr>, Médéric Dulondel
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
 * Caractéristiques communes aux calculs sur les sections :
 * - Caractéristiques des différents types de section
 * - Caractéristiques du bief
 * @param $bCourbe Pour ajouter la longueur du bief dans la liste des champs (calcul courbe de remous)
 */
function mes_saisies_section($bCourbe=false) {
    /* Tableau niveau 1 : Composantes pour chaque variable, la clé est le nom de
     * la variable dans le formulaire, et la valeur contient un tableau avec le
     * code de langue, la valeur par défaut et les tests de de vérification à
     * effectuer sur le champ (o : obligatoire, p : positif, n : nul accepté)
     */
    $caract_com = array(
        'FT' => array(
            'def_section_trap',
            array(
                'rLargeurFond'  =>array('largeur_fond',2.5,'opn'),
                'rFruit'        =>array('fruit', 0.56,'opn')
            )
        ),

        'FR' => array(
            'def_section_rect',
            array(
                'rLargeurFond'  =>array('largeur_fond',2.5,'op'),
            )
        ),

        'FC' => array(
            'def_section_circ',
            array(
                'rD'  =>array('diametre',2,'op')
            )
        ),

        'FP' => array(
            'def_section_parab',
            array(
                'rk' =>array('coef',0.5,'op'),
                'rLargeurBerge' =>array('largeur_berge',4,'op')
            )
        )
    );

    $caract_com['c_bief'] = array(
       'caract_bief',
       array(
             'rKs'    =>array('coef_strickler',40,'op')));
    if($bCourbe) {
        // Pour la courbe de remous, on a besoin de la longueur du bief en plus
        $caract_com['c_bief'][1]['rLong'] = array('longueur_bief',100,'op');
    }
    $caract_com['c_bief'][1]['rIf'] = array('pente_fond',0.001,'opn');
    $caract_com['c_bief'][1]['rYB'] = array('h_berge',1,'opn');

    return $caract_com;
}



?>
