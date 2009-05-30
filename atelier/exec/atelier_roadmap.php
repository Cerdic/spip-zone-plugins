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

function exec_atelier_roadmap_dist() {
	exec_atelier_roadmap_args(intval(_request('id_projet')));
}

function exec_atelier_roadmap_args($id_projet) {
	include_spip('inc/atelier_fonctions');

	if (!$id_projet || $id_projet == 0) {	
		include_spip('inc/minipres');
		echo minipres(_T('atelier:aucun_projet'));
		exit;
	}

	$versions = atelier_recuperer_versions($id_projet);


	atelier_roadmap($id_projet,$versions);
}

function atelier_roadmap($id_projet,$versions) {
	include_spip('inc/atelier_presentation');
	include_spip('inc/atelier_autoriser');

	$nom_page = atelier_debut_page(_T('atelier:titre_roadmap'),'atelier_roadmap');
	if (!atelier_autoriser()) exit;

	atelier_debut_gauche();

		atelier_cadre_raccourcis(array(
			'<a href="'.generer_url_ecrire('projets','id_projet='.$id_projet).'">'._T('atelier:revenir_projet').'</a>'
		));

		cadre_atelier(_T('atelier:action'),array(
			'<a href="'.generer_url_ecrire('atelier_versions','id_projet='.$id_projet).'">'._T('atelier:gerer_versions').'</a>',
		));

		atelier_cadre_infos();

	atelier_fin_gauche();
	atelier_debut_droite();

		$contenu = array();

		if ($versions) {
			foreach($versions as $version){
				$pourcentage = 0;
				$ouvertes = count(atelier_recuperer_taches_ouvertes($id_projet,$version));
				$fermees = count(atelier_recuperer_taches_fermees($id_projet,$version));
				if ($fermees != 0) $pourcentage = intval(($fermees * 100) / ($ouvertes + $fermees));
				$contenu["version $version, $ouvertes taches ouvertes, $fermees taches fermées, $pourcentage %"] = atelier_recuperer_taches($id_projet,$version);
			}
		}

		atelier_cadre_couleur(_T('atelier:feuille_de_route'),$contenu);

	atelier_fin_droite();
	atelier_fin_page();
}
