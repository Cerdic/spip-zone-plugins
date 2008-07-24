<?php

/*
 *  Plugin Bouquinerie pour SPIP
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

include_spip('inc/bouq_presentation');

function exec_supprimer_bouquinerie_dist() {

	$nom_page = debut_page_bouq(_T('bouq:titre_admin'),"admin_bouquinerie");
	if (!bouq_autoriser()) exit;

	echo debut_gauche($nom_page,true);

	bouq_afficher_raccourcis();
	echo '<br />';
	bouq_afficher_infos();

	echo debut_droite($nom_page,true);
	echo debut_boite_alerte();
	echo '<p style="font-weight: bold;color:red; text-align: center">'._T('bouq:attention_supprimer_bouquinerie').'</p>';
	include_spip('inc/supprimer_bouquinerie');
	$supprimer_bouquinerie = charger_fonction("supprimer_bouquinerie","inc");
	echo $supprimer_bouquinerie();
	echo fin_boite_alerte();
	echo fin_gauche();
	fin_page_bouq();
}

?>
