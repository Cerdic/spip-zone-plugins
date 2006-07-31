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


function exec_listes_toutes()
{

global $connect_statut;
global $connect_toutes_rubriques;
global $connect_id_auteur;
global $type;
global $new;

 
 
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





// MODE LISTES: afficher les listes --------------------------------------------


global $table_prefix;
$query_message = "SELECT * FROM ".$table_prefix."_articles AS listes LEFT JOIN ".$table_prefix."_auteurs_articles AS abonnements USING (id_article) WHERE statut='liste'";

$result_pile = spip_query($query_message);
$nb_abonnes = spip_num_rows($result_pile);
		
$query = "SELECT id_auteur, nom, extra FROM spip_auteurs";
$result = spip_query($query);
$nb_inscrits = spip_num_rows($result);

	$cmpt_texte = 0;
	$cmpt_html = 0;
	$cmpt_non = 0;
       
	   while ($row = spip_fetch_array($result)) {
		
                $abo = get_extra($row["id_auteur"],auteur);

                        if ($abo['abo'] == "texte"){
                        $cmpt_texte = $cmpt_texte + 1 ;
                        }

                        if ($abo['abo'] == "html"){
                        $cmpt_html = $cmpt_html + 1 ;
                        }
                        
                        if ($abo['abo'] == "non"){
                        $cmpt_non = $cmpt_non + 1 ;
                        }
                        
                        $total_abo = $cmpt_html + $cmpt_texte ;
	}


debut_cadre_relief('forum-interne-24.gif');

echo _T('spiplistes:nb_abonnes')."$nb_abonnes<p>";
echo "<p>";
fin_cadre_relief();

echo "<p>";

//
// Afficher tableau de listes
//


//
// lettres d'infos
//

spiplistes_afficher_en_liste(_T('spiplistes:listes_internes'), '../'._DIR_PLUGIN_SPIPLISTES.'/img_pack/stock_mail.gif', 'articles', 'inact', '', 'position') ;



spiplistes_afficher_en_liste(_T('spiplistes:liste_diff_publiques'), '../'._DIR_PLUGIN_SPIPLISTES.'/img_pack/stock_mail.gif', 'articles', 'liste', '', 'position') ;



spiplistes_afficher_en_liste(_T('spiplistes:listes_poubelle'), '../'._DIR_PLUGIN_SPIPLISTES.'/img_pack/stock_mail.gif', 'articles', 'poublist', '', 'position') ;


// MODE EDIT LISTES FIN --------------------------------------------------------

$spiplistes_version = "SPIP-listes 1.9b1";
echo "<p style='font-family: Arial, Verdana,sans-serif;font-size:10px;font-weight:bold'>".$spiplistes_version."<p>" ;

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
