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
	 * exec_applicants_edit
	 *
	 * Edition du nom et email d'un usager
	 *
	 * @author Pierre Basson
	 **/
	function exec_applicants_edit() {
		global $dir_lang, $spip_lang_right, $options;

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

		if (!empty($_POST['enregistrer'])) {
			if (ereg(_REGEXP_EMAIL, $_POST['email'])) {
				$applicant->email = $_POST['email'];
				$applicant->nom = $_POST['nom'];

				$applicant->enregistrer();

				$url = generer_url_ecrire('applicants', 'id_applicant='.$applicant->id_applicant, true);
				header('Location: ' . $url);
				exit();
			} else {
				$erreur = true;
			}
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
		echo icone_horizontale(_T('formulairesprive:retour'), generer_url_ecrire("applicants","id_applicant=".$applicant->id_applicant), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/applications.png', "", '');
		fin_raccourcis();


    	debut_droite();
		debut_cadre_relief('../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/applications.png');

		echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
		echo "<tr width='100%'><td width='100%' valign='top'>";
		gros_titre($applicant->email, "puce-verte.gif");
		echo "</td>";
		echo "<td>", http_img_pack("rien.gif", ' ', "width='5'") ."</td>\n";
		echo "<td  align='$spip_lang_right' valign='top'>";
		icone(_T('formulairesprive:retour'), generer_url_ecrire("applicants","id_applicant=".$applicant->id_applicant), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/applications.png', "rien.gif");
		echo "</td>";
		echo "</tr>\n";
		echo "</table>\n";

		echo "<div>&nbsp;</div>";
		
		echo fin_cadre_relief();

		echo '<form action="'.generer_url_ecrire('applicants_edit', 'id_applicant='.$applicant->id_applicant).'" name="formulaire" method="post">';

		echo "<div class='liste'>\n";
		echo "<table width='100%' cellpadding='3' cellspacing='0' border='0' background=''>\n";

		echo "<tr class='tr_liste' valign='top'>\n";
		echo "<td class='arial2' width='40%'>\n";
		echo "<label for='email'>\n";
		echo _T('formulairesprive:email_applicant');
		echo "</label>\n";
		echo "</td>\n";
		echo "<td class='arial2' width='60%'>\n";
		echo '<input type="text" name="email" id="email" value="'.$applicant->email.'" class="fondo" style="width: 100%;" /><br />';
		if ($erreur)
			echo '<strong>'.formulaires_afficher_erreur(true, 'email_applicant').'</strong>';
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr class='tr_liste' valign='top'>\n";
		echo "<td class='arial2' width='40%'>\n";
		echo "<label for='nom'>\n";
		echo _T('formulairesprive:nom_applicant');
		echo "</label>\n";
		echo "</td>\n";
		echo "<td class='arial2' width='60%'>\n";
		echo '<input type="text" name="nom" id="nom" value="'.$applicant->nom.'" class="fondo" style="width: 100%;" /><br />';
		echo "</td>\n";
		echo "</tr>\n";

		echo "</table>\n";
		echo "</div>\n";

		echo "<div align='right'>";
		echo "<INPUT CLASS='fondo' TYPE='submit' NAME='enregistrer' VALUE='"._T('formulairesprive:enregistrer')."'>";
		echo "</div>";

		echo "</form>";

		echo fin_gauche();

		echo fin_page();

	}

?>