<?php


	/**
	 * SPIP-Formulaires
	 *
	 * @copyright 2006-2007 Artégo
	 **/


 	include_spip('inc/presentation');
	include_spip('formulaires_fonctions');
	include_spip('inc/headers');


	/**
	 * exec_formulaires_tous
	 *
	 * Tableau de bord du plugin
	 *
	 * @author Pierre Basson
	 **/
	function exec_formulaires_tous() {

		if ($GLOBALS['connect_statut'] != "0minirezo") {
			echo _T('avis_non_acces_page');
			echo fin_page();
			exit;
		}

		pipeline('exec_init',array('args'=>array('exec'=>'formulaires_tous'),'data'=>''));

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('formulairesprive:formulaires'), "naviguer", "formulaires_tous");

		debut_gauche();
		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'formulaires_tous'),'data'=>''));

		debut_raccourcis();
		formulaires_afficher_raccourci_creer_formulaire();
		fin_raccourcis();

		creer_colonne_droite();
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'formulaires_tous'),'data'=>''));

    	debut_droite();
		echo formulaires_afficher_formulaires(_T('formulairesprive:formulaires_hors_ligne'), array("FROM" => 'spip_formulaires', "WHERE" => 'en_ligne="non"', 'ORDER BY' => "maj DESC"));
		echo formulaires_afficher_formulaires(_T('formulairesprive:formulaires_en_attente'), array("FROM" => 'spip_formulaires', "WHERE" => 'en_ligne="oui" AND limiter_temps="oui" AND statut="en_attente"', 'ORDER BY' => "maj DESC"));
		echo formulaires_afficher_formulaires(_T('formulairesprive:formulaires_publies'), array("FROM" => 'spip_formulaires', "WHERE" => 'en_ligne="oui" AND statut="publie"', 'ORDER BY' => "maj DESC"));
		echo formulaires_afficher_formulaires(_T('formulairesprive:formulaires_termines'), array("FROM" => 'spip_formulaires', "WHERE" => 'en_ligne="oui" AND limiter_temps="oui" AND statut="termine"', 'ORDER BY' => "maj DESC"));

		echo fin_gauche();

		echo fin_page();

	}


?>