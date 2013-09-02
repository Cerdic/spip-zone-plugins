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

print association_date_du_jour();

$action=$_REQUEST['action'];
$id_categorie=$_REQUEST['id'];

$action=$_POST['action'];
$libelle=$_POST['libelle'];
$valeur=$_POST['valeur'];
$duree=$_POST['duree'];
$montant=$_POST['montant'];
$commentaires=$_POST['commentaires'];

$libelle=addslashes($libelle);
$commentaires=addslashes($commentaires);
$valeur=addslashes($valeur);

//---------------------------- 
//SUPPRESSION PROVISOIRE CATEGORIE
//---------------------------- 		
if ($action == "supprime") {

$url_retour = $_SERVER['HTTP_REFERER'];

echo '<p><strong>Vous vous appr&ecirc;tez &agrave; effacer le cat&eacute;gorie n&deg; '.$id.' !</strong></p>';
echo '<form action="'.$url_action_categories.'"  method="post">';
echo '<input type=hidden name="action" value="drop">';
echo '<input type=hidden name="id" value="'.$id_categorie.'">';
echo '<input type=hidden name="url_retour" value="'.$url_retour.'">';
echo '<p><input name="submit" type="submit" value="Confirmer" class="fondo"></p>';
echo '<p>';
icone(_T('asso:Retour'), $url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/calculatrice.gif','rien.gif' );
echo '</p>';
}

//---------------------------- 
//  SUPPRESSION DEFINITIVE CATEGORIE
//---------------------------- 		
if ($action == "drop") {

spip_query( "DELETE FROM spip_asso_categories WHERE id_categorie='$id_categorie' " );

echo '<p><strong>Suppression effectu&eacute;e !</strong></p>';
echo '<p>';
icone(_T('asso:Retour'), $url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/calculatrice.gif','rien.gif' );
echo '</p>';
}
//---------------------------- 
//  MODIFICATION CATEGORIE
//---------------------------- 	
if ($action =="modifie") { 
spip_query( "UPDATE spip_asso_categories SET libelle='$libelle', valeur='$valeur', duree='$duree', cotisation='$montant', commentaires='$commentaires' WHERE id_categorie='$id_categorie' " );

echo '<p><strong>La cat&eacute;gorie a &eacute;t&eacute; mise &agrave; jour</strong></p>';
}
//---------------------------- 
//  AJOUT CATEGORIE
//---------------------------- 	
if ($action == "ajoute") {
spip_query( "INSERT INTO spip_asso_categories (libelle, valeur, duree, cotisation, commentaires) VALUES ('$libelle', '$valeur', '$duree', '$montant', '$commentaires' )" );

echo '<p><strong>La cat&eacute;gorie a &eacute;t&eacute; ins&eacute;r&eacute;e</strong></p>';

}
$url_retour = generer_url_ecrire('categories');
echo '</strong></p>';
echo '<p>';
icone(_T('asso:Retour'), $url_retour, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/calculatrice.gif','rien.gif' );
echo '</p>';


fin_boite_info();

fin_cadre_relief();  

fin_page();
}
?>
