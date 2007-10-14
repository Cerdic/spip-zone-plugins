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
	
	function exec_voir_activites(){
		global $connect_statut, $connect_toutes_rubriques;
		
		debut_page(_T('asso:titre_gestion_pour_association'), "", "");
		
		$id_evenement=$_REQUEST['id'];		
		
		$url_asso = generer_url_ecrire('association');
		$url_activites = generer_url_ecrire('activites');
		$url_edit_activite=generer_url_ecrire('edit_activite','action=modifie');
		$url_ajout_activite=generer_url_ecrire('edit_activite','action=ajoute');
		$url_pdf_activite=generer_url_ecrire('pdf_activite','id='.$id_evenement);
		$url_ajout_participation=generer_url_ecrire('ajout_participation');
		$url_action_activites = generer_url_ecrire('action_activites');

		

		$url_imprimer="../spip.php?page=print_inscriptions&id_evenement=".$id_evenement;
		
		if ( isset ($_POST['statut'] )) { $statut =  $_POST['statut']; }
		else { $statut= "%"; }
		
		association_onglets();
		
		debut_gauche();
		
		debut_boite_info();		
		echo association_date_du_jour();	
		$query = spip_query ("SELECT * FROM spip_evenements WHERE id_evenement='$id_evenement' ") ;
	 	while ($data = spip_fetch_array($query)) {
			echo '<p><strong>'.$data['date_debut'].'<br />'.$data['titre'].'</strong></p>';
			echo '<p>'._T('asso:activite_liste_legende').'</p>'; 	
		}
			
		// TOTAUX
		$query = spip_query ( "SELECT sum(inscrits) AS inscrits, sum(montant) AS encaisse FROM spip_asso_activites WHERE id_evenement='$id_evenement' AND statut ='ok' " );
		while ($data = spip_fetch_array($query)) {
			echo '<p><font color="blue"><strong>'._T('asso:activite_liste_nombre_inscrits',array('total' => $data['inscrits'])).'</strong></font><br />';
			echo '<font color="#9F1C30"><strong>'._T('asso:activite_liste_total_participations',array('total' => number_format($data['encaisse'], 2, ',', ' '))).'</strong></font><br/></p>';	
		}
		fin_boite_info();
		
		debut_raccourcis();
		icone_horizontale(_T('asso:activite_bouton_ajouter_inscription'), $url_ajout_activite.'&id='.$id_evenement, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/panier_in.gif','rien.gif' );
		icone_horizontale(_T('asso:activite_bouton_imprimer'), $url_pdf_activite, _DIR_PLUGIN_ASSOCIATION."/img_pack/print-24.png","rien.gif");	
		icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION."/img_pack/retour-24.png","rien.gif");	
		fin_raccourcis();
		
		debut_droite();
		
		debut_cadre_relief(  "", false, "", $titre = _T('asso:activite_titre_inscriptions_activites'));
		

	// PAGINATION ET FILTRES
		echo '<table width="100%">';
		echo '<tr>';
		$query = spip_query (" SELECT * FROM spip_evenements WHERE id_evenement='$id_evenement' ") ;
		while ($data = spip_fetch_array($query)) {
			$date = substr($data['date_debut'],0,10);
			$date = association_datefr($date);
			$titre = $data['titre'];
		}
		echo '<td style="text-align:right;">';
		echo '<form method="post" action="'.$url_voir_activites.'">';
		echo '<input type="hidden" name="id" value="'.$id_evenement.'">';
		echo '<select name ="statut" class="fondl" onchange="form.submit()">';
		echo '<option value="%"';
		if ($statut=="%") {echo ' selected="selected"';}
		echo '>'._T('asso:activite_entete_toutes').'</option>';
		echo '<option value="ok"';
		if ($statut=="ok") { echo ' selected="selected"'; }
		echo '>'._T('asso:activite_entete_validees').'</option>';
		echo '</select>';
		echo '</form>';
		echo '</table>';

	//TABLEAU
		echo '<form action="'.$url_action_activites.'" method="POST">';
		echo "<table border=0 cellpadding=2 cellspacing=0 width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo '<tr bgcolor="#DBE1C5">';
		echo '<td style="text-align:right"><strong>'._T('asso:activite_entete_id').'</strong></td>';
		echo '<td><strong>'._T('asso:activite_entete_date').'</strong></td>';
		echo '<td><strong>'._T('asso:activite_entete_nom').'</strong></td>';
		echo '<td><strong>'._T('asso:activite_entete_adherent').'</strong></td>';
		echo '<td style="text-align:right"><strong>'._T('asso:activite_entete_inscrits').'</strong></td>';
		echo '<td style="text-align:right"><strong>'._T('asso:activite_entete_montant').'</strong></td>';
		echo '<td><strong>'._T('asso:activite_entete_commentaire').'</strong></td>';
		echo '<td colspan="3" style="text-align:center"><strong>'._T('asso:activite_entete_action').'</strong></td>';
		echo '</tr>';

		$query = spip_query ("SELECT * FROM spip_asso_activites WHERE id_evenement='$id_evenement' AND statut like '$statut'  ORDER by id_activite") ;
	 
		while ($data = spip_fetch_array($query)) {
			
			if($data['statut']=="ok") { $class= "valide"; }
			else { $class="pair"; }
			
			echo '<tr> ';
			echo '<td class ='.$class.' style="text-align:right">'.$data['id_activite'].'</td>';
			echo '<td class ='.$class.' style="text-align:right">'.association_datefr($data['date']).'</td>';
			echo '<td class ='.$class.'>';
			if(empty($data['email'])) { echo $data['nom']; }
			else { echo '<a href="mailto:'.$data['email'].'">'.$data['nom'].'</a>'; }
			echo '</td>';
			echo '<td class ='.$class.' style="text-align:right">'.$data['id_adherent'].'</td>';
			echo '<td class ='.$class.' style="text-align:right">'.$data['inscrits'].'</td>';
			echo '<td class ='.$class.' style="text-align:right">'.number_format($data['montant'], 2, ',', ' ').'</td>';
			echo '<td class ='.$class.'>'.$data['commentaire'].'</td>';
			echo '<td class ='.$class.' style="text-align:center"><a href="'.$url_edit_activite.'&id='.$data['id_activite'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/edit-12.gif" title="'._T('asso:activite_bouton_maj_inscription').'"></a>';
					echo '<td class ='.$class.' style="text-align:center"><a href="'.$url_ajout_participation.'&id='.$data['id_activite'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/cotis-12.gif" title="'._T('asso:activite_bouton_ajouter_inscription').'"></a>';
			echo '<td class ='.$class.' style="text-align:center"><input name="delete[]" type="checkbox" value='.$data['id_activite'].'></td>';
			echo '</tr>';
		}     
		echo '</table>';

		echo '<table width="100%">';
		echo '<tr>';
		echo '<td  style="text-align:right;">';
		echo '<input type="submit" name="Submit" value="'._T('asso:activite_bouton_supprimer').'" class="fondo">';
		echo '</table>';
		echo '</form>';
		
		fin_cadre_relief();  
		fin_page();
	}
?>
