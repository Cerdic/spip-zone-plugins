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

function exec_action_activites(){
	global $connect_statut, $connect_toutes_rubriques;

	debut_page(_T('asso:titre_gestion_pour_association'), "", "");

	$url_action_activites=generer_url_ecrire('action_activites');

	include_spip ('inc/navigation');

	debut_cadre_relief(  "", false, "", $titre = _T('asso:activite_titre_inscriptions_activites'));
	debut_boite_info();

	print association_date_du_jour();

	$id_activite=$_POST['id_activite'];
	$id_evenement=$_POST['id_evenement'];
	$date=$_POST['date'];
	$nom=addslashes($_POST['nom']);
	$id_membre=$_POST['id_membre'];
	$membres=addslashes($_POST['membres']);
	$non_membres=addslashes($_POST['non_membres']);
	$inscrits=$_POST['inscrits'];
	$email=$_POST['email'];
	$telephone=$_POST['telephone'];
	$adresse=addslashes($_POST['adresse']);
	$montant=$_POST['montant'];
	$date_paiement=$_POST['date_paiement'];
	$journal=$_POST['journal'];
	$statut=$_POST['statut'];
	$commentaire=addslashes($_POST['commentaire']);

	$commentaire=nl2br($commentaire);

	$action=$_POST['action'];
	$url_retour=$_POST['url_retour'];
//----------------------------
//AJOUT INSCRIPTION
//----------------------------

	if ($action=="ajoute"){
		
		spip_query( "INSERT INTO spip_asso_activites (date, id_evenement, nom, id_adherent, membres, non_membres, inscrits, email, telephone, adresse, montant, commentaire) VALUES ('$date', '$id_evenement', '$nom', '$id_membre', '$membres', '$non_membres', '$inscrits', '$email', '$telephone', '$adresse', '$montant', '$commentaire' )" );
		
		echo '<p><strong>'._T('asso:activite_message_ajout_inscription',array('nom' => $nom, 'montant' => $montant)).'</strong></p>';
		echo '<p>';
		icone(_T('asso:bouton_retour'), $url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/actif.png','rien.gif' );
		echo '</p>';
	}

//----------------------------
//MODIFICATION INSCRIPTION
//----------------------------

	if ($action=="modifie") {

		spip_query("UPDATE spip_asso_activites SET date='$date', id_evenement='$id_evenement', nom='$nom', id_adherent='$id_membre', membres='$membres', non_membres='$non_membres', inscrits='$inscrits', email='$email', telephone='$telephone', adresse='$adresse', montant='$montant', date_paiement='$date_paiement', statut='$statut', commentaire='$commentaire' WHERE id_activite='$id_activite' ");
		
		echo '<p><strong>'._T('asso:activite_message_maj_inscription',array('nom' => $nom)).'</strong></p>';
		echo '<p>';
		icone(_T('asso:bouton_retour'), $url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/actif.png','rien.gif' );
		echo '</p>';
	}
	
//----------------------------
//AJOUT PAIEMENT
//----------------------------

	if ($action=="paie") {
		
		spip_query("UPDATE spip_asso_activites SET nom='$nom', id_adherent='$id_membre', membres='$membres', non_membres='$non_membres', inscrits='$inscrits', montant='$montant', date_paiement='$date_paiement', statut='$statut', commentaire='$commentaire' WHERE id_activite='$id_activite' ");
		
		$justification=_T('asso:activite_justification_compte_inscription',array('id_activite' => $id_activite, 'nom' => $nom));
		
		spip_query("INSERT INTO spip_asso_comptes (date, journal,recette,justification,imputation,id_journal) VALUES ('$date_paiement','$journal','$montant','$justification','activite','$id_activite')");
		
		echo '<p><strong>'._T('asso:activite_message_maj_inscription',array('nom' => $nom)).'</strong></p>';
		echo '<p>';
		icone(_T('asso:bouton_retour'), $url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/actif.png','rien.gif' );
		echo '</p>';
	}	

//----------------------------
//SUPPRESSION PROVISOIRE INSCRIPTIONS
//----------------------------
	if (isset($_POST['delete'])) {

		$url_retour = $_SERVER['HTTP_REFERER'];

		$delete_tab=(isset($_POST["delete"])) ? $_POST["delete"]:array();
		$count=count ($delete_tab);

		echo '<p><strong>'._T('asso:activite_message_confirmation_supprimer',array('nombre' => $count, 'pluriel' => $count > 1 ? 's' : '')).'</strong></<p>';
		echo '<table>';
		echo '<form action="'.$url_action_activites.'"  method="post">';
		for ( $i=0 ; $i < $count ; $i++ ) {
			$id = $delete_tab[$i];
			echo '<input type="hidden" name="drop[]" value="'.$id.'" checked>';
		}
		echo '<tr>';
		echo '<td><input name="url_retour" type="hidden" value="'.$url_retour.'">';
		echo '<input name="submit" type="submit" value="'._T('asso:activite_bouton_confirmer').'" class="fondo"></td></tr>';
		echo '</form>';
		echo '</table>';
		echo '</div>';
	}

//----------------------------
//  SUPPRESSION DEFINITIVE INSCRIPTIONS
//----------------------------
	if (isset($_POST['drop'])) {

		$drop_tab=(isset($_POST["drop"])) ? $_POST["drop"]:array();
		$count=count ($drop_tab);

		for ( $i=0 ; $i < $count ; $i++ ) {
			$id = $drop_tab[$i];
			spip_query("DELETE FROM spip_asso_activites WHERE id_activite='$id'");
			spip_query("DELETE FROM spip_asso_comptes WHERE id_journal='$id' AND imputation='activite' ");
		}

		echo '<p><strong>'._T('asso:activite_message_suppression').'</strong></p>';
		echo '<p>';
		icone(_T('asso:bouton_retour'), $url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/actif.png','rien.gif' );
		echo '</p>';
	}

	fin_boite_info();

	fin_cadre_relief();

	fin_page();
}
?>
