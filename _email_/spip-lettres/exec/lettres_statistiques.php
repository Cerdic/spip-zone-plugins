<?php

	include_spip('inc/lettres_fonctions');
	include_spip('inc/lettres_admin');
 	include_spip('inc/presentation');


	/**
	 * exec_lettres_statistiques
	 *
	 * Statistiques
	 *
	 * @author Pierre Basson
	 **/
	function exec_lettres_statistiques() {

		lettres_verifier_droits();

		debut_page(_T('lettres:statistiques'), "lettres", "lettres_statistiques");


		debut_gauche();

		lettres_afficher_statistiques_globales();

		debut_raccourcis();
		lettres_afficher_raccourci_liste_abonnes(_T('lettres:aller_liste_abonnes'));
		lettres_afficher_raccourci_liste_lettres(_T('lettres:aller_liste_lettres'));
		fin_raccourcis();


    	debut_droite();
		echo "<br />";

		fin_page();

	}


?>