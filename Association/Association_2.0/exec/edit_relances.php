<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007-2008
	* Bernard Blazin & François de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
if (!defined("_ECRIRE_INC_VERSION")) return;


	include_spip('inc/presentation');
	include_spip ('inc/navigation_modules');
	
	function exec_edit_relances(){
		
		//debut_page(_T('Gestion pour Association'), "", "");
		 $commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('Gestion pour Association')) ;
		$url_asso = generer_url_ecrire('association');
		$url_action_relances = generer_url_ecrire('action_relances','agir=confirm');
		$url_edit_relances = generer_url_ecrire('edit_relances');
		$url_edit_labels = generer_url_ecrire('edit_labels');
		$indexation = lire_config('association/indexation');
		$url_retour = $_SERVER["HTTP_REFERER"];
		
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo association_date_du_jour();	
		echo fin_boite_info(true);
		
		
		$res=icone_horizontale(_T('asso:bouton_impression'), $url_edit_labels, _DIR_PLUGIN_ASSOCIATION_ICONES."print-24.png","rien.gif",false);
			
		$res.=icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION_ICONES."retour-24.png","rien.gif",false);	
		 echo bloc_des_raccourcis($res);
		
		echo debut_droite("",true);
		
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
		$query = association_auteurs_elargis_select("*", " a LEFT JOIN spip_auteurs b ON a.id_auteur=b.id_auteur", " a.email <> ''  AND statut_interne like '$statut_interne' AND statut_interne <> 'sorti'", '', "nom_famille" );
		while ($data = spip_fetch_array($query)) {
			$id_auteur=$data['id_auteur'];
			$email=$data["email"];
			//$statut_interne=$data['statut_interne'];
			switch($data['statut_interne']) {
				case "echu": $class= "impair"; break;
				case "ok": $class="valide"; break;
				case "relance": $class="pair"; break;
				case "prospect": $class="prospect"; break;	   
			}
			
			echo '<tr> ';
			echo '<td class="'.$class. ' border1" style="text-align:right">';
			if ($indexation=="id_asso") { echo $data["id_asso"];}
			else { echo $data["a.id_auteur"];}
			echo '</td>';
			echo '<td class="'.$class. ' border1">'.$data["nom_famille"].' '.$data['prenom'].'</td>';
			echo '<td class="'.$class. ' border1">'.$data['telephone'].'</td>';
			echo '<td class="'.$class. ' border1">'.$data['mobile'].'</td>';
			echo '<td class="'.$class. ' border1">'.association_datefr($data['validite']).'</td>';
			echo '<td class="'.$class. ' border1" style="text-align:center;">';
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
		  echo fin_gauche(),fin_page(); 
	}
?>
