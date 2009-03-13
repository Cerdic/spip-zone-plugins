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
	 * exec_applications_tous
	 *
	 * @author Pierre Basson
	 **/
	function exec_applications_tous() {

		if ($GLOBALS['connect_statut'] != "0minirezo") {
			echo _T('avis_non_acces_page');
			echo fin_page();
			exit;
		}

		pipeline('exec_init',array('args'=>array('exec'=>'applications_tous'),'data'=>''));

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('formulairesprive:formulaires'), "naviguer", "applications_tous");

		debut_gauche();

		debut_raccourcis();
		echo icone_horizontale(_T('formulairesprive:aller_liste_formulaires'), generer_url_ecrire("formulaires_tous"), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/formulaire-24.png', "", '');
		fin_raccourcis();

    	debut_droite();
		echo formulaires_afficher_applicants(_T('formulairesprive:liste_applicants'), array("FROM" => 'spip_applicants', "WHERE" => 'email!=""', 'ORDER BY' => "maj DESC"));
		echo formulaires_afficher_applications(_T('formulairesprive:liste_applications'), array("SELECT" => "spip_applications.*", "FROM" => 'spip_applications, spip_applicants', "WHERE" => 'spip_applications.statut="valide" AND spip_applicants.id_applicant=spip_applications.id_applicant AND spip_applicants.email!=""', 'ORDER BY' => "spip_applications.maj DESC"));

		echo fin_gauche();

		echo fin_page();

	}


?>