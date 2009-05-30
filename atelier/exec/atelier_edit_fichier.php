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

function exec_atelier_edit_fichier_dist() {

	exec_atelier_edit_fichier_args( _request('fichier'),
                                                   _request('mode'),
			 	intval(_request('id_projet')));
}

function exec_atelier_edit_fichier_args( $fichier,$mode, $id_projet ) {

	$projet_select = charger_fonction('projet_select','inc');
	$row = $projet_select($id_projet);

	atelier_edit_fichier($fichier, $mode, $id_projet, $row);
}

function atelier_edit_fichier($fichier, $mode, $id_projet,$row ) {

	include_spip('inc/atelier_presentation');
	include_spip('inc/atelier_autoriser');

	$nom_page = atelier_debut_page(_T('atelier:titre_edit_fichier'),'edit_file');
	if (!atelier_autoriser()) exit;

	atelier_debut_gauche($nom_page);
		atelier_cadre_raccourcis(array(
			'<a href="'.generer_url_ecrire('projets','id_projet='.$row['id_projet']).'">'._T('atelier:revenir_projet').'</a>'
		));

	/*	cadre_atelier(_T('atelier:action'),array(
			'<a href="'.generer_url_ecrire('taches_edit','id_tache='.$row['id_tache']).'">'._T('atelier:modifier_tache').'</a>'
		));*/

		atelier_cadre_infos();

	atelier_debut_droite($nom_page);

		echo debut_cadre_trait_couleur('',true);
		echo gros_titre($fichier,'',false);

		$formulaire_edit = charger_fonction('atelier_edit_fichier','inc');
		echo $formulaire_edit(array('id_projet' => $id_projet, 'fichier' => $fichier, 'prefixe' => $row['prefixe'],'mode' => $mode));

		echo fin_cadre_trait_couleur(true);

	atelier_fin_gauche();
	atelier_fin_page();
}

?>
