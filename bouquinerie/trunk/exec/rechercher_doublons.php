<?php

/*
 *  Plugin Bouquinerie pour SPIP
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

function exec_rechercher_doublons_dist() {

	exec_rechercher_doublons_args(_request('par'));
}

function exec_rechercher_doublons_args($par) {

	$rechercher_doublons = charger_fonction('rechercher_doublons','inc');
	$row = $rechercher_doublons($par);

	rechercher_doublons($row,$par);
}

function rechercher_doublons($row,$par) {

	include_spip('inc/bouq_presentation');

	$nom_page = debut_page_bouq(_T('bouq:titre_rechercher_doublons'),"rechercher_doublons");
	if (!bouq_autoriser()) exit;

	echo debut_gauche($nom_page,true);
	bouq_afficher_raccourcis();
	echo '<br />';

	echo debut_cadre_trait_couleur('',true);
	echo '<p>'._T('bouq:explication_doublons').'</p>';
	echo '<a href="'.generer_url_ecrire('rechercher_doublons').'">'._T('bouq:liste_recherche').'</a></li>';
	echo fin_cadre_trait_couleur(true);

	echo '<br />';
	bouq_afficher_infos();

	echo debut_droite($nom_page,true);

	if (count($row['rows'])) {

		echo debut_cadre_trait_couleur('',true,'','Par : '.$row['titre']);
		echo debut_liste();
		foreach($row['rows'] as $doublon) {
			foreach ($doublon['livres'] as $livre) {
				echo liste_ligne('<b>N° '.$livre['id_livre'] .'</b>'
						.'&nbsp;-&nbsp;<a href="'.generer_url_ecrire('livres','id_livre='.$livre['id_livre']).'">'
						.$livre['titre'].'</a><br />'
						.'<span style="float:right">'.$par.' : '.$livre[$par].'</span>');

			}
			echo liste_ligne(false);
		}
		echo fin_liste();	

		echo fin_cadre_trait_couleur(true);
		echo '<br />';
	}
	else {
		echo debut_cadre_trait_couleur('',true);
		echo '<p>'._T('bouq:doublons_choix_type_recherche').'</p>';
		echo "<ul>";
		echo '<li><a href="'.generer_url_ecrire('rechercher_doublons','par=titre').'">'._T('bouq:par_titre').'</a></li>';
		echo '<li><a href="'.generer_url_ecrire('rechercher_doublons','par=isbn').'">'._T('bouq:par_isbn').'</a></li>';
		echo '<li><a href="'.generer_url_ecrire('rechercher_doublons','par=auteur').'">'._T('bouq:par_auteur').'</a></li>';
		echo '<li><a href="'.generer_url_ecrire('rechercher_doublons','par=edition').'">'._T('bouq:par_edition').'</a></li>';
		echo "</ul>";
		echo '<p>'._T('bouq:attention_champs_vide').'</p>';
		echo '<p>'._T('bouq:attention_temps').'</p>';
		echo fin_cadre_trait_couleur(true);
	}

	echo fin_gauche(); fin_page_bouq();
}

?>
