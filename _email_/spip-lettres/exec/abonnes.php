<?php


	/**
	 * SPIP-Lettres : plugin de gestion de lettres d'information
	 *
	 * Copyright (c) 2006
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/


	include_spip('inc/lettres_fonctions');
	include_spip('inc/lettres_admin');


	function exec_abonnes() {
		global $couleur_foncee,$tri,$debut;
	 	include_spip('inc/presentation');
		
		lettres_verifier_droits();


		$recherche = $_POST['recherche'];


		debut_page(_T('lettres:abonnes'), "lettres", "abonnes");


		debut_gauche();
		
		lettres_afficher_statistiques_globales();
		lettres_afficher_recherche('abonnes');
		
		debut_raccourcis();
		lettres_afficher_raccourci_liste_lettres(_T('lettres:aller_liste_lettres'));
		if (isset($_POST['recherche']))
			lettres_afficher_raccourci_liste_abonnes(_T('lettres:retour_liste'));
		lettres_afficher_raccourci_ajouter_abonne();
		lettres_afficher_raccourci_import_csv();
		lettres_afficher_raccourci_formulaire_inscription();
		lettres_afficher_raccourci_statistiques();
		lettres_afficher_raccourci_configurer_plugin();
		fin_raccourcis();


    	debut_droite();
		echo "<br />";

		if (isset($_POST['recherche'])) {
			gros_titre(_T('lettres:resultat_recherche').' "'.$recherche.'"');
			echo '<br />';
		}
		echo lettres_afficher_abonnes(_T('lettres:abonnes_a_valider'), _DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/abonnes.png', '= "a_valider"', $recherche, 0, 'abonnes', '', 'position_abonnes_a_valider');
		echo lettres_afficher_abonnes(_T('lettres:abonnes_valides'), _DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/abonnes.png', '= "valide"', $recherche, 0, 'abonnes', '', 'position_abonnes_valides');
		echo lettres_afficher_abonnes(_T('lettres:abonnes_non_inscrits'), _DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/abonnes.png', 'IS NULL', $recherche, 0, 'abonnes', '', 'position_abonnes_orphelins');

		echo "<br />";

		fin_page();

	}		


?>