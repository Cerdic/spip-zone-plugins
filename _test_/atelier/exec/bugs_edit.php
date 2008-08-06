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

function exec_bugs_edit_dist() {
	exec_bugs_edit_args(	intval(_request('id_bug')),
					_request('new')
	);
}

function exec_bugs_edit_args($id_bug, $new) {

	$bug_select = charger_fonction('bug_select','inc');
	$row = $bug_select($id_bug? $id_bug : $new);

	bugs_edit($id_bug, $new, $row);
}


function bugs_edit($id_bug,$new,$row) {

	include_spip('inc/atelier_presentation');
	include_spip('inc/atelier_autoriser');

	$nom_page = atelier_debut_page(_T('atelier:titre_bugs_edit'),'bugs_edit');
	if (!atelier_autoriser()) exit;

	atelier_debut_gauche($nom_page);

		atelier_cadre_raccourcis();
		atelier_cadre_infos();

	atelier_debut_droite($nom_page);

		atelier_debut_cadre_form();
		echo _T('atelier:modifier_bug') .'&nbsp;:<br />'.gros_titre($row['titre'],'',false);
		$editer_bug = charger_fonction('editer_bug', 'inc');
		echo $editer_bug($new, $id_bug, $row);
		atelier_fin_cadre_form();

	atelier_fin_gauche();
	atelier_fin_page();
}
?>
