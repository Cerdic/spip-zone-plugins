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

function exec_atelier_dist() {

	exec_atelier_args(	intval(_request('id_projet')) // id du projet par defaut
	);
}

function exec_atelier_args($id_projet) {

	$defaut = "defaut";
	// recupere les données sur le projet à afficher
	$projet_select = charger_fonction('projet_select','inc');
	$row = $projet_select($id_projet?$id_projet:$defaut);

	atelier($id_projet,$row,$defaut);
}

function atelier($id_projet,$row,$defaut) {

	include_spip('inc/atelier_presentation');
	include_spip('inc/atelier_autoriser');

	$nom_page = atelier_debut_page(_T('atelier:page_principale'),'atelier');
	if (!atelier_autoriser()) exit;

	// verifier si les bases existent
	include_spip('inc/atelier_installer_base');
	$verifier_base = atelier_verifier_base();


	atelier_debut_gauche($nom_page);

		if ($verifier_base) {
			cadre_atelier(_T('atelier:projets'), array(
				'<a href="'.generer_url_ecrire('projets_edit','new=oui').'">'._T('atelier:nouveau_projet').'</a>',
				'<a href="'.generer_url_ecrire('atelier_metas').'">'._T('atelier:voir_metas').'</a>'
			));
		}

		atelier_cadre_fichiers_temp();

		cadre_atelier(_T('atelier:administration'), array(
			'<a href="'.generer_url_ecrire('supprimer_atelier').'">'._T('atelier:supprimer_atelier').'</a>'
		));

		atelier_cadre_infos();
 
	atelier_debut_droite($nom_page);

	echo debut_cadre_trait_couleur('',true);
	echo '<p>'._T('atelier:presentation').'</p>';
	echo fin_cadre_trait_couleur(true);

	if (!$verifier_base) {
		echo debut_boite_alerte();
		echo '<p>'._T('atelier:installer_base').'</p>';
		$installer_base = charger_fonction('atelier_installer_base','inc');
		echo $installer_base();
		echo fin_boite_alerte();
	}



	echo liste_projets();

	atelier_fin_gauche();
	atelier_fin_page();  
}

function liste_projets() {
	include_spip('inc/afficher_objets');

	$afficher_projets = charger_fonction('afficher_objets','inc');

	$titre = _T('atelier:liste_projets');
	$requete = array('SELECT' => 'projets.id_projet, projets.titre ',
			 'FROM' => "spip_projets as projets");
	return $afficher_projets('projet',$titre,$requete);
}

?>
