<?php


	/**
	 * SPIP-Formulaires
	 *
	 * @copyright 2006-2007 Artégo
	 **/


 	include_spip('inc/presentation');
	include_spip('inc/date');
	include_spip('inc/chercher_logo');
	include_spip('formulaires_fonctions');
	include_spip('inc/documents');
	include_spip('inc/rubriques');
	include_spip('inc/headers');
	include_spip('inc/iconifier');
	include_spip('surcharges_fonctions');
	include_spip('public/assembler');


	/**
	 * exec_applicants
	 *
	 * Visualisation d'un usager
	 *
	 * @author Pierre Basson
	 **/
	function exec_applicants() {
		global $dir_lang, $lang, $spip_lang_right, $options;

		if ($GLOBALS['connect_statut'] != "0minirezo") {
			echo _T('avis_non_acces_page');
			echo fin_page();
			exit;
		}

		if (empty($_GET['id_applicant'])) {
			$url = generer_url_ecrire('applications_tous');
			header('Location: ' . $url);
			exit();
		}
		
		$id_applicant = intval($_GET['id_applicant']);
		$applicant = new applicant($id_applicant);

		if (!$applicant->existe) {
			$url = generer_url_ecrire('applications_tous');
			header('Location: ' . $url);
			exit();
		}


		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page($applicant->email, "naviguer", "applications_tous");


		debut_gauche();
		debut_boite_info();
		echo "<div align='center'>\n";
		echo "<font face='Verdana,Arial,Sans,sans-serif' size='1'><b>"._T('formulairesprive:numero_applicant')."</b></font>\n";
		echo "<br><font face='Verdana,Arial,Sans,sans-serif' size='6'><b>".$applicant->id_applicant."</b></font>\n";
		echo "</div>\n";
		fin_boite_info();

		debut_raccourcis();
		echo icone_horizontale(_T('formulairesprive:retour_applicants_applications'), generer_url_ecrire("applications_tous"), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/applications.png', "", '');
		fin_raccourcis();


    	debut_droite();
		debut_cadre_relief('../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/applications.png');

		echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
		echo "<tr width='100%'><td width='100%' valign='top'>";
		gros_titre($applicant->email, "puce-verte.gif");
		echo "</td>";
		echo "<td>", http_img_pack("rien.gif", ' ', "width='5'") ."</td>\n";
		echo "<td  align='$spip_lang_right' valign='top'>";
		icone(_T('formulairesprive:modifier_applicant'), generer_url_ecrire("applicants_edit","id_applicant=".$applicant->id_applicant), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/applications.png', "edit.gif");
		echo "</td>";
		echo "</tr>\n";
		if ($applicant->nom) {
			echo "<tr><td>\n";
			echo "<div align='$spip_lang_left' style='padding: 5px; border: 1px dashed #aaaaaa;'>";
			echo "<font size=2 face='Verdana,Arial,Sans,sans-serif'>";
			echo _T('formulairesprive:nom_applicant')." : <B>".$applicant->nom."</B><br />";
			echo "</font>";
			echo "</div>";
			echo "</td>";
			echo "</tr>\n";
		}
		echo "</table>\n";

		fin_cadre_relief();
		
		echo formulaires_afficher_applications(_T('formulairesprive:liste_applications_de_cet_applicant'), array("SELECT" => "spip_applications.*", "FROM" => 'spip_applications, spip_applicants', "WHERE" => 'spip_applications.statut="valide" AND spip_applicants.id_applicant=spip_applications.id_applicant AND spip_applicants.email!="" AND spip_applicants.id_applicant='.$applicant->id_applicant, 'ORDER BY' => "spip_applications.maj DESC"));

		echo fin_gauche();

		echo fin_page();

	}

?>