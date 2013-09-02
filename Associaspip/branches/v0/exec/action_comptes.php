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

debut_cadre_relief(  "", false, "", $titre = _T('Op&eacute;rations comptables'));
	debut_boite_info();

print association_date_du_jour();
	
$id_compte=$_POST['id_compte'];
$date=$_POST['date'];
$imputation=$_POST['imputation'];
$recette=$_POST['recette'];
$depense=$_POST['depense'];
$justification=$_POST['justification'];
$journal=$_POST['journal'];
$justification =addslashes($justification);

$action = $_REQUEST['action'];
$url_retour=$_POST['url_retour'];

//---------------------------- 
//AJOUT OPERATION
//---------------------------- 
	
if ($action=="ajoute"){

spip_query( "INSERT INTO spip_asso_comptes (date, imputation, recette, depense, journal, justification) VALUES ('$date', '$imputation' ,'$recette', '$depense', '$journal', '$justification')");

echo '<p><strong>L\'op&eacute;ration a &eacute;t&eacute; enregistr&eacute;e pour un montant de ';
if (empty($depense)) {echo $recette;} else {echo $depense;}
echo ' &euro;</strong></p>';
}

//---------------------------- 
//MODIFICATION OPERATION
//---------------------------- 

if ($action =="modifie") { 
spip_query( " UPDATE spip_asso_comptes SET date='$date', recette='$recette', depense='$depense', justification='$justification', journal='$journal' WHERE id_compte='$id_compte' " );

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

$id=$_GET['id'];
$url_retour = $_SERVER['HTTP_REFERER'];

echo '<p><strong>Vous vous appr&ecirc;tez &agrave; effacer la ligne de compte n&deg; '.$id.' !</strong></p>';
echo '<form action="'.$url_action_comptes.'"  method="post">';
echo '<input type=hidden name="action" value="drop">';
echo '<input type=hidden name="id" value="'.$id.'">';
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

$id=$_POST['id'];
$url_retour=$_POST['url_retour'];

spip_query( "DELETE FROM spip_asso_comptes WHERE id_compte='$id' " );

echo '<p><strong>Suppression effectu&eacute;e !</strong></p>';
echo '<p>';
icone(_T('asso:Retour'), $url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/livredor.png','rien.gif' );
echo '</p>';
}

//---------------------------- 
//VALIDATION PROVISOIRE COMPTE
//---------------------------- 		
if (isset($_POST['valide'])) {

$url_retour = $_SERVER['HTTP_REFERER'];

$valide_tab=(isset($_POST["valide"])) ? $_POST["valide"]:array();
$count=count ($valide_tab);

echo '<p>Vous vous appr&ecirc;tez &agrave; valider les op&eacute;rations  : <br>';
echo '<table>';
echo '<form action="'.$url_action_comptes.'"  method="post">';
for ( $i=0 ; $i < $count ; $i++ )
{	$id = $valide_tab[$i];
	$query = spip_query("SELECT * FROM spip_asso_comptes where id_compte='$id'");
	while($data = spip_fetch_array($query)) 
	{
echo '<tr>';
echo '<td><strong>'.association_datefr($data['date']).'</strong>';
echo '<td><strong>'.$data['justification'].'</strong>';
echo '<td>';
echo '<input type=checkbox name="definitif[]" value="'.$id.'" checked>';
	}	
}
echo '</table>';
echo '<p>Apr&egrave;s confirmation vous ne pourrez plus modifier ces op&eacute;rations !</p>';
echo '<input name="url_retour" type="hidden" value="'.$url_retour.'">';
echo '<p><input name="submit" type="submit" value="Confirmer" class="fondo"></p>';	

echo '<p>';
icone(_T('asso:Retour'),$url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/livredor.png','rien.gif' );
echo '</p>';
}

//---------------------------- 
//  VALIDATION DEFINITIVE COMPTES
//---------------------------- 		
if (isset($_POST['definitif'])) {

$url_retour=$_POST['url_retour'];

$definitif_tab=(isset($_POST["definitif"])) ? $_POST["definitif"]:array();
$count=count ($definitif_tab);

for ( $i=0 ; $i < $count ; $i++ )
{	$id = $definitif_tab[$i];
	spip_query( "UPDATE spip_asso_comptes SET valide='oui' WHERE id_compte='$id' " );
}
echo '<p><strong>Validation effectu&eacute;e !</strong></p>';	

echo '<p>';
icone(_T('asso:Retour'), $url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/livredor.png','rien.gif' );
echo '</p>';
}

fin_boite_info();
	  
  fin_cadre_relief();  

fin_page();} 
?>
      
