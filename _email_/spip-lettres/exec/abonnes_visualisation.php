<?php

	include_spip('inc/lettres_fonctions');
	include_spip('inc/lettres_admin');
	include_spip('inc/presentation');
	include_spip('inc/logos');
	include_spip('inc/extra');


	function exec_abonnes_visualisation()
	{
		global $connect_id_abonne, $id_abonne, $champs_extra;
	
		lettres_verifier_droits();

		if (!empty($_POST['enregistrer'])) {
			$id_abonne	= intval($_GET['id_abonne']);
			$id_lettre	= intval($_POST['id_lettre']);
			$email		= addslashes($_POST['email']);
			$format		= addslashes($_POST['format']);

			if (!lettres_verifier_validite_email($email)) {
				$url_edition = generer_url_ecrire('abonnes_edition', "&email=$email&format=$format&erreur=1", '&');
				lettres_rediriger_javascript($url_edition);
			}

			if ($champs_extra)
				$champs_extra = extra_recup_saisie("abonnes");
			
			if (!$id_abonne) {
				list($existence, $id_abonne) = lettres_verifier_existence_abonne($email);
				if (!$existence) {
					$insertion = 'INSERT INTO spip_abonnes (email, code, format, maj'.($champs_extra ? ", extra" : '').') VALUES ("'.$email.'", "'.lettres_calculer_code().'", "'.$format.'", NOW()'.($champs_extra ? (', "'.addslashes($champs_extra).'"') : '').')';
					spip_query($insertion);
					$id_abonne = spip_insert_id();
				}
				if ($id_lettre AND lettres_verifier_action_possible($id_lettre, 'inscription', $email)) {
					$requete_inscription = 'INSERT INTO spip_abonnes_lettres (id_abonne, id_lettre, date_inscription, statut) VALUES ("'.$id_abonne.'", "'.$id_lettre.'", NOW(), "valide")';
					spip_query($requete_inscription);
				}
			} else {			
				$modification = 'UPDATE spip_abonnes SET email="'.$email.'", format="'.$format.'", maj=NOW()'.($champs_extra ? (", extra = '".addslashes($champs_extra)."'") : '').' WHERE id_abonne="'.$id_abonne.'"';
				spip_query($modification);
			}
			$url_abonne = generer_url_ecrire('abonnes_visualisation', 'id_abonne='.$id_abonne, '&');
			lettres_rediriger_javascript($url_abonne);
		}

		if (!empty($_POST['changer_action'])) {
			$action		= $_POST['action'];
			if ($action == 'poubelle') {
				$suppression = 'DELETE FROM spip_abonnes WHERE id_abonne="'.$id_abonne.'" LIMIT 1';
				spip_query($suppression);
				$suppression = 'DELETE FROM spip_abonnes_lettres WHERE id_abonne="'.$id_abonne.'"';
				spip_query($suppression);
				$suppression = 'DELETE FROM spip_abonnes_archives WHERE id_abonne="'.$id_abonne.'"';
				spip_query($suppression);
				$url = generer_url_ecrire('abonnes');
				lettres_rediriger_javascript($url);
			}
		}
	
		// Inscription à une lettre
		if (!empty($_POST['inscrire'])) {
		
			$insert = "INSERT INTO `spip_abonnes_lettres` ( `id_abonne` , `id_lettre` , `date_inscription` , `statut` )
					   VALUES ('$id_abonne', '".$_POST['id_lettre_inscr']."', NOW(), 'valide');";		
			spip_query($insert);
			$url_lettre = generer_url_ecrire('abonnes_visualisation', 'id_abonne='.$id_abonne, '&');
			lettres_rediriger_javascript($url_lettre);		
		}
	
		// Désinscription à une lettre
		 if (!empty($_GET['id_desabo'])) {
	 	
			$del = "DELETE FROM `spip_abonnes_lettres` WHERE `id_abonne`  = ". (int) $id_abonne . " AND `id_lettre` = " . (int) $_GET['id_desabo'];		
			spip_query($del);
			$url_lettre = generer_url_ecrire('abonnes_visualisation', 'id_abonne='.$id_abonne, '&');
			lettres_rediriger_javascript($url_lettre);		 	
		 }
	
	
		$id_abonne = intval($id_abonne);
		$result = spip_query("SELECT * FROM spip_abonnes WHERE id_abonne=".$id_abonne);

		if (!$abonne = spip_fetch_array($result)) die('erreur');

		debut_page(_T('lettres:abonne_edition'), "lettres", "abonnes");

	
		debut_gauche();
		echo "<br />";
		debut_boite_info();
		echo "<div align='center'>\n";
		echo "<font face='Verdana,Arial,Sans,sans-serif' size='1'><b>"._T('lettres:numero_abonne')."</b></font>\n";
		echo "<br><font face='Verdana,Arial,Sans,sans-serif' size='6'><b>$id_abonne</b></font>\n";
		echo "</div>\n";
		fin_boite_info();
	
		debut_raccourcis();	
		lettres_afficher_raccourci_liste_abonnes(_T('lettres:retour_liste'));
		lettres_afficher_raccourci_ajouter_abonne();
		fin_raccourcis();	
	
	
		table_abonnes_edit($abonne);

		fin_page();
	}

	function table_abonnes_edit($abonne)
	{
		global $connect_statut, $connect_id_abonne, $champs_extra, $options;

		$id_abonne	= $abonne['id_abonne'];
		$email		= $abonne['email'];
		$code		= $abonne['code'];
		$format		= $abonne['format'];
		$extra		= $abonne['extra'];
#		$maj=date('d-m-Y',strtotime($abonne['maj'])) .  ' &agrave; ' . date('H:i:s',strtotime($abonne['maj']));
		$maj = affdate($abonne['maj']);


		debut_droite();

		debut_cadre_relief('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/abonne.png');	
	
		echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
		echo "<tr width='100%'><td width='100%' valign='top'>";
		gros_titre($email);
		echo "</td>";
		echo "<td>", http_img_pack("rien.gif", ' ', "width='5'") ."</td>\n";
		echo "<td  align='$spip_lang_right' valign='top'>";
		icone(_T("lettres:modifier_abo"), generer_url_ecrire("abonnes_edition","id_abonne=$id_abonne"), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/abonne.png', "edit.gif");
		echo "</td>";
		echo "</tr>\n";
		echo "<tr><td>\n";
		echo "<div align='$spip_lang_left' style='padding: 5px; border: 1px dashed #aaaaaa;'>";
		echo "<font size=2 face='Verdana,Arial,Sans,sans-serif'>";
		echo _T('lettres:format')." : <B>$format</B><br />";
		echo _T('lettres:code')." : <B>$code</B>";
		echo "</font>";
		echo "</div>";
		if ($champs_extra AND $extra) {
			echo "<br />\n";
			extra_affichage($extra, "articles");
		}
		echo "</td></tr>\n";
		echo "</table>\n";

		echo "<div>&nbsp;</div>";

		$titre_barre = _T('lettres:maj_le').' : '.$maj;
		debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/date.png', false, "", $titre_barre);
		fin_cadre_enfonce();
	
		echo generer_url_post_ecrire("abonnes_visualisation", "id_abonne=$id_abonne", 'formulaire');
		$titre_barre = _T('lettres:abonnements');
		debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/lettre-24.png', false, "", bouton_block_invisible('abolettres').$titre_barre);

		$id_abonne = intval($id_abonne);
		$result = spip_query("SELECT * FROM `spip_lettres`, `spip_abonnes_lettres` WHERE  `spip_lettres`.id_lettre = `spip_abonnes_lettres`.id_lettre AND `id_abonne` = " . $id_abonne . " ORDER BY date_inscription DESC");	
		if (@spip_num_rows($result) > 0) {
			echo "<TABLE BORDER=0 CELLPADDING=2 CELLSPACING=0 WIDTH='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
			while ($let = spip_fetch_array($result)) {	
		
				$id_lettre = $let['id_lettre'];
				$date_insc = date('d-m-Y',strtotime($let['date_inscription']));
				$titre = stripslashes($let['titre']);
				$statut = $let['statut'];
		
				echo "<tr style='background-color: #eeeeee;'>";
				echo '<td width="80">'.$date_insc.'</td>';
				echo '<td><a href="'.generer_url_ecrire("lettres_visualisation","id_lettre=".$id_lettre, '&').'">'.$titre.'</a></td>';
				echo '<td width="70" style="text-align:center;">'._T('lettres:statut_'.$statut).'</td>';
				echo '<td width="70" style="text-align:center;font-size:10px;">'."<A href='" . generer_url_ecrire('abonnes_visualisation', "id_abonne=$id_abonne&id_desabo=".$let['id_lettre']) . "'>"._T('lettres:desabonner')."&nbsp;" . http_img_pack('croix-rouge.gif', "X", "width='7' height='7' border='0' align='middle'") ."</A>".'</td>';
				echo '</tr>';
			}
			echo '</table>';
		}
	
		$query = "SELECT `spip_lettres`.id_lettre,`spip_lettres`.titre FROM `spip_lettres` 
				  LEFT JOIN  `spip_abonnes_lettres` ON `spip_lettres`.id_lettre = `spip_abonnes_lettres`.id_lettre AND `id_abonne` = $id_abonne
				  WHERE id_abonne IS NULL
				  ORDER BY spip_lettres.id_lettre DESC";
		$result = spip_query($query);	
		if (@spip_num_rows($result) > 0) {
			echo debut_block_invisible('abolettres');
			echo "<table border='0' width='100%' style='text-align: right'>";
			echo "<tr>";
			echo "	<td><span class='verdana1'><B>"._T('lettres:inscrire_abo')."</B></span> &nbsp;</td>";
			echo "	<td>";
			echo "		<select name='id_lettre_inscr' SIZE='1' STYLE='width: 180px;' CLASS='fondl'>";
			while ($let = spip_fetch_array($result)) {
		
				echo '<option value="'.$let['id_lettre'].'" '; 
				if ($id_lettre_inscr == $let['id_lettre']) echo 'selected'; 		
				echo '>'.propre($let['titre']).'</option>';
			}
			echo "		</select><br/>";
			echo "	</td>";
			echo "	<td> &nbsp; <INPUT TYPE='submit' NAME='inscrire' CLASS='fondo' VALUE='"._T('lettres:inscrire')."' STYLE='font-size:10px'></td>";
			echo "</tr>";
			echo "</table>";
			echo fin_block();
		}
		fin_cadre_enfonce();	

		echo '<br />';
		debut_cadre_relief();
		echo "<center><B>"._T('lettres:etat_abonne')."</B>&nbsp;";
		echo "<SELECT NAME='action' SIZE='1' CLASS='fondl'>\n";
		echo '	<OPTION VALUE="aucune" SELECTED>'._T('lettres:etat_abonne_aucune').'</OPTION>'."\n";
		echo '	<OPTION VALUE="poubelle">'._T('lettres:etat_abonne_poubelle').'</OPTION>'."\n";
		echo "</SELECT>";
		echo "&nbsp;&nbsp;<INPUT TYPE='submit' NAME='changer_action' CLASS='fondo' VALUE='"._T('lettres:changer')."' STYLE='font-size:10px'>";
		echo '</center>';
		fin_cadre_relief();
		echo '</form>';
		echo "<div>&nbsp;</div>";
		fin_cadre_relief();
	
		echo '<br />';

		echo lettres_afficher_archives_abonne(_T('lettres:archives_abonne'), _DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/archives.png', $id_abonne, $nom_position='position_abonne');



	}
?>