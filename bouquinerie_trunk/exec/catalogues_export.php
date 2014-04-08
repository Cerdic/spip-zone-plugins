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

function exec_catalogues_export_dist() {
	include_spip('inc/bouq_presentation');
	$nom_page = debut_page_bouq(_T('bouq:titre_catalogue_export'),"catalogues_export");
                      
	if (!bouq_autoriser()) exit;

	echo debut_gauche($nom_page,true);
	
	bouq_afficher_raccourcis();

	bouq_afficher_infos();

	echo debut_droite($nom_page, true);

	echo debut_cadre_formulaire('',true);
	include_spip("inc/exporter_catalogues");
	$exporter_catalogues = charger_fonction('exporter_catalogues','inc');
	echo $exporter_catalogues();
	echo fin_cadre_formulaire(true);

	echo fin_gauche();

	fin_page_bouq();
}


?>
