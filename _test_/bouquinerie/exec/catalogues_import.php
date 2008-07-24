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

function exec_catalogues_import_dist() {

	include_spip('inc/bouq_presentation');
	$nom_page = debut_page_bouq(_T('bouq:titre_catalogue_import'),"catalogues_import");
                      
	if (!bouq_autoriser()) exit;

	echo debut_gauche($nom_page,true);

	bouq_afficher_raccourcis();

	echo '<br />';

	echo debut_cadre_trait_couleur('',true);
	echo '<p>'._T('bouq:informations_importation').'</p>';
	echo fin_cadre_trait_couleur(true);


	echo '<br />';
	bouq_afficher_infos();
  
	echo debut_droite($nom_page,true);
  
	debut_cadre_form_bouq();
		echo catalogues_import_presentation();
		$importer_catalogue = charger_fonction('importer_catalogue', 'inc');
		echo $importer_catalogue(generer_url_ecrire("admin_bouquinerie"), $row); // a modifier
	fin_cadre_form_bouq();

	echo fin_gauche(); fin_page_bouq();

}

function catalogues_import_presentation() {
	echo '<b>'._T('bouq:titre_importation').'</b><br />';
	echo '<hr />';
}

?>
