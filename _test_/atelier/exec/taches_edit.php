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

function exec_taches_edit_dist() {
	exec_taches_edit_args(	intval(_request('id_tache')),
					_request('new'),
					intval(_request('id_projet'))
	);
}

function exec_taches_edit_args($id_tache, $new,$id_projet) {

	$tache_select = charger_fonction('tache_select','inc');
	$row = $tache_select($id_tache? $id_tache : $new);

	tache_edit($id_tache, $new,$id_projet, $row);
}


function tache_edit($id_tache,$new,$id_projet,$row) {

	include_spip('inc/atelier_presentation');
	include_spip('inc/atelier_autoriser');

	$nom_page = atelier_debut_page(_T('atelier:titre_taches_edit'),'taches_edit');
	if (!atelier_autoriser()) exit;

	atelier_debut_gauche($nom_page);

		atelier_cadre_raccourcis();
		atelier_cadre_infos();

	atelier_debut_droite($nom_page);

		atelier_debut_cadre_form();

		echo _T('atelier:modifier_tache') .'&nbsp;:<br />'.gros_titre($row['titre'],'',false);
		$editer_tache = charger_fonction('editer_tache', 'inc');
		echo $editer_tache($new, $id_tache, $row, $id_projet);
		atelier_fin_cadre_form();

	atelier_fin_gauche();
	atelier_fin_page();
}
?>
