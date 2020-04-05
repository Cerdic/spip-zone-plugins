<?php
/**
 *      @file formulaires/courbe_remous.php
 *      Formulaire CVT pour le calcul d'une courbe de remous
 */

/*      Copyright 2017 David Dorchies
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


include_spip('hyd_inc/form_courbe_remous.class');
global $FCR; // Nécessaire car nous ne sommes pas dans l'environnement global
$FCR = new form_courbe_remous;

function formulaires_courbe_remous_charger_dist() {
	global $FCR;
	return $FCR->charger();
}


function formulaires_courbe_remous_verifier_dist() {
	global $FCR;
	return $FCR->verifier();
}


function formulaires_courbe_remous_traiter_dist(){
	global $FCR;
	return $FCR->traiter();
}
?>
