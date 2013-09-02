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

function exec_action_banques(){
global $connect_statut, $connect_toutes_rubriques;

debut_page(_T('Gestion des financiers'), "", "");

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Tous les comptes financiers'));
	debut_boite_info();

print association_date_du_jour();

$id_banque=$_REQUEST['id'];
$action=$_REQUEST['action'];

$code=$_POST['code'];
$intitule=$_POST['intitule'];
$reference=$_POST['reference'];
$solde=$_POST['solde'];
$date=$_POST['date'];
$commentaire=$_POST['commentaire'];
$intitule= addslashes($intitule);
$reference= addslashes($reference);
$commentaire= addslashes($commentaire);
$commentaire=nl2br($commentaire); 

$url_retour=$_POST['url_retour'];

//---------------------------- 
//SUPPRESSION PROVISOIRE BANQUE
//---------------------------- 		
if ($action == "supprime") {

$url_retour = $_SERVER['HTTP_REFERER'];

echo '<p><strong>Vous vous appr&ecirc;tez &agrave; effacer le compte financier n&deg; '.$id_banque.' !</strong></p>';
echo '<form action="'.$url_action_banques.'"  method="post">';
echo '<input type=hidden name="action" value="drop">';
echo '<input type=hidden name="id" value="'.$id_banque.'">';
echo '<input type=hidden name="url_retour" value="'.$url_retour.'">';
echo '<p><input name="submit" type="submit" value="Confirmer" class="fondo"></p>';
echo '<p>';
icone(_T('asso:Retour'), $url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/ecole.gif','rien.gif' );
echo '</p>';
}

//---------------------------- 
//  SUPPRESSION DEFINITIVE BANQUE
//---------------------------- 		
if ($action == "drop") {

spip_query( "DELETE FROM spip_asso_banques WHERE id_banque='$id_banque' " );

echo '<p><strong>Suppression effectu&eacute;e !</strong></p>';
echo '<p>';
icone(_T('asso:Retour'), $url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/ecole.gif','rien.gif' );
echo '</p>';
}

if ($action =="modifie") { 
spip_query( "UPDATE spip_asso_banques SET valeur='$code', intitule='$intitule', reference='$reference', solde='$solde', date='$date', commentaire='$commentaire' WHERE id_banque='$id_banque' ");

echo '<p><strong>Le compte financier "'.$code.'" a &eacute;t&eacute; mis &agrave; jour</strong></p>';
echo '<p>';
icone(_T('asso:Retour'), $url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/ecole.gif','rien.gif' );
echo '</p>';
}

if ($action == "ajoute") {
spip_query( "INSERT INTO spip_asso_banques (valeur, intitule, reference, solde, date, commentaire) VALUES ('$code', '$intitule', '$reference', '$solde', '$date', '$commentaire' )");
echo '<p><strong>Le nouveau compte financier a &eacute;t&eacute; ajout&eacute;</strong></p>';
echo '<p>';
icone(_T('asso:Retour'), $url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/ecole.gif','rien.gif' );
echo '</p>';
}


fin_boite_info();

fin_cadre_relief();  

fin_page();
}
?>
