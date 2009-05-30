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

function exec_rechercher_livres_dist() {

	exec_rechercher_livres_args(
				_request('titre'),
				_request('auteur'),
				_request('illustrateur'),
				_request('edition'),
				_request('prix_vente'),
				_request('isbn')
				);
}

function exec_rechercher_livres_args($titre='',$auteur='',$illustrateur='',$edition='',$prix_vente='',$isbn='') {
	$rechercher_livres = charger_fonction('rechercher_livres','inc');
	$livres = $rechercher_livres($titre,$auteur,$illustrateur,$edition,$prix_vente,$isbn);

	rechercher_livres($livres,$titre,$auteur,$illustrateur,$edition,$prix_vente,$isbn);
}


function rechercher_livres($livres,$titre='',$auteur='',$illustrateur='',$edition='',$prix_vente='',$isbn='') {

	include_spip('inc/bouq_presentation');

	$nom_page = debut_page_bouq(_T('bouq:titre_rechercher_livres'),"rechercher_livres");
	if (!bouq_autoriser()) exit;

	echo debut_gauche($nom_page,true);

	bouq_afficher_raccourcis();

	echo '<br />';

	echo debut_cadre_trait_couleur('',true);
	echo _T('bouq:infos_generiques');
	echo fin_cadre_trait_couleur(true);

	echo '<br />';
	bouq_afficher_infos();

	echo debut_droite($nom_page,true);

	debut_cadre_form_bouq();

	$rechercher_livre = charger_fonction('rechercher_livre', 'inc');
	echo $rechercher_livre($titre,$auteur,$illustrateur,$edition,$prix_vente,$isbn);
	echo fin_cadre_formulaire(true);

	if (count($livres)) {

		echo '<br />';
		echo debut_cadre_trait_couleur('',true,'',_T('bouq:resultats'));
		echo debut_liste();
		foreach ($livres as $livre) {
			echo liste_ligne('<a href="'.generer_url_ecrire("livres",'id_livre='.$livre['id_livre']).'">'.$livre['titre'].'</a>');
		}
		echo fin_liste();
		echo fin_cadre_trait_couleur(true);

	}
	echo fin_cadre_trait_couleur(true);

	echo fin_gauche();
	fin_page_bouq();
}

?>
