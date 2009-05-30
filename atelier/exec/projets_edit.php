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

function exec_projets_edit_dist() {
	exec_projets_edit_args(	intval(_request('id_projet')),
					_request('new')
	);
}

function exec_projets_edit_args($id_projet, $new) {

	$projet_select = charger_fonction('projet_select','inc');
	$row = $projet_select($id_projet? $id_projet : $new);

	projet_edit($id_projet, $new, $row);
}


function projet_edit($id_projet,$new,$row) {

	include_spip('inc/atelier_presentation');
	include_spip('inc/atelier_autoriser');

	$nom_page = atelier_debut_page(_T('atelier:titre_projets_edit'),'projets_edit');
	if (!atelier_autoriser()) exit;

	atelier_debut_gauche();

		atelier_cadre_raccourcis(array(
			'<a href="'.generer_url_ecrire('projets','id_projet='.$row['id_projet']).'">'._T('atelier:revenir_projet').'</a>'
		));
		atelier_cadre_infos();

	atelier_fin_gauche();
	atelier_debut_droite();

		atelier_debut_cadre_form();
		echo _T('atelier:modifier_projet') .'&nbsp;:<br />'.gros_titre($row['titre'],'',false);
		$editer_projet = charger_fonction('editer_projet', 'inc');
		echo $editer_projet($new, $id_projet, $row);
		atelier_fin_cadre_form();

	atelier_fin_droite();
	atelier_fin_page();
}
?>
