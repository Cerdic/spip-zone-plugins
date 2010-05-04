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
if (!defined("_ECRIRE_INC_VERSION")) return;


	include_spip('inc/presentation');
	include_spip ('inc/navigation_modules');

	function exec_edit_labels(){
		
		include_spip('inc/autoriser');
		if (!autoriser('configurer')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}
		
		$url_asso = generer_url_ecrire('association');
		$url_action_labels = generer_url_ecrire('action_labels');
		$url_edit_relances = generer_url_ecrire('edit_relances');		
		$url_retour = $_SERVER['HTTP_REFERER'];
		$indexation = lire_config('association/indexation');
		
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
		
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo association_date_du_jour();	
		echo fin_boite_info(true);	
		
		
		$res=association_icone(_T('asso:bouton_retour'),  $url_retour, "retour-24.png");	
		echo bloc_des_raccourcis($res);
		
		echo debut_droite("",true);
		
		echo debut_cadre_relief(  "", false, "", $titre = _T('Toutes les &eacute;tiquettes &agrave; g&eacute;n&eacute;rer'));
		
		$statut_interne= "ok";
		if ( isset ($_POST['statut_interne'] )) { $statut_interne = $_POST['statut_interne']; } 
		echo '<table>';
		echo '<tr>';
		// Menu de sélection
		echo '<td style="text-align:right;">';
		echo '<form method="post" action="#">';
		echo '<input type="hidden" name="lettre" value="'.$lettre.'">';
		echo '<select name ="statut_interne" class="fondl" onchange="form.submit()">';
		foreach (array(ok,echu,relance,sorti,lire_config('inscription2/statut_interne')) as $var) {
			echo '<option value="'.$var.'"';
			if ($statut_interne==$var) {echo ' selected="selected"';}
			echo '> '._T('asso:adherent_entete_statut_'.$var).'</option>';
		}
		echo '</select>';
		echo '</form>';
		echo '</td></tr>';
		echo '</table>';
		
		echo '<form method="post" action="'.$url_action_labels.'">';
		echo "<table border=0 cellpadding=2 cellspacing=0 width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo '<tr bgcolor="#DBE1C5">';
		echo '<td><strong>';
		if ($indexation=="id_asso") { echo _T('asso:adherent_libelle_id_asso');}
		else { echo _T('asso:adherent_libelle_id_adherent');} 
		echo '<strong></td>';
		echo '<td><strong>Nom</strong></td>';
		echo '<td><strong>Adresse</strong></td>';
		echo '<td><strong>Env</strong></td>';
		echo '</tr>';
		$query = sql_select("*",_ASSOCIATION_AUTEURS_ELARGIS, "statut_interne like '$statut_interne'", '', "nom_famille, sexe DESC" );
		// originale semblait contenir une vieillerie:
		//  spip_auteurs_elargis INNER JOIN spip_asso_adherents ON spip_auteurs_elargis.id_auteur=spip_asso_adherents.id_auteur 
		while ($data = sql_fetch($query))  {
			$id_adherent=$data['id_adherent'];
			$sexe=$data['sexe'];
			
			switch($data['statut_interne']) {
				case "echu": $class= "impair"; break;
				case "ok": $class="valide"; break;
				case "relance": $class="pair"; break;
				case "prospect": $class="prospect"; break;	   
		     }
			
			echo '<tr> ';
			echo '<td style="text-align:right;vertical-align:top;" class="'.$class. ' border1">';
			if ($indexation=="id_asso") { echo $data["id_asso"];}
			else { echo $data["id_adherent"];}
			echo '</td>';
			echo '<td style="vertical-align:top;" class="'.$class. ' border1">';
			if ($sexe=='H'){ echo 'M.'; }
			elseif ($sexe=='F'){ echo 'Mme'; }
				else { echo '&nbsp;'; }
			echo ' '.$data['prenom'].' '.$data["nom_famille"].'</td>';
			echo '<td style="vertical-align:top;" class="'.$class. ' border1">'.$data['adresse'].'<br />'.$data['code_postal'].' '.$data['ville'].'</td>';
			echo '<td style="text-align:center;;vertical-align:top;" class="'.$class. ' border1">';
			echo '<input name="label[]" type="checkbox" value="'.$data['id'].'" checked="checked" />';
			echo '</td>';
			echo '</tr>';
		}
		echo '<tr> ';
		echo '<td colspan="4" style="text-align:right;"><input type="submit" name="Submit" value="Etiquettes" class="fondo"></td>';
		echo '</tr>';
		echo '</table>';
		echo '</form>';
		
		fin_cadre_relief();  
		echo fin_gauche(), fin_page();
	}
?>
