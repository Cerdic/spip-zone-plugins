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

include_spip('inc/bouq_presentation');
include_spip('inc/bouq');

charger_generer_url();

function exec_admin_bouquinerie_dist() {
	exec_admin_bouquinerie_args(_request('rapport'));
}


function exec_admin_bouquinerie_args($rapport='') {

/*	include_spip('base/create');
	include_spip('base/abstract_sql');
	creer_base();*/

	admin_bouquinerie($rapport);
}


function admin_bouquinerie($rapport = '') {
    
	$nom_page = debut_page_bouq(_T('bouq:titre_admin'),"admin_bouquinerie");
	if (!bouq_autoriser()) exit;
        
	// vérification si la base de donnée est installée  
	if (!bouq_verifier_base()) {
		include_spip('inc/installer_base');
		$installer_base = charger_fonction('installer_base','inc');
		echo debut_gauche($nom_page,true); // SPIP 2.0

		echo debut_boite_alerte();
		echo '<p>'._T('bouq:installer_base_expliq').'</p>';
		echo fin_boite_alerte();

		echo debut_droite($nom_page,true); // SPIP 2.0
		debut_cadre_form_bouq();
			echo '<p>'._T('bouq:installer_base_expliq2').'</p>';
			echo $installer_base();
		fin_cadre_form_bouq();
	}
	else {

		$catalogue = bouq_verifier_catalogue();
		$livres = bouq_verifier_livres();
		$livres_orph = bouq_verifier_livres_orphelins();
		$menu_livre = array();

		echo debut_gauche($nom_page,true); // SPIP 2.0

		if (!$catalogue)
			cadre_gauche_bouq('Catalogues',array(
				'<a href="'.generer_url_ecrire("catalogues_edit","new=oui").'">'._T('bouq:ajouter_catalogue').'</a>',
				'<a href="'.generer_url_ecrire("catalogues_import").'">'._T('bouq:importer_catalogue').'</a>'
			));
		else {
			cadre_gauche_bouq('Catalogues',array(
				'<a href="'.generer_url_ecrire("catalogues_edit","new=oui").'">'._T('bouq:ajouter_catalogue').'</a>',
				'<a href="'.generer_url_ecrire("catalogues_import").'">'._T('bouq:importer_catalogue').'</a>',
				'<a href="'.generer_url_ecrire("catalogues_export").'">'._T('bouq:exporter_catalogue').'</a>'
			));
			$menu_livre[] =	'<a href="'.generer_url_ecrire("livres_edit","new=oui").'">'._T('bouq:ajouter_livre').'</a>';
			if ($livres) {
				$menu_livre[] =	'<a href="'.generer_url_ecrire("rechercher_livres").'">'._T('bouq:rechercher_livre').'</a>';
				$menu_livre[] =	'<a href="'.generer_url_ecrire("rechercher_doublons").'">'._T('bouq:rechercher_doublons').'</a>';
			}
			echo '<br />';

			cadre_gauche_bouq('Livres',$menu_livre);
		}

		echo '<br />';
		bouq_afficher_admin();
 
		echo '<br />';

		bouq_afficher_infos();

  
		echo debut_droite($nom_page,true); // SPIP 2.0

		if ($rapport) {
			echo debut_cadre_trait_couleur('',true);
			echo '<p>'.$rapport.'</p>';
			echo fin_cadre_trait_couleur(true);
		}
	 
		if ($livres) {
			echo debut_cadre("e", "", "",_T('bouq:titre_liste_livre'));
			echo liste_livres();
			echo fin_cadre("e");

			echo "<br />";
		}
  
		echo debut_cadre("e", "", "", _T('bouq:liste_catalogue'));
		if (!$catalogue) echo _T('bouq:texte_pas_catalogue');
		else if ($livres_orph) {
			echo '<p>'._T('bouq:livres_orphelins').'</p>';
			include_spip('inc/livres_orphelins');
			$livres_orphelins = charger_fonction('livres_orphelins','inc');
			echo '<p>'.$livres_orphelins().'</p><br /><hr />';
			echo liste_catalogues();
		}
		else echo liste_catalogues();
		echo fin_cadre("e");


	}  
	echo fin_gauche(); 
	fin_page_bouq();
}

function liste_livres() {

	include_spip('inc/afficher_livres');

	$afficher_livres = charger_fonction('afficher_livres','inc');
	$titre = _T('bouq:liste_livre');
	$requete = array('SELECT' => 'livres.id_livre, livres.titre ',
			 'FROM' => "spip_livres as livres");

	return $afficher_livres($titre,$requete);
}

function liste_catalogues() {

	include_spip('inc/afficher_catalogues');
	$afficher_catalogues = charger_fonction('afficher_catalogues','inc');


	$titre = _T('bouq:liste_catalogues');
	$requete = array('SELECT' => 'catalogues.id_catalogue, catalogues.titre ',
			 'FROM' => "spip_catalogues as catalogues");

	return $afficher_catalogues($titre,$requete);
}

?>
