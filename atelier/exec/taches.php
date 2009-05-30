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

function exec_taches_dist() {

	exec_taches_args(intval(_request('id_tache')));
}

function exec_taches_args($id_tache) {
	$tache_select = charger_fonction('tache_select','inc');
	$row = $tache_select($id_tache);
	if (!$row) {	
		include_spip('inc/minipres');
		echo minipres(_T('atelier:aucune_tache'));
		exit;
	}
	taches($id_tache,$row);
}

function taches($id_tache,$row) {

	include_spip('inc/atelier_presentation');
	include_spip('inc/atelier_autoriser');
	include_spip('inc/atelier_fonctions');

	$nom_page = atelier_debut_page(_T('atelier:titre_taches'),'taches');
	if (!atelier_autoriser()) exit;

	atelier_debut_gauche();
		atelier_cadre_raccourcis(array(
			'<a href="'.generer_url_ecrire('projets','id_projet='.$row['id_projet']).'">'._T('atelier:revenir_projet').'</a>',
			'<a href="'.generer_url_ecrire('atelier_roadmap','id_projet='.$row['id_projet']).'">'._T('atelier:voir_feuille_de_route').'</a>'
		));

		cadre_atelier(_T('atelier:action'),array(
			'<a href="'.generer_url_ecrire('taches_edit','id_tache='.$row['id_tache']).'">'._T('atelier:modifier_tache').'</a>'
		));

		atelier_cadre_infos();

	atelier_fin_gauche();
	atelier_debut_droite();

		echo debut_cadre_trait_couleur('',true);
		echo gros_titre($row['id_tache'].' - '. $row['titre'],'',false);
		echo '<b>'._T('atelier:texte_etat').' : </b>'._T('atelier:tache').' '.$row['etat'].'<br />';
		echo '<b>'._T('atelier:urgence').' : </b> '.$row['urgence'].'<br />';

		$nom = atelier_recuperer_nom_auteur($row['id_auteur']);
		if (!$nom) $nom = 'personne';
		echo 'Tache assignée à '. $nom . '<br />';
		echo 'Dû pour la version '. $row['version'] .'<br /><br />';

		echo debut_cadre_couleur('',true);

		echo '<b>'._T('atelier:texte_descriptif').' :</b><br />'
			.propre($row['descriptif']);

		echo fin_cadre_couleur(true);



		echo fin_cadre_trait_couleur(true);

	atelier_fin_droite();
	atelier_fin_page();
}
?>
