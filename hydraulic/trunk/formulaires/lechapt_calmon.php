<?php
/*
 * formulaires/lechapt_calmon.php
 *
 *
 *
 * Copyright 2012 David Dorchies <dorch@dorch.fr>
 *
 *
 *
 * This program is free software; you can redistribute it and/or modify
 *
 * it under the terms of the GNU General Public License as published by
 *
 * the Free Software Foundation; either version 2 of the License, or
 *
 * (at your option) any later version.
 *
 *
 *
 * This program is distributed in the hope that it will be useful,
 *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *
 * GNU General Public License for more details.
 *
 *
 *
 * You should have received a copy of the GNU General Public License
 *
 * along with this program; if not, write to the Free Software
 *
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 *
 * MA 02110-1301, USA.
 *
 */

include_spip('hyd_inc/form_lechapt_calmon.class');
global $FLC;
$FLC = new form_lechapt_calmon;

function formulaires_lechapt_calmon_charger_dist() {
	global $FLC;
	return $FLC->charger();
}


function formulaires_lechapt_calmon_verifier_dist() {
	global $FLC;
	return $FLC->verifier();
}


function formulaires_lechapt_calmon_traiter_dist() {
	global $FLC;
	return $FLC->traiter();
}
?>