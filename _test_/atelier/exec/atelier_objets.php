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

function exec_atelier_objets_dist() {

	exec_atelier_objets_args(intval(_request('id_projet')),
				_request('rapport'),
				_request('opendir')
	);
}

function exec_atelier_objets_args($id_projet,$rapport='',$opendir='') {
	$projet_select = charger_fonction('projet_select','inc');
	$row = $projet_select($id_projet);

	atelier_objets($id_projet,$row,$rapport,$opendir);
}

function atelier_objets($id_projet,$row,$rapport='',$opendir='') {
	include_spip('inc/atelier_presentation');
	include_spip('inc/atelier_autoriser');
	$nom_page = atelier_debut_page(_T('atelier:titre_objets'),'atelier_objets');
	if (!atelier_autoriser()) exit;

	atelier_debut_gauche($nom_page);

		atelier_cadre_raccourcis(array(
			'<a href="'.generer_url_ecrire('projets','id_projet='.$row['id_projet']).'">'._T('atelier:revenir_projet').'</a>'
		));
                cadre_atelier('Gabarits',array(
                '<a href="'.generer_url_ecrire('atelier_edit_fichier','fichier='._DIR_PLUGINS.'atelier/gabarits/exec.txt&mode=absolu&id_projet='.$row['id_projet']).'">'._T('atelier:editer_exec_txt').'</a>',
                '<a href="'.generer_url_ecrire('atelier_edit_fichier','fichier='._DIR_PLUGINS.'atelier/gabarits/inc.txt&mode=absolu&id_projet='.$row['id_projet']).'">'._T('atelier:editer_inc_txt').'</a>',
                '<a href="'.generer_url_ecrire('atelier_edit_fichier','fichier='._DIR_PLUGINS.'atelier/gabarits/action.txt&mode=absolu&id_projet='.$row['id_projet']).'">'._T('atelier:editer_action_txt').'</a>'
                ));

		atelier_cadre_infos();

	atelier_debut_droite($nom_page);

		echo debut_cadre_trait_couleur('',true);
		echo '<p>'._T('atelier:explication_objets').'. Pour les objets de l\'espace privé,  votre objet aura pour nom : prefixe_nomobjet_[exec|inc|action]. Son fichier sera : [exec|inc|action]/prefixe_nomobjet.php</p>';
		echo fin_cadre_trait_couleur(true);
		echo debut_cadre_formulaire('',true);
		include_spip('inc/atelier_objets.php');
		$atelier_objets = charger_fonction('atelier_objets','inc');
		echo $atelier_objets(array('id_projet' => $id_projet,
					 'type' => $row['type']));

		include_spip('inc/atelier_explorer');
		atelier_explorer($row['prefixe'],$id_projet,$row['type'],$opendir,$nom_page);

		echo fin_cadre_formulaire(true);

	atelier_fin_gauche();
	atelier_fin_page();
}

?>
