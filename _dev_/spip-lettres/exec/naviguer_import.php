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
	include_spip('lettres_fonctions');
	include_spip('surcharges_fonctions');
	include_spip('inc/presentation');


	function exec_naviguer_import() {
		global $spip_lang_right, $spip_lang_left;
		global $champs_extra, $id_rubrique;

		if (!autoriser('importer', 'lettres')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		pipeline('exec_init',array('args'=>array('exec'=>'naviguer_import'),'data'=>''));


		if (!empty($_POST['valider'])) {
			if (!$_FILES['fichier_csv']['error']) {
				$id_rubrique = $_POST['id_parent'];
				$format = $_POST['format'];
				$fichier = $_FILES['fichier_csv']['tmp_name'];
				$tableau = surcharges_importer_csv($fichier);
				$tableau_emails_valides = array();
				$tableau_emails_non_valides = array();
				$tableau_desabonnes = array();
				foreach ($tableau as $ligne) {
					$email	= $ligne[0];
					$nom	= $ligne[1];
					if (lettres_verifier_validite_email($email)) {
						if (!lettres_tester_parmi_desabonnes($email)) {
							$abonne = new abonne(0, $email);
							$abonne->nom	= $nom;
							$abonne->format = $format;
							$abonne->enregistrer();
							$abonne->enregistrer_abonnement($id_rubrique);
							$abonne->valider_abonnement($id_rubrique);
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
			

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('lettresprive:import_abonnes'), "naviguer", "abonnes_tous");
	
		debut_gauche();
		
		debut_boite_info();
		echo _T('lettresprive:aide_naviguer_import');
		echo '<ol>';
		echo '<li>'._T('lettresprive:email').'</li>';
		echo '<li>'._T('lettresprive:nom').'</li>';
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
		echo '</ol>';
		echo _T('lettresprive:aide_donnees_obligatoires');
		fin_boite_info();

		debut_raccourcis();	
		icone_horizontale(_T('lettresprive:aller_liste_abonnes'), generer_url_ecrire('abonnes_tous'), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/abonne.png');
		if ($id_rubrique)
			icone_horizontale(_T('lettresprive:retour_rubrique'), generer_url_ecrire('naviguer', 'id_rubrique='.$id_rubrique), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/rubrique-24.png');
		fin_raccourcis();	
	
		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'naviguer_import'),'data'=>''));

		creer_colonne_droite();
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'naviguer_import'),'data'=>''));

		debut_droite();

		debut_cadre_relief('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/import.png');

		echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
		echo "<tr width='100%'><td width='100%' valign='top'>";
		gros_titre(_T('lettresprive:import_abonnes'));
		echo "</td>";
		echo "</tr>\n";
		echo "</table>\n";

		echo "<div>&nbsp;</div>";

		echo "<form method='post' action='".generer_url_ecrire('naviguer_import')."' enctype='multipart/form-data'>";

		if (count($tableau_emails_valides) or count($tableau_emails_non_valides)) {
			debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/import.png', false, "", _T('lettresprive:resultat'));
			if (count($tableau_emails_valides)) {
				echo "<br />";
				echo "<span class='verdana1'><B>"._T('lettresprive:nb_abonnes_importes')."</B> ".count($tableau_emails_valides)."</span><br />";
				echo "<br />";
				debut_cadre_couleur();
				echo '<div style="height: 200px; overflow: auto;">';
				echo "<table border='0' width='100%' style='text-align: right'>";
				foreach ($tableau_emails_valides as $email) {
					echo "<tr>";
					echo '	<td>'.$email.'</td>';
					echo "</tr>";
				}
				echo "</table>";
				echo "</div>";
				fin_cadre_couleur();
				echo "<br />";
			}
			if (count($tableau_desabonnes)) {
				echo "<br />";
				echo "<span class='verdana1'><B>"._T('lettresprive:nb_emails_desabonnes')."</B> ".count($tableau_desabonnes)."</span><br />";
				echo "<br />";
				debut_cadre_couleur();
				echo '<div style="height: 200px; overflow: auto;">';
				echo "<table border='0' width='100%' style='text-align: right'>";
				foreach ($tableau_desabonnes as $email) {
					echo "<tr>";
					echo '	<td>'.$email.'</td>';
					echo "</tr>";
				}
				echo "</table>";
				echo "</div>";
				fin_cadre_couleur();
			}
			if (count($tableau_emails_non_valides)) {
				echo "<br />";
				echo "<span class='verdana1'><B>"._T('lettresprive:nb_emails_non_valides')."</B> ".count($tableau_emails_non_valides)."</span><br />";
				echo "<br />";
				debut_cadre_couleur();
				echo '<div style="height: 200px; overflow: auto;">';
				echo "<table border='0' width='100%' style='text-align: right'>";
				foreach ($tableau_emails_non_valides as $email) {
					echo "<tr>";
					echo '	<td>'.$email.'</td>';
					echo "</tr>";
				}
				echo "</table>";
				echo "</div>";
				fin_cadre_couleur();
			}
			echo '<div align="right">';
			echo "<INPUT TYPE='submit' NAME='retour' CLASS='fondo' VALUE='"._T('lettresprive:retour')."' STYLE='font-size:10px'>";
			echo "</div>";
			fin_cadre_enfonce();
		} else {
			debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/csv.png', false, "", _T('lettresprive:fichier_csv'));
			if ($erreur)
				echo _T('lettresprive:erreur_upload').'<br />';
			echo "<input type='file' name='fichier_csv' />";
			fin_cadre_enfonce();
			debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/preferences.png', false, "", _T('lettresprive:boite_format'));
			echo "<table border='0' width='100%'>";
			echo "<tr>";
			echo "	<td><span class='verdana1'><B>"._T('lettresprive:changer_format')."</B></span> &nbsp;</td>";
			echo "	<td>";
			echo "<select name='format' CLASS='fondl'>";		
			echo '<option value="mixte">'._T('lettresprive:mixte').'</option>';
			echo '<option value="html">'._T('lettresprive:html').'</option>';
			echo '<option value="texte">'._T('lettresprive:texte').'</option>';
			echo "</select>";
			echo "	</td>";
			echo "</tr>";
			echo "</table>";
			fin_cadre_enfonce();	
			debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/import.png', false, "", _T('lettresprive:rubrique_destination'));
			$selecteur_rubrique = charger_fonction('chercher_rubrique', 'inc');
			echo $selecteur_rubrique($id_rubrique, 'rubrique', false);
			fin_cadre_enfonce();	
			echo '<div align="right">';
			echo "<INPUT TYPE='submit' NAME='valider' CLASS='fondo' VALUE='"._T('lettresprive:valider')."' STYLE='font-size:10px'>";
			echo "</div>";
		}

		echo '</form>';

		fin_cadre_relief();

		echo fin_gauche();

		echo fin_page();
	}

?>