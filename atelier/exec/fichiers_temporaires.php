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

function exec_fichiers_temporaires_dist() {
	fichiers_temporaires_args(_request('fichier'));
}

function fichiers_temporaires_args($fichier) {

	fichiers_temporaires($fichier);
	
}

function fichiers_temporaires($fichier) {

	include_spip('inc/atelier_presentation');
	include_spip('inc/atelier_autoriser');

	$nom_page = atelier_debut_page(_T('atelier:titre_fichiers_temporaires'),'fichiers_temporaires');
	if (!atelier_autoriser()) exit;

	atelier_debut_gauche($nom_page);

		atelier_cadre_raccourcis();
		atelier_cadre_fichiers_temp();
		atelier_cadre_infos();

	atelier_debut_droite($nom_page);	

	echo debut_cadre_formulaire('',true);
	$contenu = '';
	echo '<b>'.$fichier.'</b>';
	echo '<hr />';
	lire_fichier(_DIR_TMP.$fichier,&$contenu);
	echo '<textarea readonly cols="60" rows="30">'.$contenu.'</textarea>';
	echo '<hr />';
	include_spip('inc/supprimer_fichier');
	$supprimer_fichier = charger_fonction('supprimer_fichier','inc');
	echo $supprimer_fichier($fichier);
	echo fin_cadre_formulaire(true);

	atelier_fin_gauche();
	atelier_fin_page();
}
?>
