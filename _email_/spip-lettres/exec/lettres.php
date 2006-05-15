<?php

	include_spip('inc/lettres_fonctions');
	include_spip('inc/lettres_admin');
 	include_spip('inc/presentation');


	/**
	 * exec_lettres
	 *
	 * Tableau de bord du plugin
	 *
	 * @author Pierre Basson
	 **/
	function exec_lettres() {

		lettres_verifier_droits();

		$recherche = $_POST['recherche'];


		debut_page(_T('lettres:lettres_information'), "lettres", "lettres");


		debut_gauche();

		lettres_afficher_statistiques_globales();
		lettres_afficher_recherche('lettres');

		debut_raccourcis();
		lettres_afficher_raccourci_creer_lettre();
		lettres_afficher_raccourci_liste_abonnes(_T('lettres:aller_liste_abonnes'));
		lettres_afficher_raccourci_ajouter_abonne();
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
		lettres_afficher_lettres(_T('lettres:lettres_envoi_en_cours'), _DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/lettre-envoi-en-cours.png', 'envoi_en_cours', $recherche, 'position_envoi_en_cours');
		lettres_afficher_lettres(_T('lettres:lettres_brouillon'), _DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/lettre-brouillon.png', 'brouillon', $recherche, 'position_brouillon');
		lettres_afficher_lettres(_T('lettres:lettres_publiees'), _DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/lettre-publiee.png', 'publie', $recherche, 'position_publie');

		fin_page();

	}


?>