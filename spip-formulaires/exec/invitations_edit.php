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


	function exec_invitations_edit() {
	 	
		$id_formulaire	= intval($_GET['id_formulaire']);
		if (!autoriser('editer', 'formulaires', $id_formulaire)) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		if (function_exists('calculer_url_lettre'))
			$spip_lettres_actif = true;
		else
			$spip_lettres_actif = false;

		$formulaire		= new formulaire($id_formulaire);
		
		if (!empty($_POST['enregistrer'])) {
			$email = $_POST['email'];
			if (ereg(_REGEXP_EMAIL, $email)) {
				$invitation = new invitation($id_formulaire, $email);
				if ($spip_lettres_actif) { // si SPIP-Lettres
					include_spip('lettres_fonctions');
					$rubriques = $_POST['rubriques'];
					if (empty($rubriques)) $rubriques = array();
					$abonne = new abonne(0, $email);
					if ($abonne->existe) {
						$abonnements = $abonne->recuperer_abonnements();
						$abonnements_disponibles = $formulaire->recuperer_abonnements_disponibles();
						$abonnements = array_intersect($abonnements_disponibles, $abonnements);
						$desabonnements = array_diff($abonnements, $rubriques);
						if (!empty($desabonnements)) { // on désinscrit s'il y a des différences
							foreach ($desabonnements as $id_rubrique) {
								$abonne->valider_desabonnement($id_rubrique);
							}
						}
						$abonnements = array_diff($rubriques, $abonnements);
						if (!empty($abonnements)) {
							foreach ($abonnements as $id_rubrique) {
								$abonne->enregistrer_abonnement($id_rubrique);
								$abonne->valider_abonnement($id_rubrique);
							}
						}
						$abonne->supprimer_si_zero_abonnement();
					} else {
						if (!empty($rubriques)) {
							$abonne->enregistrer();
							foreach ($rubriques as $id_rubrique) {
								$abonne->enregistrer_abonnement($id_rubrique);
								$abonne->valider_abonnement($id_rubrique);
							}
						}
					}
				}
				if ($_POST['notification'] == 'oui') {
					$invitation->application->envoyer_invitation();
				}
				$url = generer_url_ecrire('formulaires', 'id_formulaire='.$id_formulaire, true);
				header('Location: ' . $url);
				exit();
			} else {
				$erreur = true;
			}
		}

		pipeline('exec_init',array('args'=>array('exec'=>'invitations_edit','id_formulaire'=>$id_formulaire),'data'=>''));

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('formulairesprive:formulaires'), "naviguer", "formulaires_tous");

		echo debut_gauche("",true);

		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'invitations_edit','id_formulaire'=>$id_formulaire),'data'=>''));

		echo creer_colonne_droite("",true);
		echo debut_droite("",true);
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'invitations_edit','id_formulaire'=>$id_formulaire),'data'=>''));

		echo '<div class="cadre-formulaire-editer">';
		echo '<div class="entete-formulaire">';
		echo icone_inline(_T('icone_retour'), generer_url_ecrire('formulaires', 'id_formulaire='.$id_formulaire), _DIR_PLUGIN_FORMULAIRES.'/prive/images/formulaire-24.png', "rien.gif", $GLOBALS['spip_lang_left']);
		echo _T('formulairesprive:creer_invitation');
		echo '<h1>'.$formulaire->titre.'</h1>';
		echo '</div>';

		echo '<div class="formulaire_spip formulaire_editer">';
		echo '<form method="post" action="'.generer_url_ecrire('invitations_edit', "id_formulaire=".$id_formulaire).'">';
		echo '<div>';

	  	echo '<ul>';

	    echo '<li class="obligatoire">';
		echo '<label for="email">'._T('formulairesprive:email_de_l_invite').'</label>';
		echo '<input type="text" class="text" name="email" id="email" value="" />';
		echo '</li>';

		if ($spip_lettres_actif) {
			$abonnements_disponibles = $formulaire->recuperer_abonnements_disponibles();
			if (count($abonnements_disponibles)) {
				$themes = sql_select('*', 'spip_themes', 'id_rubrique IN ('.implode(',', $abonnements_disponibles).')', '', 'titre');
				if (sql_count($themes) > 0) {
				    echo '<li class="obligatoire">';
					echo '<label>'._T('formulairesprive:abonner_a').'</label>';
					echo '<div class="choix">';
					echo '<input id="rub-'.$arr['id_rubrique'].'" type="checkbox" class="checkbox" value="'.$arr['id_rubrique'].'" name="rubriques[]" />';
					echo '<label for="rub-'.$arr['id_rubrique'].'">'.$arr['titre'].'</label>';
					echo '</div>';
					echo '</li>';
				}
			}
		}

	    echo '<li>';
		echo '<label>'._T('formulairesprive:envoyer_invitation_label').'</label>';
		echo '<div class="choix">';
		echo '<input id="notification" type="checkbox" class="checkbox" value="oui" name="notification" />';
		echo '<label for="notification">'._T('formulairesprive:oui').'</label>';
		echo '</div>';
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