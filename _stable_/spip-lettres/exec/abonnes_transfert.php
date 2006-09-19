<?php


	/**
	 * SPIP-Lettres : plugin de gestion de lettres d'information
	 *
	 * Copyright (c) 2006
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/


	include_spip('inc/lettres_fonctions');
	include_spip('inc/lettres_admin');
	include_spip('inc/presentation');


	function exec_abonnes_transfert() {
	
		lettres_verifier_droits();

		if (empty($_REQUEST['id_lettre'])) {
			$url = generer_url_ecrire('abonnes', '', '&');
			lettres_rediriger_javascript($url);
		}

		$id_lettre = intval($_REQUEST['id_lettre']);

		if (!empty($_POST['transferer'])) {
			$tableau_abonnes	= $_POST['abonnes'];
			$tableau_lettres	= $_POST['lettres'];
			if ((sizeof($tableau_abonnes) == 0) AND (sizeof($tableau_lettres) == 0)) {
				$etape = 1;
			} else {
				$etape = 2;
				$tableau_abonnes_ajoutes = array();
				$tableau_abonnes_non_ajoutes = array();
				foreach ($tableau_lettres as $tableau_id_lettre) {
					foreach ($tableau_abonnes as $email) {
						if (lettres_verifier_action_possible($tableau_id_lettre, 'inscription', $email)) {
							$id_abonne = lettres_recuperer_id_abonne_depuis_email($email);
							spip_query('INSERT INTO spip_abonnes_lettres (id_abonne, id_lettre, date_inscription, statut) VALUES ("'.$id_abonne.'", "'.$tableau_id_lettre.'", NOW(), "valide")');
							lettres_ajouter_statistique_import($id_lettre);
							$tableau_abonnes_ajoutes[$tableau_id_lettre][$id_abonne] = $email;
						} else {
							$id_abonne = lettres_recuperer_id_abonne_depuis_email($email);
							$tableau_abonnes_non_ajoutes[$tableau_id_lettre][$id_abonne] = $email;
						}
					}
				}
			}
		} else {
			$etape = 1;
		}
			

		debut_page(_T('lettres:transfert'), "lettres", "abonnes");
	
		debut_gauche();
		lettres_afficher_etapes_transfert($etape);
		
		debut_raccourcis();	
		lettres_afficher_raccourci_liste_abonnes(_T('lettres:aller_liste_abonnes'));
		lettres_afficher_raccourci_retourner_lettre($id_lettre);
		fin_raccourcis();	
	

		debut_droite();

		debut_cadre_relief('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/transfert.png');

		echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
		echo "<tr width='100%'><td width='100%' valign='top'>";
		gros_titre(_T('lettres:transfert_etape_'.$etape));
		echo "</td>";
		echo "<td>", http_img_pack("rien.gif", ' ', "width='5'") ."</td>\n";
		echo "<td  align='$spip_lang_right' valign='top'>";
		echo "</td>";
		echo "</tr>\n";
		echo "<tr><td>\n";
		echo "<br />\n";
		echo "<div align='$spip_lang_left' style='padding: 5px; border: 1px dashed #aaaaaa;'>";
		echo "<font size=2 face='Verdana,Arial,Sans,sans-serif'>";
		list($titre) = spip_fetch_array(spip_query('SELECT titre FROM spip_lettres WHERE id_lettre="'.$id_lettre.'"'),SPIP_NUM);
		echo _T('lettres:transferer_depuis').' : <B><a href="'.generer_url_ecrire('lettres_visualisation', "id_lettre=$id_lettre").'">'.$titre.'</a></B><br />';
		echo "</font>";
		echo "</div>";
		echo "</td></tr>\n";
		echo "</table>\n";

		echo "<div>&nbsp;</div>";


		echo generer_url_post_ecrire("abonnes_transfert", "", 'formulaire');
		echo "<input type='hidden' name='id_lettre' value='".$id_lettre."' />";
		
		
		switch ($etape) {
			default:
			case 1:
				debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/abonnes.png', false, "", _T('lettres:selectionner_abonnes_a_transferer'));
				debut_cadre_couleur();
				echo '<div style="height: 200px; overflow: auto;">';
				echo "<table border='0' width='100%' style='text-align: right'>";
				$requete_abonnes = 'SELECT DISTINCT(A.id_abonne),
										A.email,
										AL.statut
									FROM spip_abonnes AS A
									LEFT JOIN spip_abonnes_lettres AS AL ON AL.id_abonne=A.id_abonne
									WHERE AL.statut="valide"
										AND AL.id_lettre="'.$id_lettre.'"
									ORDER BY A.email ASC';
				$resultat_abonnes = spip_query($requete_abonnes);
				while ($arr = @spip_fetch_array($resultat_abonnes)) {
					echo "<tr>";
					echo '	<td width="20"><input id="abonnes_'.$i.'" name="abonnes[]" value="'.$arr['email'].'" type="checkbox" checked /></td>';
					echo '	<td><label for="abonnes_'.$i.'">'.$arr['email'].'</label></td>';
					echo "</tr>";
					$i++;
				}
				echo "</table>";
				echo "</div>";
				fin_cadre_couleur();
				fin_cadre_enfonce();

				debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/lettre-24.png', false, "", _T('lettres:selectionner_lettres_destinataires'));
				debut_cadre_couleur();
				echo '<div style="height: 200px; overflow: auto;">';
				echo "<table border='0' width='100%' style='text-align: right'>";
				$requete_lettres = 'SELECT id_lettre,
										titre
									FROM spip_lettres
									WHERE id_lettre!="'.$id_lettre.'"
									ORDER BY titre ASC';
				$resultat_lettres = spip_query($requete_lettres);
				while ($arr = @spip_fetch_array($resultat_lettres)) {
					echo "<tr>";
					echo '	<td width="20"><input id="lettres_'.$i.'" name="lettres[]" value="'.$arr['id_lettre'].'" type="checkbox" /></td>';
					echo '	<td><label for="lettres_'.$i.'">'.$arr['titre'].'</label></td>';
					echo "</tr>";
					$i++;
				}
				echo "</table>";
				echo "</div>";
				fin_cadre_couleur();
				echo "<table border='0' width='100%' style='text-align: right;'>";
				echo "<tr>";
				echo "	<td width='300'>";
				echo "		&nbsp;";
				echo "	</td>";
				echo "	<td> &nbsp; <INPUT TYPE='submit' NAME='transferer' VALUE='"._T('lettres:transferer')."' CLASS='fondo' STYLE='font-size:10px'></td>";
				echo "</tr>";
				echo "</table>";
				fin_cadre_enfonce();
				break;
			
			case 2:
				foreach ($tableau_lettres as $tableau_id_lettre) {
					list($titre) = spip_fetch_array(spip_query('SELECT titre FROM spip_lettres WHERE id_lettre="'.$tableau_id_lettre.'" LIMIT 1'),SPIP_NUM);
					$titre = '<a href="'.generer_url_ecrire('lettres_visualisation', 'id_lettre='.$tableau_id_lettre).'">'.$titre.'</a>';
					debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/lettre-24.png', false, "", $titre);
					if (sizeof($tableau_abonnes_ajoutes[$tableau_id_lettre]) > 0) {
						echo "<table border='0' width='100%' style='text-align: right'>";
						echo "<tr>";
						echo "	<td width='300'><span class='verdana1'><B>"._T('lettres:abonnes_ajoutes_a_cette_lettre')."</B></span> &nbsp;</td>";
						echo "	<td><span class='verdana1'>".sizeof($tableau_abonnes_ajoutes[$tableau_id_lettre])."</span></td>";
						echo "</tr>";
						echo "</table>";
						debut_cadre_couleur();
						echo '<div style="height: 100px; overflow: auto;">';
						echo "<table border='0' width='100%' style='text-align: right'>";
						foreach ($tableau_abonnes_ajoutes[$tableau_id_lettre] as $id_abonne => $email) {
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
					if (sizeof($tableau_abonnes_non_ajoutes[$tableau_id_lettre]) > 0) {
						echo "<table border='0' width='100%' style='text-align: right'>";
						echo "<tr>";
						echo "	<td width='300'><span class='verdana1'><B>"._T('lettres:abonnes_non_ajoutes_a_cette_lettre')."</B></span> &nbsp;</td>";
						echo "	<td><span class='verdana1'>".sizeof($tableau_abonnes_non_ajoutes[$tableau_id_lettre])."</span></td>";
						echo "</tr>";
						echo "</table>";
						debut_cadre_couleur();
						echo '<div style="height: 100px; overflow: auto;">';
						echo "<table border='0' width='100%' style='text-align: right'>";
						foreach ($tableau_abonnes_non_ajoutes[$tableau_id_lettre] as $id_abonne => $email) {
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