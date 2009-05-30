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

function exec_atelier_metas_dist() {
	include_spip('inc/atelier_presentation');
	include_spip('inc/atelier_autoriser');

	$nom_page = atelier_debut_page(_T('atelier:titre_metas'),'atelier_metas');
	if (!atelier_autoriser()) exit;

	atelier_debut_gauche();
		atelier_cadre_raccourcis();
		atelier_cadre_infos();
	atelier_fin_gauche();
	atelier_debut_droite();
		echo debut_cadre_trait_couleur('',true);
		
		include_spip('inc/atelier_metas');
		$metas = charger_fonction('atelier_metas','inc');
		echo $metas();

		echo fin_cadre_trait_couleur(true);
	
	atelier_fin_droite();
	atelier_fin_page();

}
