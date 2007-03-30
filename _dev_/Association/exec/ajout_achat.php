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
function exec_ajout_achat(){
global $connect_statut, $connect_toutes_rubriques;

debut_page(_T('Gestion pour  Association'), "", "");
echo "<br /><br />";
gros_titre(_T('Mise &agrave; jour'));
echo "<br />";
$url_asso = generer_url_ecrire('association');
$url_ajouter = generer_url_ecrire('ajouter');
$url_relance = generer_url_ecrire('essai');
$url_bienfaiteur = generer_url_ecrire('bienfaiteur');
$url_vente = generer_url_ecrire('ventes');
$url_banque = generer_url_ecrire('banque');
$url_delete = generer_url_ecrire('delete_membre');
$url_action_adherents=generer_url_ecrire('action_adherents');
$url_ajout_achat=generer_url_ecrire('ajout_achat');
debut_cadre_relief(  "", false, "", $titre = _T('Ajouter des acheteurs'));
	debut_boite_info();

print association_date_du_jour();
echo '<p><strong>.:: Ajout d\'Acheteurs ::.</strong></p>';
echo '<p><a href="'.$url_asso.'">Gestion des adh&eacute;rents</a> | <a href="'.$url_ajouter.'">Ajout d\'un membre</a>
 | <a href="'.$url_relance.'">Relance de cotisations</a> | <a href="'.$url_bienfaiteur.'">Gestion des bienfaiteurs</a>
 |  <a href="'.$url_vente.'">Vente de produits</a>
|  <a href="'.$url_banque.'">Livre de comptes</a></p>';




// recuperation des valeurs du formulaire

$nom = $_POST['nom_acheteur'];

$id= $_POST['id'];

$nom_livre= $_POST['nom_livre'];

$date_envoi=$_POST['date_envoi'];

$qa= $_POST['q_achete'];

$da= $_POST['date_achat'];

$fe= $_POST['frais_envoi'];

$pr= $_POST['prix_vente'];

$don= $_POST['livre_don'];

	//$rem_ad = addslashes($rem_ad);

//$date_jour=date('Y-m-d');

//$date_inscrip= date('Y-m-j');

//$divers= "1";

echo $id."<br />";

echo $nom."<br />";

echo $nom_livre."<br />";

echo $qa."<br />";

echo $da."<br />";

echo $don."<br />";

echo $fe."<br />";

echo $pr."<br />";

echo $date_envoi."<br />";

//echo $rem_ad;

//echo "<p align ='center'><font size='4'><strong>Un instant la page va se rafraichir!</strong></font></p>";

$sql="INSERT INTO spip_ventes (nom_livre, nom_acheteur,  q_achete, date_achat, date_envoi, livre_don, prix_vente, frais_envoi) VALUES ('$nom_livre', '$nom',  '$qa' ,'$da', '$date_envoi', '$don', '$pr', '$fe')";



$req = spip_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());



mysql_close();





 

// print "<meta http-equiv='refresh' content=\"0;URL=livres.php3\">"; 
}
 ?>
