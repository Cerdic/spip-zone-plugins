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

	include_spip ('inc/navigation_modules');
	
	function exec_voir_activites(){
		
		include_spip('inc/autoriser');
		if (!autoriser('configurer')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}
		
		$id_evenement= intval(_request('id'));
		
		$url_asso = generer_url_ecrire('association');
		$url_activites = generer_url_ecrire('activites');
		$url_edit_activite=generer_url_ecrire('edit_activite','agir=modifie');
		$url_ajout_activite=generer_url_ecrire('edit_activite','agir=ajoute');
		$url_pdf_activite=generer_url_ecrire('pdf_activite','id='.$id_evenement);
		$url_ajout_participation=generer_url_ecrire('ajout_participation');
		$url_action_activites = generer_url_ecrire('action_activites');
		
		if ( isset ($_POST['statut'] )) { $statut =  $_POST['statut']; }
		else { $statut= "%"; }
		
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('Gestion pour Association')) ;
		
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);		
		echo association_date_du_jour();	
		$query = sql_select("*", "spip_evenements", "id_evenement=$id_evenement") ;
	 	while ($data = sql_fetch($query)) {
			echo '<p><strong>'.$data['date_debut'].'<br />'.$data['titre'].'</strong></p>';
			echo '<p>'._T('asso:activite_liste_legende').'</p>'; 	
		}
			
		// TOTAUX
		$query = sql_select("sum(inscrits) AS inscrits, sum(montant) AS encaisse ", "spip_asso_activites", "id_evenement='$id_evenement' AND statut ='ok' " );
		while ($data = sql_fetch($query)) {
			echo '<p><font color="blue"><strong>'._T('asso:activite_liste_nombre_inscrits',array('total' => $data['inscrits'])).'</strong></font><br />';
			echo '<font color="#9F1C30"><strong>'._T('asso:activite_liste_total_participations',array('total' => number_format($data['encaisse'], 2, ',', ' '))).'</strong></font><br/></p>';	
		}
		echo fin_boite_info(true);
		
		
		$res=association_icone(_T('asso:activite_bouton_ajouter_inscription'),  $url_ajout_activite.'&id='.$id_evenement, 'panier_in.gif');
		$res.=association_icone(_T('asso:activite_bouton_voir_liste_inscriptions'),  $url_pdf_activite, "print-24.png");	
		$res.=association_icone(_T('asso:bouton_retour'),  $url_retour, "retour-24.png");	
		echo bloc_des_raccourcis($res);
		
		echo debut_droite("",true);
		
		echo debut_cadre_relief(  "", false, "", $titre = _T('asso:activite_titre_inscriptions_activites'));
		

	// PAGINATION ET FILTRES
		echo '<table width="100%">';
		echo '<tr>';
		$data = sql_fetsel("*", "spip_evenements", "id_evenement=$id_evenement") ;
		$date = substr($data['date_debut'],0,10);
		$date = association_datefr($date);
		$titre = $data['titre'];

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
		echo '<th><strong>'._T('asso:activite_entete_id').'</strong></th>';
		echo '<th><strong>'._T('asso:activite_entete_date').'</strong></th>';
		echo '<th><strong>'._T('asso:activite_entete_nom').'</strong></th>';
		echo '<th><strong>'._T('asso:activite_entete_adherent').'</strong></th>';
		echo '<th><strong>'._T('asso:activite_entete_inscrits').'</strong></th>';
		echo '<th><strong>'._T('asso:activite_entete_montant').'</strong></th>';
		echo '<th colspan="3"><strong>'._T('asso:activite_entete_action').'</strong></th>';
		echo '</tr>';
		$query = sql_select("*", "spip_asso_activites", "id_evenement=$id_evenement AND statut like '$statut'  ", '', "id_activite") ;
	 
		while ($data = sql_fetch($query)) {
			
			if($data['statut']=="ok") { $class= "valide"; }
			else { $class="pair"; }
			
			echo '<tr> ';
			echo '<td style="text-align:right;" class="'.$class. ' border1">'.$data['id_activite'].'</td>';
			echo '<td style="text-align:right;" class="'.$class. ' border1">'.association_datefr($data['date']).'</td>';
			echo '<td class="'.$class. ' border1">';
			if(empty($data['email'])) { echo $data['nom']; }
			else { echo '<a href="mailto:'.$data['email'].'">'.$data['nom'].'</a>'; }
			echo '</td>';
			echo '<td style="text-align:right;" class="'.$class. ' border1">'.$data['id_adherent'].'</td>';
			echo '<td style="text-align:right;" class="'.$class. ' border1">'.$data['inscrits'].'</td>';
			echo '<td style="text-align:right;" class="'.$class. ' border1">'.number_format($data['montant'], 2, ',', ' ').'</td>';
			echo '<td style="text-align:center;" class="'.$class. ' border1"><a href="'.$url_edit_activite.'&id='.$data['id_activite'].'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'edit-12.gif" title="'._T('asso:activite_bouton_maj_inscription').'"></a>';
			echo '<td style="text-align:center;" class="'.$class. ' border1"><a href="'.$url_ajout_participation.'&id='.$data['id_activite'].'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'cotis-12.gif" title="'._T('asso:activite_bouton_ajouter_inscription').'"></a>';
			echo '<td style="text-align:center;" class="'.$class. ' border1"><input name="delete[]" type="checkbox" value='.$data['id_activite'].'></td>';
			echo '</tr>';
			if ($data['commentaire']) {	echo '<tr><td colspan=10 style="text-align:justify;" class ='.$class.'>'.$data['commentaire'].'</td></tr>'; }
		}     
		echo '</table>';

		echo '<table width="100%">';
		echo '<tr>';
		echo '<td  style="text-align:right;">';
		echo '<input type="submit" value="'._T('asso:activite_bouton_supprimer').'" class="fondo">';
		echo '</table>';
		echo '</form>';
		
		fin_cadre_relief();  
		echo fin_gauche(), fin_page();
	}
?>
