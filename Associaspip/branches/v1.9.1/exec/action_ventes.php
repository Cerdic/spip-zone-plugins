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

function exec_action_ventes(){
global $connect_statut, $connect_toutes_rubriques;

debut_page(_T('Gestion pour  Association'), "", "");

$url_action_ventes=generer_url_ecrire('action_ventes');

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Action sur les ventes associatives'));
	debut_boite_info();

print association_date_du_jour();
	
$id_vente=$_POST['id_vente'];
$date_vente=$_POST['date_vente'];
$article=$_POST['article'];
$code=$_POST['code'];
$acheteur=$_POST['acheteur'];
$quantite=$_POST['quantite'];
$date_envoi=$_POST['date_envoi'];
$frais_envoi=$_POST['frais_envoi'];
$prix_vente=$_POST['prix_vente'];
$journal=$_POST['journal'];
$justification='vente n&deg; '.$id_vente.' - '.$article;
$commentaire=$_POST['commentaire'];
$recette=$quantite*$prix_vente;
$article= addslashes($article);
$commentaire =addslashes($commentaire);
$commentaire=nl2br($rcommentaire); 

$action=$_POST['action'];

//---------------------------- 
//AJOUT VENTE
//---------------------------- 
	
if ($action=="ajoute"){

spip_query( "INSERT INTO spip_asso_ventes (date_vente, article, code, acheteur, quantite, date_envoi, frais_envoi, don, prix_vente, commentaire) VALUES ('$date_vente', '$article', '$code', '$acheteur', '$quantite', '$date_envoi', '$frais_envoi', '$don', '$prix_vente', '$commentaire' )");

$query=spip_query( "SELECT MAX(id_vente) AS id_vente FROM spip_asso_ventes");
while ($data = spip_fetch_array($query))
{
$id_vente=$data['id_vente'];
$justification='vente n&deg; '.$id_vente.' - '.$article;
}

spip_query( "INSERT INTO spip_asso_comptes (date, journal,recette,depense,justification,imputation,id_journal) VALUES ('$date_vente','$journal','$recette','$frais_envoi','$justification','vente','$id_vente')" );

echo '<p><strong>La vente a &eacute;t&eacute; enregistr&eacute;e pour un montant de '.$recette.' &euro;, hors frais d\'envoi</strong></p>';
}

//---------------------------- 
//MODIFICATION VENTE
//---------------------------- 	

if ($action=="modifie"){

spip_query( "UPDATE spip_asso_ventes SET date_vente='$date_vente', article='$article', code='$code', acheteur='$acheteur', quantite='$quantite', date_envoi='$date_envoi', frais_envoi='$frais_envoi', don='$don', prix_vente='$prix_vente', commentaire='$commentaire' WHERE id_vente='$id_vente' " );

spip_query( "UPDATE spip_asso_comptes SET date='$date_vente', journal='$journal',recette='$recette', depense='$frais_envoi', justification='$justification' WHERE id_journal=$id_vente AND imputation='vente' " );
	
echo '<p><strong>Les informations ont &eacute;t&eacute; mises &agrave; jour</strong></p>';
}
//---------------------------- 
//SUPPRESSION PROVISOIRE ADHERENT
//---------------------------- 		
if (isset($_POST['delete'])) {

$delete_tab=(isset($_POST["delete"])) ? $_POST["delete"]:array();
$count=count ($delete_tab);

echo '<p><strong>Vous vous appr&ecirc;tez &agrave; effacer '.$count;
if ($count==1)
{echo ' vente !';} else {echo ' ventes !';}
echo '</strong></p>';
echo '<table>';
echo '<form action="'.$url_action_ventes.'"  method="post">';
for ( $i=0 ; $i < $count ; $i++ )
{	$id = $delete_tab[$i];
echo '<input type=hidden name="drop[]" value="'.$id.'" checked>';
	}	
echo '<tr>';
echo '<td><input name="submit" type="submit" value="Confirmer" class="fondo"></td></tr>';	
echo '</form>';
echo '</table>';
echo '</div>';
}

//---------------------------- 
//  SUPPRESSION DEFINITIVE ADHERENTS
//---------------------------- 		
if (isset($_POST['drop'])) {

$drop_tab=(isset($_POST["drop"])) ? $_POST["drop"]:array();
$count=count ($drop_tab);

for ( $i=0 ; $i < $count ; $i++ )
{	$id = $drop_tab[$i];
	spip_query("DELETE FROM spip_asso_ventes WHERE id_vente='$id' " );
	spip_query("DELETE FROM spip_asso_comptes WHERE id_journal='$id' AND imputation='vente' ");
}
echo '<p><strong>Suppression effectu&eacute;e !</strong></p>';	
}

fin_boite_info();
	  
  fin_cadre_relief();  

fin_page();} 
?>
      
