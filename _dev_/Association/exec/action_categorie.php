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
function exec_action_categorie(){
global $connect_statut, $connect_toutes_rubriques;

debut_page(_T('Cat&eacute;gories de cotisation'), "", "");
$url_asso = generer_url_ecrire('association');
$url_ajouter = generer_url_ecrire('ajouter');
$url_relance = generer_url_ecrire('essai');
$url_bienfaiteur = generer_url_ecrire('bienfaiteur');
$url_vente = generer_url_ecrire('ventes');
$url_banque = generer_url_ecrire('banque');
$url_delete = generer_url_ecrire('delete_membre');
$url_categories=generer_url_ecrire('categories');

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Toutes les cat&eacute;gories de cotisation'));
	debut_boite_info();

print('Nous sommes le '.date('d-m-Y').'</br>');

$action=$_GET['action'];
$id=$_GET['id'];

if ($action == "supprime") {
$sql = "DELETE FROM spip_asso_categories WHERE id_categorie='$id'";
$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
echo '<div align="center">';
echo '<br><strong>La cat&eacute;gorie a &eacute;t&eacute; supprim&eacute;e</strong>';
echo '</div>';
}

$action=$_POST['action'];
$id=$_POST['id_categorie'];
$libelle=$_POST['libelle'];
$valeur=$_POST['valeur'];
$duree=$_POST['duree'];
$montant=$_POST['montant'];
$commentaires=$_POST['commentaires'];

$libelle=addslashes($libelle);
$commentaires=addslashes($commentaires);

if ($action =="modifie") { 
$sql = "UPDATE spip_asso_categories SET libelle='$libelle', valeur='$valeur', duree='$duree', cotisation='$montant', commentaires='$commentaires' WHERE id_categorie='$id'";
$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());	
echo '<div align="center">';
echo '<br><strong>La cat&eacute;gorie a &eacute;t&eacute; mise &agrave; jour</strong>';
echo '</div>';
}

if ($action == "ajoute") {
$sql = "INSERT INTO spip_asso_categories (libelle, valeur, duree, cotisation, commentaires) VALUES ('$libelle', '$valeur', '$duree', '$montant', '$commentaires' )";
$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());	
echo '<div align="center">';
echo '<br><strong>La cat&eacute;gorie a &eacute;t&eacute; ins&eacute;r&eacute;e</strong>';
echo '</div>';
}


fin_boite_info();

fin_cadre_relief();  

fin_page();
}
?>