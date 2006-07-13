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


	function exec_abonnes_export() {
	
		lettres_verifier_droits();

		if (!empty($_GET['timestamp']) AND $_GET['telecharger'] == 'oui') {
			$timestamp = intval($_GET['timestamp']);
			$fichier = _DIR_CACHE.'export-'.$timestamp.'.csv';
			$fp = fopen($fichier, 'rb');
			header("Content-Type: application/csv-tab-delimited-table");
			header("Content-disposition: filename=export-".$timestamp.".csv");
			fpassthru($fp);
			fclose($fp);
			exit();
		}

		if (!empty($_POST['exporter'])) {
			$tableau_abonnes	= $_POST['abonnes'];
			$timestamp			= $_POST['timestamp'];
			if (sizeof($tableau_abonnes) == 0) {
				$etape = 1;
			} else {
				$etape = 2;
				foreach ($tableau_abonnes as $valeur)
					$csv.= $valeur."\n";
				$fichier = _DIR_CACHE.'export-'.$timestamp.'.csv';
				$fp = fopen($fichier, 'w');
				fwrite($fp, $csv);
				fclose($fp);
				$url_fichier = generer_url_ecrire('abonnes_export', '&telecharger=oui&timestamp='.$timestamp, '&');
			}
		} else {
			$etape = 1;
		}
			

		debut_page(_T('lettres:export_csv'), "lettres", "abonnes");
	
		debut_gauche();
		lettres_afficher_etapes_export($etape);
		
		debut_raccourcis();	
		lettres_afficher_raccourci_liste_abonnes(_T('lettres:aller_liste_abonnes'));
		lettres_afficher_raccourci_liste_lettres(_T('lettres:aller_liste_lettres'));
		fin_raccourcis();	
	

		debut_droite();

		debut_cadre_relief('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/export.png');

		echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
		echo "<tr width='100%'><td width='100%' valign='top'>";
		gros_titre(_T('lettres:export_etape_'.$etape));
		echo "</td>";
		echo "<td>", http_img_pack("rien.gif", ' ', "width='5'") ."</td>\n";
		echo "<td  align='$spip_lang_right' valign='top'>";
		echo "</td>";
		echo "</tr>\n";
		echo "</table>\n";

		echo "<div>&nbsp;</div>";


		echo generer_url_post_ecrire("abonnes_export", "", 'formulaire');
		echo "<input type='hidden' name='timestamp' value='".mktime()."' />";
		if ($id_lettre)
			echo "<input type='hidden' name='id_lettre' value='".$id_lettre."' />";
		
		
		switch ($etape) {
			default:
			case 1:
				debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/abonnes.png', false, "", _T('lettres:selectionner_abonnes_a_exporter'));
				debut_cadre_couleur();
				echo '<div style="height: 200px; overflow: auto;">';
				echo "<table border='0' width='100%' style='text-align: right'>";
				$id_lettre = intval($_GET['id_lettre']);
				if ($id_lettre) $clause_where = ' AND AL.id_lettre="'.$id_lettre.'" ';
				$requete_abonnes = 'SELECT DISTINCT(A.email),
										AL.statut
									FROM spip_abonnes AS A
									LEFT JOIN spip_abonnes_lettres AS AL ON AL.id_abonne=A.id_abonne
									WHERE AL.statut="valide"
										'.$clause_where.'
									ORDER BY A.email ASC';
				$resultat_abonnes = spip_query($requete_abonnes);
				while ($arr = @spip_fetch_array($resultat_abonnes)) {
					echo "<tr>";
					echo '	<td width="20"><input id="cle_'.$i.'" name="abonnes[]" value="'.$arr['email'].'" type="checkbox" checked /></td>';
					echo '	<td><label for="cle_'.$i.'">'.$arr['email'].'</label></td>';
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
				echo "	<td> &nbsp; <INPUT TYPE='submit' NAME='exporter' VALUE='"._T('lettres:exporter')."' CLASS='fondo' STYLE='font-size:10px'></td>";
				echo "</tr>";
				echo "</table>";
				fin_cadre_enfonce();
				break;
			
			case 2:
				debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/csv.png', false, "", _T('lettres:telechargement_fichier_csv'));
				echo '<div style="display: none;">';
				foreach ($tableau_abonnes as $email) {
					echo '	<input name="abonnes[]" value="'.$email.'" type="checkbox" checked />';
				}
				echo "</div>";
				echo "<table border='0' width='100%'>";
				echo "<tr>";
				echo "	<td><em>"._T('lettres:telechargement_note')."</em></td>";
				echo "</tr>";
				echo "<tr>";
				echo "	<td><center><a href='".$url_fichier."'><img valign='middle' src='"._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/mime-csv.png'."' alt='csv' border='0' /></a> <a href='".$url_fichier."'>"._T('lettres:telecharger_le_fichier')."</a></center></td>";
				echo "</tr>";
				echo "</table>";
				fin_cadre_enfonce();
				break;
		}

		echo '</form>';


		fin_cadre_relief();

		echo '<br/>';

		fin_page();
	}

?>