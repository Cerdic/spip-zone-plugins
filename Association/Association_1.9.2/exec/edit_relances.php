<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007-2008
	* Bernard Blazin & Fran�ois de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
	include_spip('inc/presentation');
	include_spip ('inc/navigation_modules');
	
	function exec_edit_relances(){
		global $connect_statut, $connect_toutes_rubriques;
		
		debut_page(_T('Gestion pour Association'), "", "");
		
		$url_asso = generer_url_ecrire('association');
		$url_action_relances = generer_url_ecrire('action_relances','action=confirm');
		$url_edit_relances = generer_url_ecrire('edit_relances');
		$url_edit_labels = generer_url_ecrire('edit_labels');
		$indexation = lire_config('association/indexation');
		$url_retour = $_SERVER["HTTP_REFERER"];
		
		association_onglets();
		
		debut_gauche();
		
		debut_boite_info();
		echo association_date_du_jour();	
		fin_boite_info();
		
		debut_raccourcis();
		icone_horizontale(_T('asso:bouton_impression'), $url_edit_labels, _DIR_PLUGIN_ASSOCIATION."/img_pack/print-24.png","rien.gif");	
		icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION."/img_pack/retour-24.png","rien.gif");	
		fin_raccourcis();
		
		debut_droite();
		
		debut_cadre_relief(  "", false, "", $titre = _T('Tous les membres &agrave; relancer'));
		
		echo '<form method="post" action="'.$url_action_relances.'">';
		
		//MESSAGE
		echo '<fieldset>';
		echo '<legend>Message de relance</legend>';
		echo '<label for="sujet"><strong>'._T('asso:Sujet').' :</strong></label>';
		echo '<input name="sujet" type="text" value="'.stripslashes(_T('asso:titre_relance')).'" id="sujet" class="formo" />';
		echo '<label for="message"><strong>'._T('asso:Message').' :</strong></label>';
		echo '<textarea name="message" rows="15" id="message" class="formo" />'.stripslashes(_T('asso:message_relance')).'</textarea>';
		echo '</fieldset>';
		
		// FILTRES
		if ( isset ($_POST['statut_interne'] )) { $statut = $_POST['statut_interne']; }
		else { $statut_interne= "echu"; }
		
		echo '<table width="100%">';
		echo '<tr>';
		echo '<td style="text-align:right;">';
		echo '<input type="hidden" name="lettre" value="'.$lettre.'">';
		echo '<select name ="statut_interne" class="fondl" onchange="form.submit()">';
		foreach (array(ok,echu,relance,sorti,prospect) as $var) {
			echo '<option value="'.$var.'"';
			if ($var==$statut) {echo ' selected="selected"';}
			echo '> '._T('asso:adherent_entete_statut_'.$var).'</option>';
		}
		echo '</select>';
		echo '</td></tr>';
		echo '</table>';
		
		//TABLEAU
		echo "<table border=0 cellpadding=2 cellspacing=0 width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo '<tr bgcolor="#DBE1C5">';
		echo '<td><strong>';
		if ($indexation=="id_asso") { echo _T('asso:adherent_libelle_id_asso');}
		else { echo _T('asso:adherent_libelle_id_adherent');} 
		echo '</strong></td>';
		echo '<td><strong>Nom</strong></td>';
		echo '<td><strong>T&eacute;l&eacute;phone</strong></td>';
		echo '<td><strong>Portable</strong></td>';
		echo '<td><strong>Validit&eacute;</strong></td>';
		echo '<td><strong>Env</strong></td>';
		echo '</tr>';
		$query = spip_query ( "SELECT * FROM spip_auteurs_elargis a LEFT JOIN spip_auteurs b ON a.id_auteur=b.id_auteur WHERE email <> ''  AND statut_interne like '$statut_interne' AND statut_interne <> 'sorti' ORDER by nom_famille" );
		while ($data = spip_fetch_array($query)) {
			$id_auteur=$data['id_auteur'];
			$email=$data["email"];
			switch($data['statut_interne']) {
				case "echu": $class= "impair"; break;
				case "ok": $class="valide"; break;
				case "relance": $class="pair"; break;
				case "prospect": $class="prospect"; break;	   
			}
			
			echo '<tr> ';
			echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;text-align:right">';
			if ($indexation=="id_asso") { echo $data["id_asso"];}
			else { echo $data["a.id_auteur"];}
			echo '</td>';
			echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;">'.$data["nom_famille"].' '.$data['prenom'].'</td>';
			echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;">'.$data['telephone'].'</td>';
			echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;">'.$data['mobile'].'</td>';
			echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;">'.association_datefr($data['validite']).'</td>';
			echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;text-align:center;">';
			echo '<input name="id[]" type="checkbox" value="'.$id_auteur.'" checked="checked" >';
			echo '<input name="statut[]" type="hidden" value="'.$statut_interne.'">';
			echo '<input name="email[]" type="hidden" value="'.$email.'">';
			echo '</td>';
			echo '</tr>';
		}
		echo '</table>';
		echo '<input name="url_retour" type="hidden" value="'.$url_retour.'">';
		echo '<div style="float:right;"><input name="submit" type="submit" value="';
		if ( isset($action)) {echo _T('asso:bouton_'.$action);}
		else {echo _T('asso:bouton_envoyer');}
		echo '" class="fondo" /></div>';
		echo '</form>';	
		
		fin_cadre_relief();  
		fin_page();
	}
?>
