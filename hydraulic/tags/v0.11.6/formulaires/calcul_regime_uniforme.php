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


include_spip('hyd_inc/form_regime_uniforme.class');
global $FRU; // Nécessaire car nous ne sommes pas dans l'environnement global
$FRU = new form_regime_uniforme;

function formulaires_calcul_regime_uniforme_charger_dist() {
	global $FRU;
	return $FRU->charger();
}


function formulaires_calcul_regime_uniforme_verifier_dist() {
	global $FRU;
	return $FRU->verifier();
}


function formulaires_calcul_regime_uniforme_traiter_dist(){
	global $FRU;
	return $FRU->traiter();
}
?>