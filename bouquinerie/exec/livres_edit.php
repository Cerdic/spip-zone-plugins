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

function exec_livres_edit_dist() {
	// récupere les paramètres et lance la fonction de traitement des paramètres
	exec_livres_edit_args(	intval(_request('id_livre')), // id du livre a editer
					_request('new') // nouveau ou pas ?
	);
}

function exec_livres_edit_args($id_livre, $new){
	$livre_select = charger_fonction('livre_select','inc');
	$row = $livre_select($id_livre? $id_livre : $new);

	livres_edit($id_livre, $new, $row);
}

function livres_edit($id_livre, $new, $row) {


	include_spip('inc/bouq_presentation');

	$nom_page = debut_page_bouq(_T('bouq:titre_ajouter_livre'),"livres_edit");
                      
	if (!bouq_autoriser()) exit;

	echo debut_gauche($nom_page,true);
	bouq_afficher_raccourcis();
	echo '<br />';
	bouq_afficher_infos();
  
	echo debut_droite($nom_page,true);
  	
	debut_cadre_form_bouq();
		echo livres_edit_presentation($row['titre']);
		$editer_livre = charger_fonction('editer_livre', 'inc');
		echo $editer_livre($new, $id_livre, $row);
	fin_cadre_form_bouq();


	echo fin_gauche(); fin_page_bouq();  
}

function livres_edit_presentation($titre) {
	echo _T('bouq:modifier_livre') . '<br />';
	echo gros_titre($titre,'',false);
}


?>
