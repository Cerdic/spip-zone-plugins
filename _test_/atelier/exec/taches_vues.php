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

function exec_taches_vues_dist() {

	exec_taches_vues_args(intval(_request('id_projet')),
				_request('etat')
	);
}

function exec_taches_vues_args($id_projet,$etat='toutes') {
	$projet_select = charger_fonction('projet_select','inc');
	$row = $projet_select($id_projet);
	if (!$row) {	
		include_spip('inc/minipres');
		echo minipres(_T('atelier:aucun_projet'));
		exit;
	}
	taches_vues($id_projet,$row,$etat);
}

function taches_vues($id_projet,$row,$etat='toutes') {
	include_spip('inc/atelier_presentation');
	include_spip('inc/atelier_autoriser');
	$nom_page = atelier_debut_page(_T('atelier:titre_taches_vues'),'taches_vues');
	if (!atelier_autoriser()) exit;

	atelier_debut_gauche();
		atelier_cadre_raccourcis(array(
			'<a href="'.generer_url_ecrire('projets','id_projet='.$row['id_projet']).'">'._T('atelier:revenir_projet').'</a>'
		));
		atelier_cadre_infos();
	atelier_fin_gauche();
	atelier_debut_droite();
		echo debut_cadre_trait_couleur('',true);
		if ($etat == "fermees") echo liste_taches_fermees($row['id_projet']);
		if ($etat == "toutes") echo liste_taches($row['id_projet']);
		echo fin_cadre_trait_couleur(true);

	atelier_fin_droite();
	atelier_fin_page();
}

function liste_taches_fermees($id_projet) {
	include_spip('inc/afficher_objets');

	$afficher_projets = charger_fonction('afficher_objets','inc');

	$titre = _T('atelier:liste_taches_fermees');
	$requete = array('SELECT' => 'taches.id_tache, taches.titre ',
			 'FROM' => "spip_taches as taches",
			 'WHERE' => "taches.id_projet=$id_projet AND taches.etat='fermee'");
	return $afficher_projets('tache',$titre,$requete);
}
function liste_taches($id_projet) {
	include_spip('inc/afficher_objets');

	$afficher_projets = charger_fonction('afficher_objets','inc');

	$titre = _T('atelier:liste_taches');
	$requete = array('SELECT' => 'taches.id_tache, taches.titre ',
			 'FROM' => "spip_taches as taches",
			 'WHERE' => "taches.id_projet=$id_projet");
	return $afficher_projets('tache',$titre,$requete);
}

