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

function exec_atelier_objets_dist() {

	exec_atelier_objets_args(intval(_request('id_projet')),
				_request('rapport')
	);
}

function exec_atelier_objets_args($id_projet,$rapport='') {
	$projet_select = charger_fonction('projet_select','inc');
	$row = $projet_select($id_projet);

	atelier_objets($id_projet,$row,$rapport);
}

function atelier_objets($id_projet,$row,$rapport='') {
	include_spip('inc/atelier_presentation');
	include_spip('inc/atelier_autoriser');
	$nom_page = atelier_debut_page(_T('atelier:titre_objets'),'atelier_objets');
	if (!atelier_autoriser()) exit;

	atelier_debut_gauche($nom_page);
		atelier_cadre_raccourcis();
		atelier_cadre_infos();
	atelier_debut_droite($nom_page);
		echo debut_cadre_trait_couleur('',true);
		echo '<p>'._T('atelier:explication_objets').'</p>';
		echo fin_cadre_trait_couleur(true);
		echo debut_cadre_formulaire('',true);
		include_spip('inc/atelier_objets.php');
		$atelier_objets = charger_fonction('atelier_objets','inc');
		echo $atelier_objets();
		echo fin_cadre_formulaire(true);
	atelier_fin_gauche();
	atelier_fin_page();
}

?>
