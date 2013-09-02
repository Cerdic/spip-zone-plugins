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

function exec_voir_activites(){
	global $connect_statut, $connect_toutes_rubriques;

	debut_page(_T('asso:titre_gestion_pour_association'), "", "");

	$url_asso = generer_url_ecrire('association');
	$url_activites = generer_url_ecrire('activites');
	$url_edit_activite=generer_url_ecrire('edit_activite');
	$url_ajout_activite=generer_url_ecrire('ajout_activite');
	$url_pdf_activite=generer_url_ecrire('pdf_activite');
	$url_ajout_participation=generer_url_ecrire('ajout_participation');
	$url_action_activites = generer_url_ecrire('action_activites');
	include_spip ('inc/navigation');

	debut_cadre_relief(  "", false, "", $titre = _T('asso:activite_titre_inscriptions_activites'));
	debut_boite_info();

	print association_date_du_jour();

	$id_evenement=$_REQUEST['id'];
if ( isset ($_POST['statut'] )) 
	{ $statut =  $_POST['statut']; }
	else { $statut= "%"; }

// PAGINATION ET FILTRES
	echo '<table width="70%">';
	echo '<tr>';
	$query = spip_query (" SELECT * FROM spip_evenements WHERE id_evenement='$id_evenement' ") ;
	while ($data = spip_fetch_array($query)) {
		$date = substr($data['date_debut'],0,10);
		$date = association_datefr($date);
		$titre = $data['titre'];
	}
	echo '<td><strong>'.$date.': '.$titre.'</strong></td>';
	echo '<td>';
	echo '<a href="'.$url_pdf_activite.'&id='.$id_evenement.'">Imprimer</a>';
	echo '</td>';
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
	echo '<table width="70%" border="0">';
	echo '<tr bgcolor="#D9D7AA">';
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

		$total_inscrits += $data['inscrits'];
		$total_montants += $data['montant'];

 }     
	echo '</table>';

	echo '<table width="70%">';
	echo '<tr>';
	echo '<td  style="text-align:right;">';
	echo '<input type="submit" name="Submit" value="'._T('asso:activite_bouton_supprimer').'" class="fondo">';
	echo '</table>';
	echo '</form>';
	echo '<p>'._T('asso:activite_liste_legende').'</p>'; 	

	echo '<p>';
	icone(_T('asso:activite_bouton_ajouter_inscription'), $url_ajout_activite.'&id='.$id_evenement, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/panier_in.gif','rien.gif' );
	echo '</p>';


//$total =$i; 

// TOTAUX

	echo '<p><font color="blue"><strong>'._T('asso:activite_liste_nombre_inscrits',array('total' => $total_inscrits)).'</strong></font><br />';
	echo '<font color="#9F1C30"><strong>'._T('asso:activite_liste_total_participations',array('total' => number_format($total_montants, 2, ',', ' '))).'</strong></font><br/></p>';

	fin_boite_info();

	fin_cadre_relief();  

	fin_page();
}
?>
