<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007
	* Bernard Blazin & François de Montlivault
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
		$url_action_relances = generer_url_ecrire('action_relances');
		$url_edit_relances = generer_url_ecrire('edit_relances');
		$url_edit_labels = generer_url_ecrire('edit_labels');
		$indexation = lire_config('association/indexation');
		
		association_onglets();
		
		debut_gauche();
		
		debut_boite_info();
		echo association_date_du_jour();	
		fin_boite_info();
		
		debut_droite();
		
		debut_cadre_relief(  "", false, "", $titre = _T('Tous les membres &agrave; relancer'));
		
		
		//MESSAGE
		echo '<fieldset>';
		echo '<legend>Message de relance</legend>';
		echo '<label for="sujet"><strong>'._T('asso:Sujet').' :</strong></label>';
		echo '<input name="sujet" type="text" value="'.stripslashes(_T('asso:titre_relance')).'" id="sujet" class="formo" />';
		echo '<label for="message"><strong>'._T('asso:Message').' :</strong></label>';
		echo '<textarea name="message" rows="15" id="message" class="formo" />'.stripslashes(_T('asso:message_relance')).'</textarea>';
		echo '</fieldset>';
		
		// FILTRES
		if ( isset ($_POST['statut'] )) { $statut = $_POST['statut']; }
		else { $statut= "echu"; }
		
		echo '<table width="100%">';
		echo '<tr>';
		echo '<td><a href=" '.$url_edit_labels.'">Etiquettes</a></td>';
		echo '<td style="text-align:right;">';
		echo '<form method="post" action="#">';
		echo '<input type="hidden" name="lettre" value="'.$lettre.'">';
		echo '<select name ="statut" class="fondl" onchange="form.submit()">';
		foreach (array('ok','echu','relance','sorti','prospect','tous') as $var) {
			echo '<option value="'.$var.'"';
			if ($var==$statut) {echo ' selected="selected"';}
			echo '> '._T('asso:adherent_entete_statut_'.$var).'</option>';
		}
		echo '</select>';
		echo '</form>';
		echo '</td></tr>';
		echo '</table>';
		
		//TABLEAU
		echo '<form method="post" action="'.$url_action_relances.'">';
		echo "<table border=0 cellpadding=2 cellspacing=0 width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo '<tr bgcolor="#DBE1C5">';
		echo '<td><strong>';
		if ($indexation=="id_asso") { echo _T('asso:adherent_libelle_id_asso');}
		else { echo _T('asso:adherent_libelle_id_adherent');} 
		echo '</strong></td>';
		echo '<td><strong>Nom</strong></td>';
		echo '<td><strong>Pr&eacute;nom</strong></td>';
		echo '<td><strong>T&eacute;l&eacute;phone</strong></td>';
		echo '<td><strong>Portable</strong></td>';
		echo '<td><strong>Validit&eacute;</strong></td>';
		echo '<td><strong>Env</strong></td>';
		echo '</tr>';
		$query = spip_query ("SELECT * FROM spip_asso_adherents WHERE email <> ''  AND statut like '$statut' AND statut <> 'sorti' ORDER by nom" );
		$i=0;
		while ($data = spip_fetch_array($query)) {
			$i++;
			$id_adherent=$data['id_adherent'];
			
			switch($data['statut']) {
				case "echu": $class= "impair"; break;
				case "ok": $class="valide"; break;
			    case "relance": $class="pair"; break;
				case "prospect": $class="prospect"; break;	   
			}
			
			echo '<tr> ';
			echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;text-align:right">';
			if ($indexation=="id_asso") { echo $data["id_asso"];}
			else { echo $data["id_adherent"];}
			echo '</td>';
			echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;">'.$data["nom"].'</td>';
			echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;">'.$data['prenom'].'</td>';
			echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;">'.$data['telephone'].'</td>';
			echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;">'.$data['portable'].'</td>';
			echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;">'.association_datefr($data['validite']).'</td>';
			echo '<td class ='.$class.' style="border-top: 1px solid #CCCCCC;text-align:center;">';
			echo '<input name="relance[]" type="checkbox" value="'.$data['id_adherent'].'" checked >';
			echo '<input type="hidden" name="statut[]" value="'.$statut.'">';
			echo '<input type="hidden" name="email[]" value="'.$data["email"].'">';
			echo '</td>';
			echo '</tr>';
		}
		echo '</table>';
		
		echo '<div style="float:right;"><input name="submit" type="submit" value="';
		if ( isset($action)) {echo _T('asso:bouton_'.$action);}
		else {echo _T('asso:bouton_envoyer');}
		echo '" class="fondo" /></div>';
		echo '</form>';	
		
		fin_cadre_relief();  
		fin_page();
	}
?>
