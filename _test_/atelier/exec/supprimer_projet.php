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

function exec_supprimer_projet_dist() {

	exec_supprimer_projet_args(intval(_request('id_projet')));
}

function exec_supprimer_projet_args($id_projet) {

	supprimer_projet($id_projet);
}

function supprimer_projet($id_projet) {
	include_spip('inc/atelier_presentation');
	include_spip('inc/atelier_autoriser');

	$nom_page = atelier_debut_page(_T('atelier:titre_supprimer_projet'),'supprimer_projet');
	if (!atelier_autoriser()) exit;

	atelier_debut_gauche();

		atelier_cadre_raccourcis(array(
			'<a href="'.generer_url_ecrire('projets','id_projet='.$id_projet).'">'._T('atelier:retour_projet').'</a>'
		));

		atelier_cadre_infos();

	atelier_fin_gauche();
	atelier_debut_droite();

		echo debut_boite_alerte();
		echo '<p>'._T('atelier:prevenir_suppression_projet').'</p>';
		include_spip('inc/supprimer_projet');
		$supprimer_projet = charger_fonction('supprimer_projet','inc');
		echo $supprimer_projet($id_projet);
		echo fin_boite_alerte();

	atelier_fin_droite();
	atelier_fin_page();
}

?>
