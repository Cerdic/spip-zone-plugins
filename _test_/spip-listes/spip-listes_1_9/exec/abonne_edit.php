<?php

/******************************************************************************************/
/* SPIP-listes est un système de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Générale GNU publiée par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    */
/* en même temps que ce programme ; si ce n'est pas le cas, écrivez à la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.                   */
/******************************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/affichage');


function exec_abonne_edit()
{

global $connect_statut;
global $connect_toutes_rubriques;
global $connect_id_auteur, $id_auteur;
global $champs_extra, $confirm ;
global $suppr_auteur , $id_article, $effacer_definitif,$nom,$email ; 
 
$nomsite=lire_meta("nom_site"); 
$urlsite=lire_meta("adresse_site"); 

 
 
// Admin SPIP-Listes
debut_page("Spip listes", "redacteurs", "spiplistes");

// spip-listes bien installé ?
if (!function_exists(spip_listes_onglets)){
    echo("<h3>erreur: spip-listes est mal installé !</h3>");     
    fin_page();
	  exit;
}

if ($connect_statut != "0minirezo" ) {
	echo "<p><b>"._T('spiplistes:acces_a_la_page')."</b></p>";
	fin_page();
	exit;
}

if (($connect_statut == "0minirezo") OR ($connect_id_auteur == $id_auteur)) {
	$statut_auteur=$statut;
	spip_listes_onglets("messagerie", "Spip listes");
}

debut_gauche();

spip_listes_raccourcis();

creer_colonne_droite();

debut_droite("messagerie");

// MODE ABONNE: gestion d'un abonne---------------------------------------------






if($champs_extra AND ($confirm == 'oui') ){
// prendre en compte les extras
$extras = bloog_extra_recup_saisie('auteurs');
spip_query("UPDATE spip_auteurs SET extra = '$extras' WHERE id_auteur ='$id_auteur'");
}


$query = "SELECT * FROM spip_auteurs WHERE id_auteur='$id_auteur'";
$result = spip_query($query);


if ($row = spip_fetch_array($result)) {
	$id_auteur=$row['id_auteur'];
	$nom=$row['nom'];
	$bio=$row['bio'];
	$email=$row['email'];
	$nom_site_auteur=$row['nom_site'];
	$url_site=$row['url_site'];
	$login=$row['login'];
	$pass=$row['pass'];
	$statut=$row['statut'];
	$pgp=$row["pgp"];
	$messagerie=$row["messagerie"];
	$imessage=$row["imessage"];
	$extra = $row["extra"];
	$low_sec = $row["low_sec"];

if($effacer_definitif){
debut_cadre_relief("redacteurs-poubelle-24.gif");
if($statut=='6forum'){

spip_query("DELETE FROM spip_auteurs_articles WHERE id_auteur='$id_auteur'");
spip_query("DELETE FROM spip_auteurs WHERE id_auteur='$id_auteur'");

echo "$nom ($email) "._T('spiplistes:efface');
echo "<p><a href='?exec=abonnes_tous'>Retour au suivi des abonnements</a><p>";
}	else {echo "Attention, ce contact est auteur sur le site, il ne peut etre effac&eacute;"; }

fin_cadre_relief();
}
        

echo "<div align='center'>";
gros_titre($nom);
echo "</div>";

if ($suppr_auteur AND $id_article) {

        $query = spip_query("SELECT * FROM spip_articles WHERE id_article='$id_article'");
        $row = spip_fetch_array($query);
        $title = $row['titre'] ;
        echo "<h2> ".$nom." "._T('spiplistes:plus_abonne').$title." </h2>";
        $query="DELETE FROM spip_auteurs_articles WHERE id_auteur='$suppr_auteur' AND id_article='$id_article'";
	spip_query($query);
}


if ($statut == "0minirezo") {
$logo = "redacteurs-admin-24.gif";
}else{ 
	if ($statut == "5poubelle") { 
	$logo = "redacteurs-poubelle-24.gif";
	} else {
	$logo = "redacteurs-24.gif";
	}
}

if (strlen($email) > 2 OR strlen($bio) > 0 OR strlen($nom_site_auteur) > 0 OR ($champs_extra AND $extra)) {
	debut_cadre_relief("$logo");
	echo "<font face='Verdana,Arial,Sans,sans-serif'>";
	if (strlen($email) > 2) echo _T('email_2')." <b><a href='mailto:$email'>$email</a></b><br /> ";
	if (strlen($nom_site_auteur) > 2) echo _T('info_site_2')." <b><a href='$url_site'>$nom_site_auteur</a></b>";
	echo "<p>".propre($bio)."</p>";
        echo "</font>";
	fin_cadre_relief();

	if ($champs_extra AND $extra) {

	
        debut_cadre_relief("$logo");

        echo"<form action='?exec=abonne_edit' method='post'>";
         echo"<p align='center'>";

         bloog_extra_saisie($extra, 'auteurs', 'inscription');
         echo"<input type='submit' name='Valider' value='"._T('spiplistes:modifier')."'>";
         echo"<input type='hidden' name='id_auteur'  value=$id_auteur >";
         echo"<input type='hidden' name='confirm'  value='oui' >";
         echo"</p>";
         echo"</form>";
        fin_cadre_relief();
         }

	
}


echo "<p>";
if ($connect_statut == "0minirezo") $aff_art = "'prepa','prop','publie','refuse'";
else if ($connect_id_auteur == $id_auteur) $aff_art = "'prepa','prop','publie'";
else $aff_art = "'prop','publie'";

}

spiplistes_afficher_en_liste(_T('spiplistes:abonne_listes'), '../'._DIR_PLUGIN_SPIPLISTES.'/img_pack/stock_mail.gif', 'abonnements', '', '', 'position') ;





if(!$effacer_definitif=$_POST['effacer_definitif']){
debut_cadre_relief("$logo");
echo "<h3>"._T('spiplistes:supprime_contact')."</h3>";
echo "<form action='?exec=abonne_edit' method='post'>";
echo "<p align='center'>";

echo "<input type='submit' name='Valider' value='"._T('spiplistes:supprime_contact_base')."'>";
echo "<input type='hidden' name='id_auteur'  value=$id_auteur >";
echo "<input type='hidden' name='nom'  value=$nom >";
echo "<input type='hidden' name='email'  value=$email >";
echo "<input type='hidden' name='effacer_definitif'  value='oui' >";
echo "</p>";
echo "</form>";
fin_cadre_relief();
}



//MODE ABONNE FIN abonne -------------------------------------------------------



echo "<p style='font-family: Arial, Verdana,sans-serif;font-size:10px;font-weight:bold'>".$GLOBALS['spiplistes_version']."<p>" ;

fin_page();

}

/******************************************************************************************/
/* SPIP-listes est un système de gestion de listes d'abonnés et d'envoi d'information     */
/* par email  pour SPIP.                                                                  */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Générale GNU publiée par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    */
/* en même temps que ce programme ; si ce n'est pas le cas, écrivez à la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.                   */
/******************************************************************************************/
?>
