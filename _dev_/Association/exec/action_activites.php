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

debut_page(_T('Gestion pour  Association'), "", "");

$url_action_activites=generer_url_ecrire('action_activites');
$url_retour=$_POST['url_retour'];

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Action sur les inscriptions'));
	debut_boite_info();

print('<p>Nous sommes le '.date('d/m/Y').'</p>');

$id_activite=$_POST['id_activite'];
$id_evenement=$_POST['id_evenement'];
$date=$_POST['date'];
$nom=addslashes($_POST['nom']);
$id_adherent=$_POST['id_adherent'];
$accompagne=addslashes($_POST['accompagne']);
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

//---------------------------- 
//AJOUT INSCRIPTION
//---------------------------- 
	
if ($action=="ajoute"){

spip_query( "INSERT INTO spip_asso_activites (date, id_evenement, nom, id_adherent, accompagne, inscrits, email, telephone, adresse, montant, date_paiement, statut, commentaire) VALUES ('$date', '$id_evenement', '$nom', '$id_adherent', '$accompagne', '$inscrits', '$email', '$telephone', '$adresse', '$montant', '$date_paiement', '$statut', '$commentaire' )" );

$query=spip_query( "SELECT MAX(id_activite) AS id_activite FROM spip_asso_activites" );
while ($data = mysql_fetch_assoc($query))
{
$id_activite=$data['id_activite'];
$justification='Inscription_activit&eacute; n&deg; '.$id_activite.' - '.$nom;
}

spip_query("INSERT INTO spip_asso_comptes (date, journal,recette,justification,imputation,id_journal) VALUES ('$date_paiement','$journal','$montant','$justification','activite','$id_activite')");

echo '<p><strong>L\'inscription de '.$nom.' a &eacute;t&eacute; enregistr&eacute;e pour un montant de '.$montant.' &euro;</strong></p>';
echo '<p>';
icone(_T('asso:Retour'), $url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/actif.png','rien.gif' );
echo '</p>';
}

//---------------------------- 
//MODIFICATION INSCRIPTION
//---------------------------- 	

if ($action=="modifie"){

spip_query("UPDATE spip_asso_activites SET date='$date', id_evenement='$id_evenement', nom='$nom', id_adherent='$id_adherent', accompagne='$accompagne', inscrits='$inscrits', email='$email', telephone='$telephone', adresse='$adresse', montant='$montant', date_paiement='$date_paiement', statut='$statut', commentaire='$commentaire' WHERE id_activite='$id_activite' ");

spip_query("UPDATE spip_asso_comptes SET date='$date_paiement', journal='$journal', recette='$montant' WHERE id_journal=$id_activite AND imputation='activite' ");
	
echo '<p><strong>L\'inscription de '.$nom.' a &eacute;t&eacute; mise &agrave; jour</strong></p>';
echo '<p>';
icone(_T('asso:Retour'), $url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/actif.png','rien.gif' );
echo '</p>';
}
//---------------------------- 
//SUPPRESSION PROVISOIRE INSCRIPTIONS
//---------------------------- 		
if (isset($_POST['delete'])) {

$url_retour = $_SERVER['HTTP_REFERER'];

$delete_tab=(isset($_POST["delete"])) ? $_POST["delete"]:array();
$count=count ($delete_tab);

echo '<p><strong>Vous vous appr&ecirc;tez &agrave; effacer '.$count;
if ($count==1)
{echo ' inscription !';} else {echo ' inscriptions !';}
echo '</strong></p>';
echo '<table>';
echo '<form action="'.$url_action_activites.'"  method="post">';
for ( $i=0 ; $i < $count ; $i++ )
{	$id = $delete_tab[$i];
echo '<input type=hidden name="drop[]" value="'.$id.'" checked>';
	}	
echo '<tr>';
echo '<td><input name="url_retour" type="hidden" value="'.$url_retour.'">';
echo '<input name="submit" type="submit" value="Confirmer" class="fondo"></td></tr>';	
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

for ( $i=0 ; $i < $count ; $i++ )
{	$id = $drop_tab[$i];
	mysql_query("DELETE FROM spip_asso_activites WHERE id_activite='$id'");
	mysql_query("DELETE FROM spip_asso_comptes WHERE id_journal='$id' AND imputation='activite' ");
}

echo '<p><strong>Suppression effectu&eacute;e !</strong></p>';	
echo '<p>';
icone(_T('asso:Retour'), $url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/actif.png','rien.gif' );
echo '</p>';
}

fin_boite_info();
	  
  fin_cadre_relief();  

fin_page();} 
?>
      
