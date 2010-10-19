<?php


	/**
	 * SPIP-Lettres
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
	include_spip('lettres_fonctions');


	function exec_naviguer_import() {
		$id_rubrique = $_REQUEST['id_rubrique'];

		if (!autoriser('importerabonnes', 'lettres')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		if (!empty($_POST['valider'])) {
			if (!$_FILES['fichier_csv']['error']) {
				$id_rubrique = $_POST['id_parent'];
				$format = $_POST['format'];
				$importer_csv = charger_fonction('importer_csv','inc');
				$fichier = $_FILES['fichier_csv']['tmp_name'];
				$tableau = $importer_csv($fichier,false,";");
				$tableau_emails_valides = array();
				$tableau_emails_non_valides = array();
				$tableau_desabonnes = array();
				foreach ($tableau as $ligne) {
					$email	= $ligne[0];
					$nom	= $ligne[1];
					if (lettres_verifier_validite_email($email)) {
						if (!lettres_tester_parmi_desabonnes($email)) {
							$abonne = new abonne(0, $email);
							if ($nom)
								$abonne->nom	= $nom;
							$abonne->format = $format;
							$abonne->enregistrer();
							$abonne->enregistrer_abonnement($id_rubrique);
							$abonne->valider_abonnement($id_rubrique);
/*
TODO
							// extras
							if ($champs_extra['abonnes']) {
								$i = 2;
								$tableau_extras = array();
								foreach ($champs_extra['abonnes'] as $cle_extra => $valeur_extra) {
									$tableau_extras[$cle_extra] = $ligne[$i];
									$i++;
								}
								$abonne->extra = serialize($tableau_extras);
								$abonne->enregistrer_champs_extra($manuellement=true);
							}
*/
							$tableau_emails_valides[] = $email;
						} else {
							$tableau_desabonnes[] = $email;
						}
					} else {
						if (!empty($email))
							$tableau_emails_non_valides[] = $email;
					}
				}
				$tableau_emails_valides = array_unique($tableau_emails_valides);
				$tableau_emails_non_valides = array_unique($tableau_emails_non_valides);
				$tableau_desabonnes = array_unique($tableau_desabonnes);
			} else {
				$erreur = true;
			}
		}

		pipeline('exec_init',array('args'=>array('exec'=>'naviguer_import'),'data'=>''));

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('lettresprive:import_abonnes'), "naviguer", "abonnes_tous");

		echo '<br /><br /><br />';
		echo gros_titre(_T('lettresprive:import_abonnes'),'',false);

		echo debut_gauche('', true);

		echo debut_boite_info(true);
		echo _T('lettresprive:aide_naviguer_import');
		echo '<ol>';
		echo '<li><strong>'._T('lettresprive:email').'</strong></li>';
		echo '<li>'._T('lettresprive:nom').'</li>';
/*
TODO
		if ($champs_extra['abonnes']) {
			foreach ($champs_extra['abonnes'] as $cle => $valeur) {
				list($style, $filtre, $prettyname, $choix, $valeurs) = explode("|", $valeur);
				if ($style == 'radio') {
					$un_choix = explode(',', $choix);
					echo '<li>'.$prettyname;
					echo '<br />'._T('lettresprive:seulement_valeurs_suivantes');
					echo '<ul>';
					foreach ($un_choix as $un)
						echo '<li>'.$un.'</li>';
					echo '</ul>';
					echo '</li>';
				} else {
					echo '<li>'.$prettyname.'</li>';
				}
			}
		}
*/
		echo '</ol>';
		echo _T('lettresprive:aide_donnees_obligatoires');
		echo fin_boite_info(true);

		$raccourcis = icone_horizontale(_T('lettresprive:aller_liste_abonnes'), generer_url_ecrire('abonnes_tous'), _DIR_PLUGIN_LETTRES.'prive/images/abonne.png', 'rien.gif', false);
		if ($id_rubrique)
			$raccourcis.= icone_horizontale(_T('lettresprive:retour_rubrique'), generer_url_ecrire('naviguer', 'id_rubrique='.$id_rubrique), _DIR_PLUGIN_LETTRES.'prive/images/rubrique-24.png', 'rien.gif', false);
		echo bloc_des_raccourcis($raccourcis);
  		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'naviguer_import'),'data'=>''));

		echo creer_colonne_droite('', true);
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'naviguer_import'),'data'=>''));

   		echo debut_droite('', true);

		echo "<form method='post' action='".generer_url_ecrire('naviguer_import')."' enctype='multipart/form-data'>";

		if (count($tableau_emails_valides) or count($tableau_emails_non_valides)) {
			echo debut_cadre_enfonce(_DIR_PLUGIN_LETTRES.'prive/images/import.png', true, "", _T('lettresprive:resultat'));
			if (count($tableau_emails_valides)) {
				echo "<p><strong>"._T('lettresprive:nb_abonnes_importes')."</strong> ".count($tableau_emails_valides)."</p>";
				echo debut_cadre_couleur('', true);
				echo '<div style="height: 200px; overflow: auto;">';
				foreach ($tableau_emails_valides as $email)
					echo $email.'<br />';
				echo "</div>";
				echo fin_cadre_couleur(true);
			}
			if (count($tableau_desabonnes)) {
				echo "<p><strong>"._T('lettresprive:nb_emails_desabonnes')."</strong> ".count($tableau_desabonnes)."</p>";
				echo debut_cadre_couleur('', true);
				echo '<div style="height: 200px; overflow: auto;">';
				foreach ($tableau_desabonnes as $email)
					echo $email.'<br />';
				echo "</div>";
				echo fin_cadre_couleur(true);
			}
			if (count($tableau_emails_non_valides)) {
				echo "<p><strong>"._T('lettresprive:nb_emails_non_valides')."</strong> ".count($tableau_emails_non_valides)."</p>";
				echo debut_cadre_couleur('', true);
				echo '<div style="height: 200px; overflow: auto;">';
				foreach ($tableau_emails_non_valides as $email)
					echo $email.'<br />';
				echo "</div>";
				echo fin_cadre_couleur(true);
			}
			echo '<div align="right">';
			echo '<input type="submit" name="retour" class="fondo" value="'._T('lettresprive:retour').'" />';
			echo '</div>';
			echo fin_cadre_enfonce(true);
		} else {
			echo debut_cadre_enfonce(_DIR_PLUGIN_LETTRES.'prive/images/import.png', true, "", _T('lettresprive:rubrique_destination'));
			$selecteur_rubrique = charger_fonction('chercher_rubrique', 'inc');
			echo $selecteur_rubrique($id_rubrique, 'rubrique', false);
			echo fin_cadre_enfonce(true);
			echo debut_cadre_enfonce(_DIR_PLUGIN_LETTRES.'prive/images/preferences.png', true, "", _T('lettresprive:boite_format'));
			echo _T('lettresprive:changer_format').'&nbsp;&nbsp;';
			echo "<select name='format' CLASS='fondl'>";
			echo '<option value="mixte">'._T('lettresprive:mixte').'</option>';
			echo '<option value="html">'._T('lettresprive:html').'</option>';
			echo '<option value="texte">'._T('lettresprive:texte').'</option>';
			echo "</select>";
			echo fin_cadre_enfonce(true);
			echo debut_cadre_enfonce(_DIR_PLUGIN_LETTRES.'prive/images/csv.png', true, "", _T('lettresprive:fichier_csv'));
			if ($erreur)
				echo _T('lettresprive:erreur_upload').'<br />';
			echo "<input type='file' name='fichier_csv' />";
			echo '<div align="right">';
			echo '<input type="submit" name="valider" class="fondo" value="'._T('lettresprive:valider').'" />';
			echo '</div>';
			echo fin_cadre_enfonce(true);
		}

		echo '</form>';

		echo pipeline('affiche_milieu', array('args'=>array('exec'=>'naviguer_import'),'data'=>''));
		
		echo fin_gauche();

		echo fin_page();

	}


?>