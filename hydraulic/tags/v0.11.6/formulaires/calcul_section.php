<?php
/**
 *      @file formulaires/calcul_normale_critique.php
 *      Formulaire CVT pour les calculs des paramètres hydrauliques d'une section
 */

/*      Copyright 2012 Médéric Dulondel
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


include_spip('hyd_inc/form_calcul_section.class');
global $FCS;
$FCS = new form_calcul_section;


function formulaires_calcul_section_charger_dist() {
	global $FCS;
	return $FCS->charger();
}


function formulaires_calcul_section_verifier_dist() {
	global $FCS;
	return $FCS->verifier();
}


function formulaires_calcul_section_traiter_dist() {
	global $FCS;
	return $FCS->traiter();
}
?>