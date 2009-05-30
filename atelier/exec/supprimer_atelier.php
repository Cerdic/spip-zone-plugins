<?php

/*
 *  Plugin Atelier pour SPIP
 *  Copyright (C) 2008  Polez Kévin
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_supprimer_atelier_dist() {

	include_spip('inc/atelier_presentation');
	include_spip('inc/atelier_autoriser');

	$nom_page = atelier_debut_page(_T('atelier:titre_supprimer_atelier'),'supprimer_atelier');
	if (!atelier_autoriser()) exit;

	atelier_debut_gauche($nom_page);

		atelier_cadre_raccourcis();
		atelier_cadre_infos();

	atelier_debut_droite($nom_page);

		echo debut_boite_alerte();
		echo '<p>'._T('atelier:prevenir_suppression').'</p>';
		include_spip('inc/supprimer_atelier');
		$supprimer_atelier = charger_fonction('supprimer_atelier','inc');
		echo $supprimer_atelier();
		echo fin_boite_alerte();

	atelier_fin_gauche();
	atelier_fin_page();

}
?>
