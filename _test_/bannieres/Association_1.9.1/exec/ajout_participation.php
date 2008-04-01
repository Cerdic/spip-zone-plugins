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

function exec_ajout_participation() {
	global $connect_statut, $connect_toutes_rubriques;

	debut_page(_T('asso:titre_gestion_pour_association'), "", "");

	$url_action_activites=generer_url_ecrire('action_activites');
	$url_retour = $_SERVER["HTTP_REFERER"];

	include_spip ('inc/navigation');

	debut_cadre_relief(  "", false, "", $titre = _T('asso:activite_titre_ajouter_inscriptions'));
	debut_boite_info();

	print association_date_du_jour();

	$id_activite=preg_replace("/[^0-9]/","",$_GET['id']);

	$query = spip_query ("SELECT * FROM spip_asso_activites WHERE id_activite=$id_activite ");

	echo '<form action="'.$url_action_activites.'" method="POST">';
	echo '<fieldset><legend>'._T('asso:activite_ajouter_participation').'</legend>';
	echo '<table width="70%" class="noclass">';
	while ($data = spip_fetch_array($query)) {
		echo '<tr> ';
		echo '<td>'._T('asso:activite_libelle_inscription').' :</td>';
		echo '<td><input name="id_activite" type="text"  size="3" readonly="true" value="'.$data['id_activite'].'"></td>';
		echo '</tr>';
		echo '<tr> ';
		echo '<td>'._T('asso:activite_libelle_nomcomplet').' :</td>';
		echo '<td><input name="nom"  type="text" size="40" value="'.$data['nom'].'"> </td>';
		echo '</tr>';
		echo '<tr> ';
		echo '<td>'._T('asso:activite_libelle_adherent').' :</td>';
		echo '<td><input name="id_membre" type="text" value="'.$data['id_adherent'].'"> </td>';
		echo '</tr>';
		echo '<tr> ';
		echo '<td>'._T('asso:activite_libelle_membres').' :</td>';
		echo '<td><input name="membres"  type="text" size="40" value="'.$data['membres'].'"> </td>';
		echo '</tr>';
		echo '<tr> ';
		echo '<td>'._T('asso:activite_libelle_non_membres').' :</td>';
		echo '<td><input name="non_membres"  type="text" size="40" value="'.$data['non_membres'].'"> </td>';
		echo '</tr>';
		echo '<tr> ';
		echo '<td>'._T('asso:activite_libelle_nombre_inscrit').' :</td>';
		echo '<td><input name="inscrits"  type="text" value="'.$data['inscrits'].'"> </td>';
		echo '</tr>';
		echo '<tr>'; 
		echo '<td>&nbsp;</td>';
		echo '<td>&nbsp;</td>';
		echo '</tr>';
		echo '<tr> ';
		echo '<td>'._T('asso:activite_libelle_montant_inscription').' :</td>';
		echo '<td><input name="montant"  type="text" value="'.$data['montant'].'"> </td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td>'._T('asso:activite_libelle_date_paiement').' :</td>';
		echo '<td><input name="date_paiement" value="'.date('Y-m-d').'" type="text"> </td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td>'._T('asso:activite_libelle_mode_paiement').' :</td>';
		echo '<td><select name="journal" type="text">';
		$sql = spip_query ("SELECT * FROM spip_asso_banques ORDER BY id_banque") ;
		while ($banque = spip_fetch_array($sql)) {
			echo '<option value="'.$banque['code'].'"> '.$banque['intitule'].' </option>';
		}
		echo '</select></td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td>'._T('asso:activite_libelle_statut').' :</td>';
		echo '<td><input name="statut"  type="checkbox" value="ok" unchecked>ok</td>';
		echo '</tr>';
		echo '<tr>'; 
		echo '<td>'._T('asso:activite_libelle_commentaires').' :</td>';
		echo '<td><textarea name="commentaire" cols="30" rows="3">'.$data['commentaire'].'</textarea></td>';
		echo '</tr>';
	}
	echo '<tr>'; 
	echo '<td>&nbsp;</td>';
	echo '<td>&nbsp;</td>';
	echo '</tr>';
	echo '<tr>'; 
	echo '<td>&nbsp;</td>';
	echo '<td>';
	echo '<input name="action" type="hidden" value="paie">';
	echo '<input name="id_evenement" type="hidden" value="'.$id_evenement.'">';
	echo '<input name="url_retour" type="hidden" value="'.$url_retour.'">';
	echo '<input name="" type="submit" value="'._T('asso:activite_bouton_ajouter').'" class="fondo">';
	echo '</td>';
	echo '</tr>';

	echo '</table>';
	echo '</fieldset></form>';


	fin_boite_info();
	fin_cadre_relief();  
	fin_page();
}

?>

