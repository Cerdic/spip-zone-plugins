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

include_spip('hyd_inc/form_cond_distri.class');
global $FCD;
$FCD = new form_cond_distri;

function formulaires_cond_distri_charger_dist() {
	global $FCD;
	return $FCD->charger();
}


function formulaires_cond_distri_verifier_dist() {
	global $FCD;
	return $FCD->verifier();
}


function formulaires_cond_distri_traiter_dist() {
	global $FCD;
	return $FCD->traiter();
}
?>