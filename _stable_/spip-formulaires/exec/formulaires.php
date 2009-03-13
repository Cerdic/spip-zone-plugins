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
	include_spip('inc/filtres');
	include_spip('inc/filtres_images');
	include_spip('surcharges_fonctions');


	/**
	 * exec_formulaires
	 *
	 * Visualisation d'un formulaire
	 *
	 * @author Pierre Basson
	 **/
	function exec_formulaires() {
		global $dir_lang, $lang, $spip_lang_right, $options;

		if ($GLOBALS['connect_statut'] != "0minirezo") {
			echo _T('avis_non_acces_page');
			echo fin_page();
			exit;
		}

		if (empty($_GET['id_formulaire'])) {
			$url = generer_url_ecrire('formulaires_tous');
			header('Location: ' . $url);
			exit();
		}
		
		$id_formulaire = intval($_GET['id_formulaire']);
		$formulaire = new formulaire($id_formulaire);

		$url = generer_url_ecrire('formulaires', 'id_formulaire='.$formulaire->id_formulaire, true);

		pipeline('exec_init',array('args'=>array('exec'=>'formulaires','id_formulaire'=>$formulaire->id_formulaire),'data'=>''));

		if (!empty($_GET['supprimer_auteur'])) {
			$formulaire->supprimer_auteur(intval($_GET['supprimer_auteur']));
			header('Location: ' . $url);
			exit();
		}

		if (!empty($_POST['changer_auteur'])) {
			$formulaire->ajouter_auteur(intval($_POST['id_auteur']));
			header('Location: ' . $url);
			exit();
		}

		if (!empty($_POST['changer_dates'])) {
			$formulaire->changer_dates($_POST['annee_debut'], $_POST['mois_debut'], $_POST['jour_debut'], $_POST['annee_fin'], $_POST['mois_fin'], $_POST['jour_fin']);
			header('Location: ' . $url);
			exit();
		}

		if (!empty($_POST['changer_en_ligne'])) {
			$url = $formulaire->changer_en_ligne($_POST['en_ligne']);
			header('Location: ' . $url);
			exit();
		}
		
		if (!empty($_GET['ordonner_bloc'])) {
			$bloc = new bloc($formulaire->id_formulaire, $_GET['ordonner_bloc']);
			$bloc->changer_ordre(intval($_GET['position']));
			header('Location: ' . $url);
			exit();
		}
		
		if (!empty($_GET['ordonner_question'])) {
			$question = new question($formulaire->id_formulaire, $_GET['id_bloc'], $_GET['ordonner_question']);
			$question->changer_ordre(intval($_GET['position']));
			header('Location: ' . $url);
			exit();
		}
		
		if (!empty($_GET['ordonner_choix_question'])) {
			$choix_question = new choix_question($formulaire->id_formulaire, $_GET['id_bloc'], $_GET['id_question'], $_GET['ordonner_choix_question']);
			$choix_question->changer_ordre(intval($_GET['position']));
			header('Location: ' . $url);
			exit();
		}
		
		if (!empty($_GET['supprimer_bloc'])) {
			$bloc = new bloc($formulaire->id_formulaire, $_GET['supprimer_bloc']);
			$bloc->supprimer();
			header('Location: ' . $url);
			exit();
		}
		
		if (!empty($_GET['supprimer_question'])) {
			$question = new question($formulaire->id_formulaire, $_GET['id_bloc'], $_GET['supprimer_question']);
			$question->supprimer();
			header('Location: ' . $url);
			exit();
		}
		
		if (!empty($_GET['supprimer_choix_question'])) {
			$choix_question = new choix_question($formulaire->id_formulaire, $_GET['id_bloc'], $_GET['id_question'], $_GET['supprimer_choix_question']);
			$choix_question->supprimer();
			header('Location: ' . $url);
			exit();
		}
		


		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page($formulaire->titre, "naviguer", "formulaires_tous");


		debut_grand_cadre();
		echo afficher_hierarchie($formulaire->id_rubrique);
		fin_grand_cadre();


		debut_gauche();
		formulaires_afficher_numero_formulaire($formulaire->id_formulaire, true);
		global $logo_libelles;
		$logo_libelles['id_formulaire'] = _T('formulairesprive:logo_formulaire');
		$iconifier = charger_fonction('iconifier', 'inc');
		echo $iconifier('id_formulaire', $formulaire->id_formulaire, 'formulaires');

		debut_raccourcis();
		formulaires_afficher_raccourci_liste_formulaires();
		echo icone_horizontale(_T('formulairesprive:copier_ce_formulaire'), generer_url_action("copie_formulaire", "id_formulaire=".$formulaire->id_formulaire), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/copie.png', "", '');
		echo icone_horizontale(_T('formulairesprive:creer_nouveau_bloc'), generer_url_ecrire("blocs_edit", "id_formulaire=".$formulaire->id_formulaire."&new=oui"), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/bloc.png', "creer.gif", '');
		if ($formulaire->limiter_invitation == 'oui')
			echo icone_horizontale(_T('formulairesprive:creer_invitation'), generer_url_ecrire("invitations_edit", "id_formulaire=".$formulaire->id_formulaire), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/invitation.png', "creer.gif", '');
		if ($formulaire->possede_applications())
			echo icone_horizontale(_T('formulairesprive:exporter_les_resultats'), generer_url_action("applications_export", "id_formulaire=".$formulaire->id_formulaire), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/export.png', "", '');
		fin_raccourcis();


 		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'formulaires','id_formulaire'=>$id_formulaire),'data'=>''));

		creer_colonne_droite();
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'formulaires','id_formulaire'=>$id_formulaire),'data'=>''));

    	debut_droite();
		debut_cadre_relief('../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/formulaire-24.png');

		echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
		switch ($formulaire->en_ligne) {
			case 'non':
				$logo_statut = "puce-blanche.gif";
				break;
			case 'oui':
				if ($formulaire->limiter_temps == 'oui') {
					switch ($formulaire->statut) {
						case 'en_attente' :
							$logo_statut = "puce-orange.gif";
							break;
						case 'publie' :
							$logo_statut = "puce-verte.gif";
							break;
						case 'termine' :
							$logo_statut = "puce-poubelle.gif";
							break;
					}
				} else {
					$logo_statut = "puce-verte.gif";
				}
				break;
		}
		echo "<tr width='100%'><td width='100%' valign='top'>";
		gros_titre($formulaire->titre, $logo_statut);
		echo "</td>";
		echo "<td>", http_img_pack("rien.gif", ' ', "width='5'") ."</td>\n";
		echo "<td  align='$spip_lang_right' valign='top'>";
		icone(_T('formulairesprive:modifier_formulaire'), generer_url_ecrire("formulaires_edit","id_formulaire=".$formulaire->id_formulaire), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/formulaire-24.png', "edit.gif");
		echo "</td>";
		echo "</tr>\n";
		echo "<tr><td>\n";
		if (!empty($formulaire->descriptif)) {
			echo "<div align='$spip_lang_left' style='padding: 5px; border: 1px dashed #aaaaaa;'>";
			echo "<font size=2 face='Verdana,Arial,Sans,sans-serif'>";
			echo image_reduire(propre($formulaire->descriptif), 375, 0);
			echo "</font>";
			echo "</div>";
			echo "<br />\n";
		}
		echo "<div align='$spip_lang_left' style='padding: 5px; border: 1px dashed #aaaaaa;'>";
		echo "<font size=2 face='Verdana,Arial,Sans,sans-serif'>";
		echo _T('formulairesprive:type_formulaire')." : <B>"._T('formulairesprive:'.$formulaire->type)."</B><br />";
		echo _T('formulairesprive:limiter_temps')." : <B>"._T('formulairesprive:'.$formulaire->limiter_temps)."</B><br />";
		echo _T('formulairesprive:limiter_invitation')." : <B>"._T('formulairesprive:'.$formulaire->limiter_invitation)."</B><br />";
		if ($formulaire->limiter_invitation == 'non') {
			echo _T('formulairesprive:limiter_applicant')." : <B>"._T('formulairesprive:'.$formulaire->limiter_applicant)."</B><br />";
#			echo _T('formulairesprive:notifier_applicant')." : <B>"._T('formulairesprive:'.$formulaire->notifier_applicant)."</B><br />";
		}
		echo _T('formulairesprive:notifier_auteurs')." : <B>"._T('formulairesprive:'.$formulaire->notifier_auteurs)."</B><br />";
		echo "</font>";
		echo "</div>";
		echo "</td></tr>\n";
		echo "</table>\n";

		echo "<div>&nbsp;</div>";


		echo generer_url_post_ecrire("formulaires", "id_formulaire=$id_formulaire", 'formulaire1');

		if ($formulaire->limiter_temps == 'oui')
			formulaires_afficher_dates($formulaire->date_debut, $formulaire->date_fin, true);

		formulaires_afficher_auteurs($formulaire->id_formulaire, true);

		echo '</form>';

		$editer_mot = charger_fonction('editer_mot', 'inc');
		echo $editer_mot('formulaire', $formulaire->id_formulaire, $cherche_mot, $select_groupe, true);

		if ($options == 'avancees') {
 			echo pipeline('affiche_milieu', array('args' => array('exec' => 'formulaires', 'id_formulaire' => $formulaire->id_formulaire, 'flag_editable' => true), 'data' => ''));
		}

		echo generer_url_post_ecrire("formulaires", "id_formulaire=$id_formulaire", 'formulaire2');
		debut_cadre_relief();
		echo "<center><B>"._T('formulairesprive:action')."</B>&nbsp;";
		echo "<SELECT NAME='en_ligne' SIZE='1' CLASS='fondl'>\n";
		echo '	<OPTION VALUE="non"'.(($formulaire->en_ligne == 'non') ? ' SELECTED' : '').'>'._T('formulairesprive:action_hors_ligne').'</OPTION>'."\n";
		echo '	<OPTION VALUE="oui"'.(($formulaire->en_ligne == 'oui') ? ' SELECTED' : '').'>'._T('formulairesprive:action_en_ligne').'</OPTION>'."\n";
		echo '	<OPTION VALUE="poubelle">'._T('formulairesprive:action_poubelle').'</OPTION>'."\n";
		echo "</SELECT>";
		echo "&nbsp;&nbsp;<INPUT TYPE='submit' NAME='changer_en_ligne' CLASS='fondo' VALUE='"._T('formulairesprive:changer')."' STYLE='font-size:10px'>";
		echo '</center>';
		fin_cadre_relief();
		echo '</form>';

		fin_cadre_relief();

		$blocs = $formulaire->recuperer_blocs();
		$taille_blocs = count($blocs);
		foreach ($blocs as $id_bloc) {
			$bloc = new bloc($formulaire->id_formulaire, $id_bloc);
			$bloc->afficher();
		}

		echo "<div align='$spip_lang_right'>";
		echo icone(_T('formulairesprive:creer_nouveau_bloc'), generer_url_ecrire("blocs_edit","id_formulaire=".$formulaire->id_formulaire."&new=oui"), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/bloc.png', "creer.gif", '', 'non');
		echo "</div><p>";

		debut_cadre_relief();

		if (strlen($formulaire->chapo) > 0) {
			echo "<div $dir_lang style='font-weight: bold; padding: 10px;' class='spip_small'>";
			echo image_reduire(propre($formulaire->chapo), 475, 0);
			echo "</div>";
		}

		echo "<div align='justify' style='padding: 10px;'>";
		echo "<div $dir_lang>";
		echo image_reduire(propre($formulaire->texte), 475, 0);
		echo "<br clear='both' />";
		echo "<br clear='both' />";
		echo "</div>";
		if ($formulaire->ps) {
			echo debut_cadre_enfonce();
			echo "<div $dir_lang><font style='font-family:Verdana,Arial,Sans,sans-serif; font-size: small;'>";
			echo justifier("<b>"._T('info_ps')."</b> ".image_reduire(propre($formulaire->ps), 475, 0));
			echo "</font></div>";
			echo fin_cadre_enfonce();
		}
		echo "</div>";
		echo "\n\n<div align='$spip_lang_right'><br />";
		icone(_T('formulairesprive:modifier_formulaire'), generer_url_ecrire("formulaires_edit","id_formulaire=$id_formulaire"), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/formulaire-24.png', "edit.gif");
		echo "</div>";

		echo surcharges_upload_documents( (int) $formulaire->id_formulaire, "formulaire", 'formulaires');

		fin_cadre_relief();

		echo formulaires_afficher_applications(_T('formulairesprive:liste_applications'), array("FROM" => 'spip_applications, spip_applicants', "WHERE" => 'spip_applications.statut="valide" AND spip_applications.id_formulaire="'.$formulaire->id_formulaire.'" AND spip_applicants.id_applicant=spip_applications.id_applicant AND spip_applicants.email!=""', 'ORDER BY' => "spip_applications.maj DESC"));

		if ($formulaire->limiter_invitation == 'oui') {
			echo "<div align='$spip_lang_right'>";
			echo icone(_T('formulairesprive:creer_invitation'), generer_url_ecrire("invitations_edit", "id_formulaire=".$formulaire->id_formulaire), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/invitation.png', "creer.gif", '', 'non');
			echo "</div><p>";
		}

		echo fin_gauche();

		echo fin_page();

	}

?>