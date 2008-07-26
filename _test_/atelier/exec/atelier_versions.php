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

function exec_atelier_versions_dist() {
	exec_atelier_versions_args(intval(_request('id_projet')));
}

function exec_atelier_versions_args($id_projet) {
	include_spip('inc/atelier_fonctions');
	$versions = atelier_recuperer_versions($id_projet);

	atelier_versions($id_projet,$versions);
}

function atelier_versions($id_projet,$versions) {

	include_spip('inc/atelier_presentation');
	include_spip('inc/atelier_autoriser');

	$nom_page = atelier_debut_page(_T('atelier:titre_versions'),'atelier_versions');
	if (!atelier_autoriser()) exit;

	atelier_debut_gauche($nom_page);

		atelier_cadre_raccourcis(array(
			'<a href="'.generer_url_ecrire('projets','id_projet='.$id_projet).'">'._T('atelier:revenir_projet').'</a>',
			'<a href="'.generer_url_ecrire('atelier_roadmap','id_projet='.$id_projet).'">'._T('atelier:revenir_roadmap').'</a>'
		));

		atelier_cadre_infos();

	atelier_debut_droite($nom_page);

		$contenu = array();

		if (count($versions)>0) cadre_atelier('liste des versions pour le projet '.$id_projet,$versions);
		$ajouter_version = charger_fonction('atelier_versions','inc');
		echo $ajouter_version($id_projet);


	atelier_fin_gauche();
	atelier_fin_page();
}

?>
