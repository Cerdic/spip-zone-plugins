<?php


	/**
	 * SPIP-Formulaires
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	if (!defined("_ECRIRE_INC_VERSION")) return;
 	include_spip('inc/presentation');
	include_spip('formulaires_fonctions');


	function exec_applicants_edit() {
	 	
		if (!autoriser('editer', 'formulaires')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		$id_applicant = intval($_GET['id_applicant']);
		$applicant = new applicant($id_applicant);

		pipeline('exec_init',array('args' => array('exec' => 'applicants_edit', 'id_applicant' => $applicant->id_applicant), 'data' => ''));

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

		echo debut_gauche("",true);

		echo pipeline('affiche_gauche',array('args' => array('exec' => 'applicants_edit', 'id_applicant' => $applicant->id_applicant), 'data' => ''));

		echo creer_colonne_droite("",true);
		echo debut_droite("",true);
		echo pipeline('affiche_droite',array('args' => array('exec' => 'applicants_edit', 'id_applicant' => $applicant->id_applicant), 'data' => ''));


		echo '<div class="cadre-formulaire-editer">';
		echo '<div class="entete-formulaire">';
		echo icone_inline(_T('icone_retour'), generer_url_ecrire('applicants', 'id_applicant='.$applicant->id_applicant), _DIR_PLUGIN_FORMULAIRES.'/prive/images/applications.png', "rien.gif", $GLOBALS['spip_lang_left']);
		echo _T('formulairesprive:editer_applicant');
		echo '<h1>'.$applicant->email.'</h1>';
		echo '</div>';

		echo '<div class="formulaire_spip formulaire_editer">';
		echo '<form method="post" action="'.generer_url_ecrire('applicants_edit', "id_applicant=".$applicant->id_applicant).'">';
		echo '<div>';

	  	echo '<ul>';

	    echo '<li class="obligatoire">';
		echo '<label for="email">'._T('formulairesprive:email_applicant').'</label>';
		echo '<input type="text" class="text" name="email" id="email" value="'.$applicant->email.'" />';
		if ($erreur)
			echo '<span class="erreur_message">'.formulaires_afficher_erreur(true, 'email_applicant').'</span>';
		echo '</li>';

	    echo '<li>';
		echo '<label for="nom">'._T('formulairesprive:nom_applicant').'</label>';
		echo '<input type="text" class="text" name="nom" id="nom" value="'.$applicant->nom.'" />';
		echo '</li>';

		echo '</ul>';

	  	echo '<p class="boutons"><input type="submit" class="submit" name="enregistrer" value="'._T('formulairesprive:enregistrer').'" /></p>';

		echo '</div>';

		echo '</form>';

		echo '</div>';
		echo '</div>';
	 	
		echo fin_gauche();

		echo fin_page();

	}
	
	
?>