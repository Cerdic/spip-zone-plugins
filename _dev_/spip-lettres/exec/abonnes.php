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
	include_spip('inc/presentation');
	include_spip('inc/extra');


	function exec_abonnes() {
		global $id_abonne, $champs_extra, $table_des_abonnes;

		if (!autoriser('voir', 'lettres')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		$abonne = new abonne($id_abonne);

		if ($connect_statut != '0minirezo' or !$abonne->existe) {
			echo _T('avis_non_acces_page');
			echo fin_page();
			exit;
		}

		pipeline('exec_init',array('args'=>array('exec'=>'abonnes','id_abonne'=>$id_abonne),'data'=>''));

		if (!empty($_POST['format'])) {
			$abonne->enregistrer_format($_POST['format']);
			$url = generer_url_ecrire('abonnes','id_abonne='.$id_abonne, true);
			header('Location: '.$url);
			exit();
		}
		
		if (!empty($_POST['abonner'])) {
			$abonne->enregistrer_abonnement($_POST['id_parent']);
			$abonne->valider_abonnement($_POST['id_parent']);
			$url = generer_url_ecrire('abonnes','id_abonne='.$id_abonne, true);
			header('Location: '.$url);
			exit();
		}

		if (isset($_GET['desabonner'])) {
			$abonne->valider_desabonnement($_GET['desabonner']);
			$url = generer_url_ecrire('abonnes','id_abonne='.$id_abonne, true);
			header('Location: '.$url);
			exit();
		}

		if (!empty($_POST['changer_action'])) {
			switch ($_POST['action']) {
				case 'valider':
					$abonne->valider_abonnements_en_attente();
					$url = generer_url_ecrire('abonnes','id_abonne='.$id_abonne, true);
					break;
				case 'poubelle':
					$abonne->supprimer();
					$url = generer_url_ecrire('abonnes_tous');
					break;
			}
			header('Location: '.$url);
			exit();
		}
	

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('lettresprive:abonnes'), "naviguer", "abonnes_tous");

	
		debut_gauche();
		echo "<br />";
		debut_boite_info();
		echo "<div align='center'>\n";
		echo "<font face='Verdana,Arial,Sans,sans-serif' size='1'><b>"._T('lettresprive:numero_abonne')."</b></font>\n";
		echo "<br><font face='Verdana,Arial,Sans,sans-serif' size='6'><b>".$abonne->id_abonne."</b></font>\n";
		echo "</div>\n";
		fin_boite_info();
	
		echo '<br />';
		debut_boite_info();
		echo _T('lettresprive:aide_abonnes');
		fin_boite_info();

		debut_raccourcis();	
		icone_horizontale(_T('lettresprive:aller_liste_abonnes'), generer_url_ecrire('abonnes_tous'), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/abonne.png', '');
		icone_horizontale(_T('lettresprive:ajouter_abonne'), generer_url_ecrire('abonnes_edit'), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/abonne.png', 'creer.gif');
		fin_raccourcis();	
	
		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'abonnes','id_abonne'=>$abonne->id_abonne),'data'=>''));

		creer_colonne_droite();
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'abonnes','id_abonne'=>$abonne->id_abonne),'data'=>''));

    	debut_droite();
		debut_cadre_relief('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/abonne.png');	
	
		echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
		switch ($abonne->calculer_statut()) {
			case 'a_valider' :
				$logo_statut = "puce-blanche.gif";
				break;
			case 'valide' :
				$logo_statut = "puce-verte.gif";
				break;
			case 'vide' :
				$logo_statut = "puce-poubelle.gif";
				break;
		}
		echo "<tr width='100%'><td width='100%' valign='top'>";
		gros_titre(typo($abonne->email), $logo_statut);
		echo "</td>";
		echo "<td>", http_img_pack("rien.gif", ' ', "width='5'") ."</td>\n";
		echo "<td  align='$spip_lang_right' valign='top'>";
		icone($table_des_abonnes[$abonne->objet]['url_prive_titre'], generer_url_ecrire($table_des_abonnes[$abonne->objet]['url_prive'],$table_des_abonnes[$abonne->objet]['champ_id'].'='.$abonne->id_objet), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/abonne.png', "edit.gif");
		echo "</td>";
		echo "</tr>\n";
		echo "<tr><td>\n";
		echo "<div align='$spip_lang_left' style='padding: 5px; border: 1px dashed #aaaaaa;'>";
		echo "<font size=2 face='Verdana,Arial,Sans,sans-serif'>";
		if ($abonne->nom)
			echo _T('lettresprive:nom')." : <B>".$abonne->nom."</B><br />";
		echo _T('lettresprive:code')." : <B>".$abonne->code."</B><br />";
		echo _T('lettresprive:maj_le')." : <B>".affdate($abonne->maj)."</B>";
		echo "</font>";
		echo "</div>";
		if ($champs_extra AND $abonne->extra) {
			echo "<br />\n";
			echo extra_affichage($abonne->extra, "abonnes");
		}
		echo "</td>";
		echo "</tr>\n";
		echo "</table>\n";

		echo "<div>&nbsp;</div>";

		echo generer_url_post_ecrire("abonnes", "id_abonne=".$abonne->id_abonne, 'formulaire_format');
		debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/preferences.png', false, "", bouton_block_invisible('format')._T('lettresprive:boite_format').' : '.strtoupper($abonne->format));
		echo debut_block_invisible('format');
		echo "<table border='0' width='100%' style='text-align: right'>";
		echo "<tr>";
		echo "	<td><span class='verdana1'><B>"._T('lettresprive:changer_format')."</B></span> &nbsp;</td>";
		echo "	<td>";
		echo "<select name='format' CLASS='fondl'>";		
		echo '<option value="mixte"'.(($abonne->format == 'mixte') ? ' selected="selected"' : '' ).'>'._T('lettresprive:mixte').'</option>';
		echo '<option value="html"'.(($abonne->format == 'html') ? ' selected="selected"' : '' ).'>'._T('lettresprive:html').'</option>';
		echo '<option value="texte"'.(($abonne->format == 'texte') ? ' selected="selected"' : '' ).'>'._T('lettresprive:texte').'</option>';
		echo "</select>";
		echo "	</td>";
		echo "	<td> &nbsp; <INPUT TYPE='submit' NAME='changer_format' VALUE='"._T('lettresprive:changer')."' CLASS='fondo' STYLE='font-size:10px'></td>";
		echo "</tr>";
		echo "</table>";
		echo fin_block();
		fin_cadre_enfonce();	
		echo '</form>';


		echo generer_url_post_ecrire("abonnes", "id_abonne=".$abonne->id_abonne, 'formulaire_abonnement');
		$test_racine = spip_num_rows(spip_query('SELECT * FROM spip_abonnes_rubriques WHERE id_abonne="'.$abonne->id_abonne.'" AND id_rubrique=0'));
		debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/rubrique-24.png', false, "", ($test_racine ? '' : bouton_block_invisible('abonner'))._T('lettresprive:boite_abonnements'));
		$abonnements = spip_query('SELECT * FROM spip_abonnes_rubriques WHERE id_abonne="'.$abonne->id_abonne.'" ORDER BY date_abonnement DESC');
		if (spip_num_rows($abonnements) > 0) {
			echo "<TABLE BORDER=0 CELLPADDING=2 CELLSPACING=0 WIDTH='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
			while ($abo = spip_fetch_array($abonnements)) {	
		
				$id_rubrique = $abo['id_rubrique'];
				$statut = $abo['statut'];
				if ($id_rubrique == 0)
					$titre = _T('lettresprive:racine_du_site');
				else
					list($titre) = spip_fetch_array(spip_query('SELECT titre FROM spip_rubriques WHERE id_rubrique="'.$id_rubrique.'"'), SPIP_NUM);
		
				echo "<tr style='background-color: #eeeeee;'>";
				echo '<td width="12">'.http_img_pack('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/rubrique-12.png', "rub", '').'</td>';
				echo '<td><a href="'.generer_url_ecrire("naviguer","id_rubrique=".$id_rubrique).'">'.typo($titre).'</a></td>';
				echo '<td width="60" class="arial1">'._T('lettresprive:'.$statut).'</td>';
				echo '<td width="100" class="arial1">'.affdate($abo['date_abonnement']).'</td>';
				echo '<td width="70" class="arial1">'."<A href='" . generer_url_ecrire('abonnes', "id_abonne=$id_abonne&desabonner=".$id_rubrique) . "'>"._T('lettresprive:desabonner')."&nbsp;" . http_img_pack('croix-rouge.gif', "X", "width='7' height='7' border='0' align='middle'") ."</A>".'</td>';
				echo '</tr>';
			}
			echo '</table>';
		}
		if (!$test_racine) {
			echo debut_block_invisible('abonner');
			echo '<br />';
			debut_cadre_trait_couleur("", false, "", _T('lettresprive:nouvel_abonnement'));
			$selecteur_rubrique = charger_fonction('chercher_rubrique', 'inc');
			echo $selecteur_rubrique(0, 'rubrique', false);
			echo "<table width='100%'><tr>";
			echo "<td class='arial2' width='80%'>";
			echo _T('lettresprive:selectionnez_rubrique');
			echo "</td>\n";
			echo '<td>';
			echo '<div align="right">';
			echo "<INPUT TYPE='submit' NAME='abonner' CLASS='fondo' VALUE='"._T('lettresprive:abonner')."' STYLE='font-size:10px'>";
			echo "</div>";
			echo "</td>";
			echo "</tr></table>";
			fin_cadre_trait_couleur();
			echo fin_block();
		}
		fin_cadre_enfonce();
		echo '</form>';

		echo generer_url_post_ecrire("abonnes", "id_abonne=".$abonne->id_abonne, 'formulaire_statut');
		debut_cadre_relief();
		echo "<center><B>"._T('lettresprive:action')."</B>&nbsp;";
		echo "<SELECT NAME='action' SIZE='1' CLASS='fondl'>\n";
		echo '	<OPTION VALUE="aucune" SELECTED>'._T('lettresprive:aucune_action').'</OPTION>'."\n";
		if ($abonne->calculer_statut() == 'a_valider')
			echo '	<OPTION VALUE="valider">'._T('lettresprive:valider_abonnements_en_attente').'</OPTION>'."\n";
		echo '	<OPTION VALUE="poubelle">'._T('lettresprive:supprimer').'</OPTION>'."\n";
		echo "</SELECT>";
		echo "&nbsp;&nbsp;<INPUT TYPE='submit' NAME='changer_action' CLASS='fondo' VALUE='"._T('lettresprive:changer')."' STYLE='font-size:10px'>";
		echo '</center>';
		fin_cadre_relief();
		echo '</form>';

		fin_cadre_relief();
	
		echo '<br />';

		echo lettres_afficher_lettres(_T('lettresprive:lettres_recues'), array('SELECT' => 'A.id_lettre AS id_lettre, A.titre AS titre, A.date AS date, A.statut AS statut', 'FROM' => 'spip_lettres AS A, spip_abonnes_lettres AS AL', 'WHERE' => 'AL.id_abonne="'.$abonne->id_abonne.'" AND A.id_lettre=AL.id_lettre', 'ORDER BY' => 'A.date DESC'));

		echo fin_gauche();

		echo fin_page();

	}


#		$maj = affdate($abonne['maj']);


?>