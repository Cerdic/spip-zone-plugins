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
$url_asso = generer_url_ecrire('association');
$url_ajouter = generer_url_ecrire('ajouter');
$url_relance = generer_url_ecrire('essai');
$url_bienfaiteur = generer_url_ecrire('bienfaiteur');
$url_vente = generer_url_ecrire('ventes');
$url_banque = generer_url_ecrire('banque');
$url_delete = generer_url_ecrire('delete_membre');
$url_categories=generer_url_ecrire('categories');

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Tous les comptes financiers'));
	debut_boite_info();

print('<p>Nous sommes le '.date('d/m/Y').'</p>');

$action=$_GET['action'];
$id=$_GET['id'];

if ($action == "supprime") {
spip_query( "DELETE FROM spip_asso_banques WHERE id_banque='$id' ");

echo '<p><strong>Le compte financier a &eacute;t&eacute; supprim&eacute;e</strong></p>';
echo '<p>';
icone(_T('asso:Retour'), 'javascript:history.go(-2)', '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/ecole.gif','rien.gif' );
echo '</p>';
}

$action=$_POST['action'];
$id=$_POST['id_banque'];
$code=$_POST['code'];
$intitule=$_POST['intitule'];
$reference=$_POST['reference'];
$solde=$_POST['solde'];
$date=$_POST['date'];
$commentaire=$_POST['commentaires'];
$intitule= addslashes($intitule);
$reference= addslashes($reference);
$commentaire= addslashes($commentaire);
$commentaire=nl2br($commentaire); 

if ($action =="modifie") { 
spip_query( "UPDATE spip_asso_banques SET code='$code', intitule='$intitule', reference='$reference', solde='$solde', date='$date', commentaire='$commentaire' WHERE id_banque='$id' ");

echo '<p><strong>Le compte "'.$code.'" a &eacute;t&eacute; mis &agrave; jour</strong></p>';
echo '<p>';
icone(_T('asso:Retour'), 'javascript:history.go(-2)', '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/ecole.gif','rien.gif' );
echo '</p>';
}

if ($action == "ajoute") {
spip_query( "INSERT INTO spip_asso_banques (code, intitule, reference, solde, date, commentaire) VALUES ('$code', '$intitule', '$reference', '$solde', '$date', '$commentaire' )");
echo '<p><strong>Le nouveau compte financier a &eacute;t&eacute; ajout&eacute;</strong></p>';
echo '<p>';
icone(_T('asso:Retour'), 'javascript:history.go(-2)', '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/ecole.gif','rien.gif' );
echo '</p>';
}


fin_boite_info();

fin_cadre_relief();  

fin_page();
}
?>
