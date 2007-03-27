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

print('Nous sommes le '.date('d/m/Y').'</br>');

$action=$_GET['action'];
$id=$_GET['id'];

if ($action == "supprime") {
spip_query( "DELETE FROM spip_asso_categories WHERE id_categorie='$id' " );

echo '<p><strong>La cat&eacute;gorie a &eacute;t&eacute; supprim&eacute;e</strong></p>';
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
spip_query( "UPDATE spip_asso_categories SET libelle='$libelle', valeur='$valeur', duree='$duree', cotisation='$montant', commentaires='$commentaires' WHERE id_categorie='$id' " );

echo '<p><strong>La cat&eacute;gorie a &eacute;t&eacute; mise &agrave; jour</strong></p>';
}

if ($action == "ajoute") {
spip_query( "INSERT INTO spip_asso_categories (libelle, valeur, duree, cotisation, commentaires) VALUES ('$libelle', '$valeur', '$duree', '$montant', '$commentaires' )" );

echo '<p><strong>La cat&eacute;gorie a &eacute;t&eacute; ins&eacute;r&eacute;e</strong></p>';

}


fin_boite_info();

fin_cadre_relief();  

fin_page();
}
?>
