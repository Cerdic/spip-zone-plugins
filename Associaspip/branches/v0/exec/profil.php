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


function exec_profil() {
  	global $connect_statut, $connect_toutes_rubriques;
	if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)){
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}

	debut_page(_T('livre:association'), "naviguer", "association");

	debut_gauche();

	include_spip('inc/raccourcis');

	debut_droite();
	debut_cadre_formulaire();
	gros_titre(_T('Profil de votre association'));

	$action =$_POST['action'];

	if ( $action != "modifie" ){
		
		$query = spip_query("SELECT * FROM spip_asso_profil where id_profil=1");
		
		echo '<br>';
		echo '<form action="" method="POST">';
		echo '<table width="100%" border="0">';
		while ($data = spip_fetch_array($query)){
			echo '<tr>';
			echo '<td>Nom de l\'association</td>';
			echo '<td><input name="nom" type="text" size="40" value="'.$data['nom'].'"></td>';
			echo '</tr>';
			echo ' <tr>';
			echo '<td>Rue</td>';
			echo '<td><textarea name="rue" cols="30">'.$data['rue'].'</textarea></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td>Code Postal</td>';
			echo '<td><input name="cp" type="text" size="40"value="'.$data['cp'].'"></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td>Ville</td>';
			echo '<td><input name="ville" type="text" size="40" value="'.$data['ville'].'"></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td>T&eacute;l&eacute;phone</td>';
			echo '<td><input name="telephone" type="text" size="40" value="'.$data['telephone'].'"></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td>Adresse courriel</td>';
			echo '<td><input name="mail" type="text" size="40" value="'.$data['mail'].'"></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td>N&deg; SIRET</td>';
			echo '<td><input name="siret" type="text" size="40" value="'.$data['siret'].'"></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td>N&deg; d&eacute;claration</td>';
			echo '<td><input name="declaration" type="text" size="40" value="'.$data['declaration'].'"></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td>Pr&eacute;fecture ou Sous-Pr&eacute;fecture</td>';
			echo '<td><input name="prefet" type="text" size="40" value="'.$data['prefet'].'"></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td>Pr&eacute;sident en cours</td>';
			echo '<td><input name="president" type="text" size="40" value="'.$data['president'].'"></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td>Indexation des adh&eacute;rents</td>';
			echo '<td><input name="indexation" type="radio" value="num" ';
			if ($data['indexation']=="num") {echo ' checked="checked"';}
			echo '>Incr&eacute;mentielle';
			echo '<input name="indexation" type="radio" value="ref" ';
			if ($data['indexation']=="ref") {echo ' checked="checked"';}
			echo '>Structur&eacute;e';
			echo '</td></tr>';
			echo '<tr>';
			echo '<td>Gestion des ventes associatives</td>';
			echo '<td><input name="ventes" type="radio" value="oui"';
			if ($data['ventes']=="oui") {echo ' checked="checked"';}
			echo '>oui';
			echo '<input name="ventes" type="radio" value="non"';
			if ($data['ventes']=="non") {echo ' checked="checked"';}
			echo '>non';
			echo '</td></tr>';
			echo '<tr>';
			echo '<td>Gestion des dons et colis</td>';
			echo '<td><input name="dons" type="radio" value="oui"';
			if ($data['dons']=="oui") {echo ' checked="checked"';}
			echo '>oui';
			echo '<input name="dons" type="radio" value="non"';
			if ($data['dons']=="non") {echo ' checked="checked"';}
			echo '>non';
			echo '</td></tr>';
			echo '<td>Gestion comptable</td>';
			echo '<td><input name="comptes" type="radio" value="oui"';
			if ($data['comptes']=="oui") {echo ' checked="checked"';}
			echo '>oui';
			echo '<input name="comptes" type="radio" value="non"';
			if ($data['comptes']=="non") {echo ' checked="checked"';}
			echo '>non';
			echo '</td></tr>';
			echo '<td>Gestion des inscriptions aux activit&eacute;s</td>';
			echo '<td><input name="activites" type="radio" value="oui"';
			if ($data['activites']=="oui") {echo ' checked="checked"';}
			echo '>oui';
			echo '<input name="activites" type="radio" value="non"';
			if ($data['activites']=="non") {echo ' checked="checked"';}
			echo '>non (n&eacute;cessite le plugin agenda)</td>';
			echo '</tr>';
		}
		echo '<tr>';
		echo '<td>&nbsp;</td>';
		echo '<td><input name="action" type="hidden" value="modifie"><input name="submit" type="submit" value="Valider" class="fondo"></td>';
		echo '</table> ';
		echo'</form>';
	}
	else {
		
		$nom=addslashes($_POST['nom']);
		$rue= addslashes($_POST['rue']);
		$cp= $_POST['cp'];
		$ville=addslashes($_POST['ville']);
		$telephone=$_POST['telephone'];
		$mail=$_POST['mail'];
		$siret= $_POST['siret'];
		$declaration= addslashes($_POST['declaration']);
		$prefet= $_POST['prefet'];
		$president= addslashes($_POST['president']);
		$indexation=$_POST['indexation'];
		$dons= $_POST['dons'];
		$ventes= $_POST['ventes'];
		$comptes= $_POST['comptes'];
		$activites= $_POST['activites'];
		$prefet=nl2br($prefet); 
		
		if($nom == ''){echo '';}    
		else {
			spip_query("UPDATE spip_asso_profil SET nom='$nom', rue='$rue', cp='$cp', ville='$ville', telephone= '$telephone', mail='$mail', siret='$siret', declaration='$declaration', prefet='$prefet', president='$president', indexation='$indexation', dons='$dons', ventes='$ventes', comptes='$comptes', activites='$activites' WHERE id_profil=1");
			echo '<p><strong>Le profil de l\'association a &eacute;t&eacute; mis &agrave; jour</strong></p>';
		}	
	}

fin_cadre_formulaire();
		 fin_page(); } ?> 
