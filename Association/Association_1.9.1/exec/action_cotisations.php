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

function exec_action_cotisations(){
global $connect_statut, $connect_toutes_rubriques;

debut_page(_T('Gestion pour  Association'), "", "");

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Action sur les cotisations'));
	debut_boite_info();

print association_date_du_jour();

$action=$_POST['action'];
$id_adherent= $_POST['id_adherent'];
$nom= $_POST['nom'];
$prenom= $_POST['prenom'];
$date= $_POST['date'];
$journal= $_POST['journal'];
$montant= $_POST['montant'];
$justification =$_POST['justification'];
$validite =$_POST['validite'];
$url_retour=$_POST['url_retour'];

$justification =addslashes($justification);

if($action=="ajoute") {

spip_query( "INSERT INTO spip_asso_comptes (date, journal,recette,justification,imputation,id_journal) VALUES ('$date','$journal','$montant','$justification','cotisation','$id_adherent')" );

spip_query( "UPDATE spip_asso_adherents SET statut='ok', validite='$validite' WHERE id_adherent='$id_adherent' " );

}

echo '<p><strong>La cotisation de '.$prenom.' '.$nom.' a bien &eacute;t&eacute; enregistr&eacute;e</strong></p>';
echo '<p>';
icone(_T('asso:Retour'), $url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/actif.png','rien.gif' );
echo '</p>';

fin_boite_info();
	  
  fin_cadre_relief();  

fin_page();
} 
?>
      
