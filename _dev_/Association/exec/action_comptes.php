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
function exec_action_comptes(){
global $connect_statut, $connect_toutes_rubriques;

debut_page(_T('Gestion pour  Association'), "", "");

$url_action_comptes=generer_url_ecrire('action_comptes');

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('OP&eacute;rations comptables'));
	debut_boite_info();

print('<p>Nous sommes le '.date('d-m-Y').'</p>');

$id_compte=$_POST['id_compte'];
$date=$_POST['date'];
$imputation=$_POST['imputation'];
$recette=$_POST['recette'];
$depense=$_POST['depense'];
$justification=$_POST['justification'];
$journal=$_POST['journal'];
$url_retour=$_POST['url_retour'];

$justification =addslashes($justification);

$action=$_POST['action'];

//---------------------------- 
//AJOUT OPERATION
//---------------------------- 
	
if ($action=="ajoute"){

$sql = "INSERT INTO spip_asso_comptes (date, imputation, recette, depense, journal, justification) VALUES ('$date', '$imputation' ,'$recette', '$depense', '$journal', '$justification')";
$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());

echo '<p><strong>L\'op&eacute;ration a &eacute;t&eacute; enregistr&eacute;e pour un montant de ';
if (empty($depense)) {echo $recette;} else {echo $depense;}
echo ' &euro;</strong></p>';
}

//---------------------------- 
//MODIFICATION OPERATION
//---------------------------- 

if ($action =="modifie") { 
$sql = "UPDATE spip_asso_comptes SET date='$date', recette='$recette', depense='$depense', justification='$justification', journal='$journal' WHERE id_compte='$id_compte'";
$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());	

echo '<div align="center">';
echo '<br><strong>L\'op&eacute;ration a &eacute;t&eacute; mise &agrave; jour</strong>';
echo '<p>';
icone(_T('asso:Retour'), $url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/livredor.png','rien.gif' );
echo '</p>';
echo '</div>';
}


//---------------------------- 
//SUPPRESSION PROVISOIRE OPERATION
//---------------------------- 		
if ($action == "supprime") {

$url_retour = $_SERVER['HTTP_REFERER'];

echo '<p><strong>Vous vous appr&ecirc;tez &agrave; effacer une op&eacute;ration  !</strong></p>';
echo '<form action="'.$url_action_comptes.'"  method="post">';
echo '<input type=hidden name="action" value="drop">';
echo '<input type=hidden name="id" value="'.$id_compte.'">';
echo '<input type=hidden name="url_retour" value="'.$url_retour.'">';
echo '<p><input name="submit" type="submit" value="Confirmer" class="fondo"></p>';
echo '<p>';
icone(_T('asso:Retour'), $url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/livredor.png','rien.gif' );
echo '</p>';
}

//---------------------------- 
//  SUPPRESSION DEFINITIVE OPERATION
//---------------------------- 		
if ($action == "drop") {

$url_retour=$_POSTE['url_retour'];

$sql = "DELETE FROM spip_asso_comptes WHERE id_compte='$id'";
$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());  

echo '<p><strong>Suppression effectu&eacute;e !</strong></p>';
echo '<p>';
icone(_T('asso:Retour'), $url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/livredor.png','rien.gif' );
echo '</p>';
}

fin_boite_info();
	  
  fin_cadre_relief();  

fin_page();} 
?>
      