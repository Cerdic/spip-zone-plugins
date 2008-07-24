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

function exec_catalogues_edit_dist() {
	
	// récupere les paramètres et lance la fonction de traitement des paramètres
	exec_catalogues_edit_args(	intval(_request('id_catalogue')), // id du catalogue a editer
					_request('new') // nouveau ou pas ?
	);
}

function exec_catalogues_edit_args($id_catalogue, $new){

	// récupére les données sur le catalogue à editer
	$catalogue_select = charger_fonction('catalogue_select','inc');
	$row = $catalogue_select($id_catalogue? $id_catalogue : $new);

	// lance l'édition du catalogue
	catalogues_edit($id_catalogue, $new, $row);
}

function catalogues_edit($id_catalogue, $new, $row) {

	include_spip('inc/bouq_presentation');

	if ($new) $nom_page = debut_page_bouq(_T('bouq:titre_ajouter_catalogue'),"catalogues_edit");
	else $nom_page = debut_page_bouq(_T('bouq:titre_modifier_catalogue'),"catalogues_edit");
                      
	if (!bouq_autoriser()) exit;

	echo debut_gauche($nom_page,true); // SPIP 2.0

	bouq_afficher_raccourcis();

	echo '<br />';
	bouq_afficher_infos();

  
	echo debut_droite($nom_page,true);
  
	debut_cadre_form_bouq();
		echo catalogues_edit_presentation($row['titre']);
		$editer_catalogue = charger_fonction('editer_catalogue', 'inc');
		echo $editer_catalogue($new, $id_catalogue, generer_url_ecrire("admin_bouquinerie"), $row);
	fin_cadre_form_bouq();

	echo fin_gauche(); fin_page_bouq();  
}

function catalogues_edit_presentation($titre) {
	echo _T('bouq:modifier_catalogue') . '<br />';
	echo gros_titre($titre,'',false);
}

?>
