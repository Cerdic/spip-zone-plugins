<?php

	include_spip('inc/lettres_fonctions');
	include_spip('inc/lettres_admin');
	include_spip('inc/presentation');


	function exec_abonnes_import() {
	
		lettres_verifier_droits();

		if (!empty($_POST['charger']) AND !empty($_FILES['fichier_csv'])) {
			if (!$_FILES['fichier_csv']['error']) {
				$etape = 2;
				$chemin_fichier = $_FILES['fichier_csv']['tmp_name'];
				$nom_fichier = $_FILES['fichier_csv']['name'];
				$tableau = explode("\n", str_replace("\r", "\n", implode('', file($chemin_fichier))));
				$tableau_emails_valides = array();
				foreach ($tableau as $cle => $email) {
					if (lettres_verifier_validite_email($email)) {
						if (!in_array($email, $tableau_emails_valides))
							$tableau_emails_valides[] = $email;
					} else {
						if (!empty($email))
							$tableau_emails_non_valides[] = $email;
					}
				}
			}
		} else if (!empty($_POST['ajouter_abonnes']) AND !empty($_POST['abonnes'])) {
			$tableau_abonnes = $_POST['abonnes'];
			$format = $_POST['format'];
			$etape = 3;
			$tableau_abonnes_ajoutes = array();
			$tableau_abonnes_non_ajoutes = array();
			foreach ($tableau_abonnes as $cle => $email) {
				list($existence, $id_abonne) = lettres_verifier_existence_abonne($email);
				if ($existence) {
					$tableau_abonnes_non_ajoutes[$id_abonne] = $email;
				} else {
					spip_query('INSERT INTO spip_abonnes (email, code, format, maj) VALUES ("'.$email.'", "'.lettres_calculer_code().'", "'.$format.'", NOW())');
					$id_abonne = spip_insert_id();
					$tableau_abonnes_ajoutes[$id_abonne] = $email;
				}
			}
		} else if (!empty($_POST['inscrire']) AND !empty($_POST['abonnes']) AND !empty($_POST['lettres'])) {
			$etape = 4;
			$tableau_abonnes = $_POST['abonnes'];
			$tableau_lettres = $_POST['lettres'];
			$tableau_abonnes_ajoutes = array();
			$tableau_abonnes_non_ajoutes = array();
			foreach ($tableau_lettres as $id_lettre) {
				$tableau_abonnes_non_ajoutes[$id_lettre] = array();
				foreach ($tableau_abonnes as $id_abonne => $email) {
					if (lettres_verifier_action_possible($id_lettre, 'inscription', $email)) {
						$id_abonne = lettres_recuperer_id_abonne_depuis_email($email);
						spip_query('INSERT INTO spip_abonnes_lettres (id_abonne, id_lettre, date_inscription, statut) VALUES ("'.$id_abonne.'", "'.$id_lettre.'", NOW(), "valide")');
						$tableau_abonnes_ajoutes[$id_lettre][$id_abonne] = $email;
						lettres_ajouter_statistique_import($id_lettre);
					} else {
						$id_abonne = lettres_recuperer_id_abonne_depuis_email($email);
						if (!in_array($email, $tableau_abonnes_non_ajoutes[$id_lettre]))
							$tableau_abonnes_non_ajoutes[$id_lettre][$id_abonne] = $email;
					}
				}
			}
		} else {
			$etape = 1;
		}

		$id_lettre = intval($_REQUEST['id_lettre']);
			


		debut_page(_T('lettres:import_csv'), "lettres", "abonnes");
	
		debut_gauche();
		lettres_afficher_etapes_import($etape);
		
		debut_raccourcis();	
		lettres_afficher_raccourci_liste_abonnes(_T('lettres:aller_liste_abonnes'));
		lettres_afficher_raccourci_liste_lettres(_T('lettres:aller_liste_lettres'));
		fin_raccourcis();	
	

		debut_droite();

		debut_cadre_relief('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/import.png');

		echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
		echo "<tr width='100%'><td width='100%' valign='top'>";
		gros_titre(_T('lettres:import_etape_'.$etape));
		echo "</td>";
		echo "<td>", http_img_pack("rien.gif", ' ', "width='5'") ."</td>\n";
		echo "<td  align='$spip_lang_right' valign='top'>";
		echo "</td>";
		echo "</tr>\n";
		echo "</table>\n";

		echo "<div>&nbsp;</div>";


		echo "<form method='post' action='".generer_url_ecrire('abonnes_import')."' enctype='multipart/form-data'>";
		echo "<input type='hidden' name='id_lettre' value='".$id_lettre."' />";
		
		switch ($etape) {
			default:
			case 1:
				debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/csv.png', false, "", _T('lettres:uploader_fichier_csv'));
				echo "<table border='0' width='100%' style='text-align: right'>";
				echo "<tr>";
				echo "	<td colspan='2'><span STYLE='font-size:10px; font-style: italic;'>";
				echo _T('lettres:note_importation');
				echo "	</span></td>";
				echo "</tr>";
				echo "<tr>";
				echo "	<td width='300'>";
				echo "		<input type='file' name='fichier_csv' />";
				echo "	</td>";
				echo "	<td> &nbsp; <INPUT TYPE='submit' NAME='charger' VALUE='"._T('lettres:charger')."' CLASS='fondo' STYLE='font-size:10px'></td>";
				echo "</tr>";
				echo "</table>";
				fin_cadre_enfonce();
				break;
			
			case 2:
				debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/csv.png', false, "", _T('lettres:donnees_fichier_csv'));
				echo "<table border='0' width='100%' style='text-align: right'>";
				echo "<tr>";
				echo "	<td width='300'><span class='verdana1'><B>"._T('lettres:donnees_fichier_csv')."</B></span> &nbsp;</td>";
				echo "	<td><span class='verdana1'>".$nom_fichier."</span></td>";
				echo "</tr>";
				echo "<tr>";
				echo "	<td width='300'><span class='verdana1'><B>"._T('lettres:nombre_emails_valides_fichier_csv')."</B></span> &nbsp;</td>";
				echo "	<td><span class='verdana1'>".sizeof($tableau_emails_valides)."</span></td>";
				echo "</tr>";
				echo "<tr>";
				echo "	<td width='300'><span class='verdana1'><B>"._T('lettres:nombre_emails_non_valides_fichier_csv')."</B></span> &nbsp;</td>";
				echo "	<td><span class='verdana1'>".sizeof($tableau_emails_non_valides)."</span></td>";
				echo "</tr>";
				echo "</table>";
				fin_cadre_enfonce();
				debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/preferences.png', false, "", _T('lettres:choix_format'));
				echo "<table border='0' width='100%' style='text-align: right'>";
				echo "<tr>";
				echo "	<td colspan='2'><span STYLE='font-size:10px; font-style: italic;'>";
				echo _T('lettres:note_preferences');
				echo "	</span></td>";
				echo "</tr>";
				echo "<tr>";
				echo "	<td width='300'><span class='verdana1'><B>"._T('lettres:format')."</B></span> &nbsp;</td>";
				echo "	<td>";
				echo "		<select name='format' SIZE='1' CLASS='fondl'>";
				echo '		<option value="mixte" '; if ($format == 'mixte') echo 'selected'; echo '>'._T('lettres:format_mixte').'</option>';
				echo '		<option value="html" '; if ($format == 'html') echo 'selected'; echo '>'._T('lettres:format_html').'</option>';
				echo '		<option value="texte" '; if ($format == 'texte') echo 'selected'; echo '>'._T('lettres:format_texte').'</option>';
				echo "		</select>";
				echo "	</td>";
				echo "</tr>";
				echo "</table>";
				fin_cadre_enfonce();
				if (sizeof($tableau_emails_valides) > 0) {
					debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/abonnes.png', false, "", _T('lettres:emails_valides_fichier_csv'));
					debut_cadre_couleur();
					echo '<div style="height: 200px; overflow: auto;">';
					echo "<table border='0' width='100%' style='text-align: right'>";
					foreach ($tableau_emails_valides as $cle => $valeur) {
						echo "<tr>";
						echo '	<td><input id="cle_'.$cle.'" name="abonnes[]" value="'.$valeur.'" type="checkbox" checked /></td>';
						echo '	<td><label for="cle_'.$cle.'">'.$valeur.'</label></td>';
						echo "</tr>";
					}
					echo "</table>";
					echo "</div>";
					fin_cadre_couleur();
					echo '<br />';
					echo "<table border='0' width='100%' style='text-align: right'>";
					echo "<tr>";
					echo "	<td width='300'>&nbsp;</td>";
					echo "	<td><INPUT TYPE='submit' NAME='ajouter_abonnes' VALUE='"._T('lettres:importer_ces_emails')."' CLASS='fondo' STYLE='font-size:10px'></td>";
					echo "</tr>";
					echo "</table>";
					fin_cadre_enfonce();
				}
				if (sizeof($tableau_emails_non_valides) > 0) {
					debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/impossible.png', false, "", _T('lettres:emails_non_valides_fichier_csv'));
					debut_cadre_couleur();
					echo '<div style="height: 100px; overflow: auto;">';
					echo "<table border='0' width='100%' style='text-align: right'>";
					foreach ($tableau_emails_non_valides as $cle => $valeur) {
						echo "<tr>";
						echo '	<td>&nbsp;&nbsp;</td>';
						echo '	<td>'.$valeur.'</td>';
						echo "</tr>";
					}
					echo "</table>";
					echo "</div>";
					fin_cadre_couleur();
					fin_cadre_enfonce();
				}
				break;

			case 3:
				debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/abonnes.png', false, "", _T('lettres:abonnes_ajoutes'));
				echo "<table border='0' width='100%' style='text-align: right'>";
				echo "<tr>";
				echo "	<td width='300'><span class='verdana1'><B>"._T('lettres:nombre_abonnes_ajoutes')."</B></span> &nbsp;</td>";
				echo "	<td><span class='verdana1'>".sizeof($tableau_abonnes_ajoutes)."</span></td>";
				echo "</tr>";
				echo "<tr>";
				echo "	<td width='300'><span class='verdana1'><B>"._T('lettres:nombre_abonnes_non_ajoutes')."</B></span> &nbsp;</td>";
				echo "	<td><span class='verdana1'>".sizeof($tableau_abonnes_non_ajoutes)."</span></td>";
				echo "</tr>";
				echo "<tr>";
				echo "	<td width='300'><span class='verdana1'><B>"._T('lettres:nombre_abonnes_selectionnes')."</B></span> &nbsp;</td>";
				echo "	<td><span class='verdana1'>".(sizeof($tableau_abonnes_non_ajoutes)+sizeof($tableau_abonnes_ajoutes))."</span></td>";
				echo "</tr>";
				echo "<tr>";
				echo "	<td width='300'><span class='verdana1'><B>"._T('lettres:abonnes_selectionnes')." :</B></span> &nbsp;</td>";
				echo "	<td></td>";
				echo "</tr>";
				echo "</table>";
				debut_cadre_couleur();
				echo '<div style="height: 200px; overflow: auto;">';
				echo "<table border='0' width='100%' style='text-align: right'>";
				foreach ($tableau_abonnes_ajoutes as $cle => $valeur) {
					echo "<tr>";
					echo '	<td><div style="display: none;"><input id="cle_'.$cle.'" name="abonnes[]" value="'.$valeur.'" type="checkbox" checked /></div></td>';
					echo '	<td><label for="cle_'.$cle.'">'.$valeur.'</label></td>';
					echo "</tr>";
				}
				foreach ($tableau_abonnes_non_ajoutes as $cle => $valeur) {
					echo "<tr>";
					echo '	<td><div style="display: none;"><input id="cle_'.$cle.'" name="abonnes[]" value="'.$valeur.'" type="checkbox" checked /></div></td>';
					echo '	<td><label for="cle_'.$cle.'">'.$valeur.'</label></td>';
					echo "</tr>";
				}
				echo "</table>";
				echo "</div>";
				fin_cadre_couleur();
				fin_cadre_enfonce();
				echo "<br />";
				debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/lettre-24.png', false, "", _T('lettres:inscrire_lettres'));
				echo "<table border='0' width='100%' style='text-align: right;'>";
				$requete_lettres = 'SELECT id_lettre, titre FROM spip_lettres ORDER BY titre';
				$resultat_lettres = spip_query($requete_lettres);
				while ($arr = @spip_fetch_array($resultat_lettres)) {
					echo "<tr>";
					echo "	<td width='15' style='text-align: right;'><input type='checkbox' value='".$arr['id_lettre']."' name='lettres[]' id='lettre_".$arr['id_lettre']."'";
					if ($arr['id_lettre'] == $id_lettre)
						echo " checked";
					echo " />&nbsp;</td>";
					echo "	<td width='200' style='text-align: left;'><label for='lettre_".$arr['id_lettre']."' class='verdana1'><B>".$arr['titre']."</B></label></td>";
					echo "</tr>";
				}
				echo "</table>";
				echo "<table border='0' width='100%' style='text-align: right;'>";
				echo "<tr>";
				echo "	<td width='300'>";
				echo "		&nbsp;";
				echo "	</td>";
				echo "	<td> &nbsp; <INPUT TYPE='submit' NAME='inscrire' VALUE='"._T('lettres:inscrire')."' CLASS='fondo' STYLE='font-size:10px'></td>";
				echo "</tr>";
				echo "</table>";
				fin_cadre_enfonce();
				break;

			case 4:
				foreach ($tableau_lettres as $id_lettre) {
					list($titre) = spip_fetch_array(spip_query('SELECT titre FROM spip_lettres WHERE id_lettre="'.$id_lettre.'" LIMIT 1'));
					$titre = '<a href="'.generer_url_ecrire('lettres_visualisation', 'id_lettre='.$id_lettre).'">'.$titre.'</a>';
					debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/lettre-24.png', false, "", $titre);
					if (sizeof($tableau_abonnes_ajoutes[$id_lettre]) > 0) {
						echo "<table border='0' width='100%' style='text-align: right'>";
						echo "<tr>";
						echo "	<td width='300'><span class='verdana1'><B>"._T('lettres:abonnes_ajoutes_a_cette_lettre')."</B></span> &nbsp;</td>";
						echo "	<td><span class='verdana1'>".sizeof($tableau_abonnes_ajoutes[$id_lettre])."</span></td>";
						echo "</tr>";
						echo "</table>";
						debut_cadre_couleur();
						echo '<div style="height: 100px; overflow: auto;">';
						echo "<table border='0' width='100%' style='text-align: right'>";
						foreach ($tableau_abonnes_ajoutes[$id_lettre] as $id_abonne => $email) {
							$url_abonne = generer_url_ecrire('abonnes_visualisation', '&id_abonne='.$id_abonne, '&');
							echo "<tr>";
							echo '	<td><a href="'.$url_abonne.'">'.$email.'</a></td>';
							echo "</tr>";
						}
						echo "</table>";
						echo "</div>";
						fin_cadre_couleur();
						echo "<br/>";
					}
					if (sizeof($tableau_abonnes_non_ajoutes[$id_lettre]) > 0) {
						echo "<table border='0' width='100%' style='text-align: right'>";
						echo "<tr>";
						echo "	<td width='300'><span class='verdana1'><B>"._T('lettres:abonnes_non_ajoutes_a_cette_lettre')."</B></span> &nbsp;</td>";
						echo "	<td><span class='verdana1'>".sizeof($tableau_abonnes_non_ajoutes[$id_lettre])."</span></td>";
						echo "</tr>";
						echo "</table>";
						debut_cadre_couleur();
						echo '<div style="height: 100px; overflow: auto;">';
						echo "<table border='0' width='100%' style='text-align: right'>";
						foreach ($tableau_abonnes_non_ajoutes[$id_lettre] as $id_abonne => $email) {
							$url_abonne = generer_url_ecrire('abonnes_visualisation', '&id_abonne='.$id_abonne, '&');
							echo "<tr>";
							echo '	<td><a href="'.$url_abonne.'">'.$email.'</a></td>';
							echo "</tr>";
						}
						echo "</table>";
						echo "</div>";
						fin_cadre_couleur();
					}
					fin_cadre_enfonce();
					echo "<br />";
				}
				break;
		}

		echo '</form>';


		fin_cadre_relief();

		echo '<br/>';

		fin_page();
	}

?>