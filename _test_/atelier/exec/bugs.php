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

function exec_bugs_dist() {

	exec_bugs_args(intval(_request('id_bug')));
}

function exec_bugs_args($id_bug) {
	$bug_select = charger_fonction('bug_select','inc');
	$row = $bug_select($id_bug);
	if (!$row) {	
		include_spip('inc/minipres');
		echo minipres(_T('atelier:aucun_bug'));
		exit;
	}
	bugs($id_bug,$row);
}

function bugs($id_bug,$row) {

	include_spip('inc/atelier_presentation');
	include_spip('inc/atelier_autoriser');
	include_spip('inc/atelier_fonctions');

	$nom_page = atelier_debut_page(_T('atelier:titre_bugs'),'bugs');
	if (!atelier_autoriser()) exit;

	atelier_debut_gauche($nom_page);
		atelier_cadre_raccourcis(array(
			'<a href="'.generer_url_ecrire('projets','id_projet='.$row['id_projet']).'">'._T('atelier:revenir_projet').'</a>'
		));

		cadre_atelier(_T('atelier:action'),array(
			'<a href="'.generer_url_ecrire('bugs_edit','id_bug='.$row['id_bug']).'">'._T('atelier:modifier_bug').'</a>',
			'<a href="'.generer_url_action('bug_tache','id_bug='.$row['id_bug']).'">'._T('atelier:transformer_bug_tache').'</a>',
		));

		atelier_cadre_infos();

	atelier_debut_droite($nom_page);

		echo debut_cadre_trait_couleur('',true);
		echo gros_titre($row['id_bug'].' - '. $row['titre'],'',false);

		echo debut_cadre_couleur('',true);

		echo '<b>'._T('atelier:texte_descriptif').' :</b><br />'
			.propre($row['descriptif']);

		echo fin_cadre_couleur(true);

		echo fin_cadre_trait_couleur(true);

	atelier_fin_gauche();
	atelier_fin_page();
}
?>
