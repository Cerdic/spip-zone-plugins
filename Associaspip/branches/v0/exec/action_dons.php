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

function exec_action_dons(){
global $connect_statut, $connect_toutes_rubriques;

$url_asso = generer_url_ecrire('association');
$url_action_dons = generer_url_ecrire('action_dons');

debut_page(_T('Gestion pour  Association'), "", "");

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Action sur les dons'));
	debut_boite_info();

print association_date_du_jour();

$id_don = $_POST['id_don'];
$date_don = $_POST['date_don'];
$bienfaiteur= $_POST['bienfaiteur'];
$argent= $_POST['argent'];
$journal= $_POST['journal'];
$colis= $_POST['colis'];
$valeur= $_POST['valeur'];
$contrepartie= $_POST['contrepartie'];
$justification='don n&deg; '.$id_don.' - '.$bienfaiteur;
$commentaire=$_POST['commentaire'];
$url_retour=$_POST['url_retour'];

$commentaire= addslashes($commentaire);

$action=$_POST['action'];

//---------------------------- 
//AJOUT DON
//---------------------------- 

if ($action=="ajoute"){
spip_query( "INSERT INTO spip_asso_dons (date_don, bienfaiteur, argent, colis, valeur, contrepartie, commentaire ) VALUES ( '$date_don', '$bienfaiteur', '$argent', '$colis', '$valeur', '$contrepartie', '$commentaire' )");

$query=spip_query( "SELECT MAX(id_don) AS id_don FROM spip_asso_dons");
while ($data = spip_fetch_array($query))
{
$id_don=$data['id_don'];
$justification='don n&deg; '.$id_don.' - '.$bienfaiteur;
}

spip_query( "INSERT INTO spip_asso_comptes (date, journal,recette,justification,imputation,id_journal) VALUES ('$date_don', '$journal', '$argent', '$justification', 'don', '$id_don')" );

echo '<p><strong>Le don a &eacute;t&eacute; enregistr&eacute;</strong></p>';
}

//---------------------------- 
//MODIFICATION DON
//---------------------------- 	

if ($action=="modifie"){

spip_query( "UPDATE spip_asso_dons SET date_don='$date_don', bienfaiteur='$bienfaiteur', argent='$argent', colis='$colis', valeur='$valeur', contrepartie='$contrepartie', commentaire='$commentaire' WHERE id_don='$id_don'");

spip_query( "UPDATE spip_asso_comptes SET date='$date_don', journal='$journal',recette='$argent', justification='$justification'  WHERE id_journal=$id_don AND imputation='don' ");
	
echo '<p><strong>Le don a &eacute;t&eacute; mis &agrave; jour</strong></p>';
echo '<p>';
icone(_T('asso:Retour'), $url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/bienfaiteur.png','rien.gif' );
echo '</p>';
}
//---------------------------- 
//SUPPRESSION PROVISOIRE DONS
//---------------------------- 		
if (isset($_POST['delete'])) {

$delete_tab=(isset($_POST["delete"])) ? $_POST["delete"]:array();
$count=count ($delete_tab);

echo '<div align="center">';

echo '<br><strong>Vous vous appr&ecirc;tez &agrave; effacer '.$count;
if ($count=1)
{echo ' don !';} else {echo ' dons !';}
echo '</strong><br>';
echo '<table>';
echo '<form action="'.$url_action_dons.'"  method="post">';
for ( $i=0 ; $i < $count ; $i++ )
{	$id = $delete_tab[$i];
echo '<input type=hidden name="drop[]" value="'.$id.'">';
}
echo '<tr>';
echo '<td colspan="2"><input name="submit" type="submit" value="Confirmer" class="fondo"></td></tr>';	
echo '<table>';
echo '</div>';
}

//---------------------------- 
//  SUPPRESSION DEFINITIVE DONS
//---------------------------- 		
if (isset($_POST['drop'])) {

$drop_tab=(isset($_POST["drop"])) ? $_POST["drop"]:array();
$count=count ($drop_tab);

for ( $i=0 ; $i < $count ; $i++ )
{	$id = $drop_tab[$i];
	spip_query( "DELETE FROM spip_asso_dons WHERE id_don='$id' ");
	spip_query( "DELETE FROM spip_asso_comptes WHERE id_journal='$id' AND imputation='don' ");  
}
echo '<p><strong>Suppression effectu&eacute;e !</strong></p>';	
}

fin_boite_info();
	  
  fin_cadre_relief();  

fin_page();} 
?>
