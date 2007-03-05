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



function exec_spip_listes()
{

include_spip ('inc/acces');
include_spip ('inc/filtres');
include_spip ('inc/config');
include_spip ('inc/barre');

include_spip ('inc/logos');
include_spip ('inc/mots');
include_spip ('inc/documents');

global $connect_statut;
global $connect_toutes_rubriques;
global $connect_id_auteur;
global $supp_dest,$detruire_message;
 
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


// MODE HISTORIQUE: Historique des envois --------------------------------------



if ($supp_dest) {
	spip_query("DELETE FROM spip_auteurs_messages WHERE id_message=$id_message AND id_auteur=$supp_dest");
}

if ($detruire_message) {
	spip_query("DELETE FROM spip_messages WHERE id_message=$detruire_message");
	spip_query("DELETE FROM spip_auteurs_messages WHERE id_message=$detruire_message");
	spip_query("DELETE FROM spip_forum WHERE id_message=$detruire_message");
}



/// afficher un tableau de messages



///

$messages_vus = '';

spiplistes_afficher_en_liste(_T('spiplistes:aff_encours'),  '../'._DIR_PLUGIN_SPIPLISTES.'/img_pack/24_send-receive.gif', 'messages', 'encour', '', 'position') ;



spiplistes_afficher_en_liste(_T('spiplistes:aff_redac'), '../'._DIR_PLUGIN_SPIPLISTES.'/img_pack/stock_mail.gif', 'messages', 'redac', '', 'position') ;



// afficher les messages auto
$list = spip_query ("SELECT * FROM spip_articles WHERE statut = 'inact' OR statut = 'liste' ");
$message_pile = spip_num_rows($list);
if ($message_pile > 0){

$flag_auto = false ;
while($row = spip_fetch_array($list)) {
$extraa = unserialize($row['extra']);
if($extraa['auto'] == 'oui') $flag_auto = true ;
}

if($flag_auto){
debut_cadre_enfonce('../'._DIR_PLUGIN_SPIPLISTES.'/img_pack/stock_timer.gif');
echo "<div  class='chapo' style='border-top:1px #cccccc;width:100%;font-weight:bold;font-size:14px'>"._T('spiplistes:Messages_automatiques')."</div>";
		echo "<style>
		table.tab td {
		text-align:center;
		padding:3px;
		width:33%;
		background-color:#cccccc;
		}
		table.tab {
		margin-top:5px;
		}
		tr.row_even {
		background-color:#cccccc;
		}
		</style>";
		echo "<table class='tab'>" ;	
			echo "<tr style='padding:5px'>";
			echo "<td style='font-weight:bold;background-color:#eeeecc'>envoi du patron</td>";
			echo "<td style='font-weight:bold;background-color:#eeeecc'>sur la liste</td>";
			echo "<td style='font-weight:bold;background-color:#eeeecc'>"._T('spiplistes:prochain_envoi_prevu')."</td>";
			echo "</tr>";
			
	
			
			$i = 0 ;
			$list = spip_query ("SELECT * FROM spip_articles WHERE statut = 'inact' OR statut = 'liste' ");
            $message_pile = spip_num_rows($list);
            while($row = spip_fetch_array($list)) {
            $id_article = $row['id_article'] ;
			$titre = $row['titre'] ;
                         
						// On récupere les extras
                        $extra = get_extra($id_article, 'article');
                        // Tient il n'y avait pas d'extra pour cette liste
                        if (!is_array($extra)) {
                        	$extra = array();
                        }

                        $sablier = time() - $extra['majnouv'] ;
                        $proch = round( ( (24*3600*$extra['periode']) - $sablier) / (3600*24) ) ;


			if($extra['auto'] == "oui"){
				if($i == 0){
				echo "<tr style='padding:5px'>" ;
				$i = 1 ;
				}else {
				echo "<tr style='padding:5px' class='row_even'>" ;
				$i = 0 ;
				}
	
			
              	$date_dernier = $extra['majnouv'] ;
                $date_dernier = date(_T('spiplistes:format_date'),$date_dernier) ;

			echo "<td><a href='".generer_url_public('patron_switch',"patron=".$extra['squelette']."&date=".$date_dernier)."'> ".$extra['squelette']."</a><br />"._T('spiplistes:Tous_les')." ".$extra['periode']." "._T('spiplistes:jours')."</td><td><a href='?exec=gerer_liste&id_article=$id_article'>$titre</a><br />" ;
			echo "</td>" ;
					echo "<td>";
			if($proch != 0) echo "dans <b>$proch</b> "._T('spiplistes:jours')."</td>";
                        else echo "<b>aujourd'hui</b></td>";

                        }
echo "</tr>" ;
			}
			
			
	echo "</table>" ;


fin_cadre_enfonce();
}// flag_auto
}// message pile


echo "<br /><br />";


spiplistes_afficher_en_liste(_T('spiplistes:messages_auto_envoye'),'../'._DIR_PLUGIN_SPIPLISTES.'/img_pack/stock_mail.gif', 'messages', 'auto', '', 'position') ;


spiplistes_afficher_en_liste(_T('spiplistes:aff_envoye'), '../'._DIR_PLUGIN_SPIPLISTES.'/img_pack/stock_mail.gif', 'messages', 'publie', '', 'position') ;




// MODE HISTORIQUE FIN ---------------------------------------------------------

echo "<p style='font-family: Arial, Verdana,sans-serif;font-size:10px;font-weight:bold'>".$GLOBALS['spiplistes_version']."<p>" ;

if (_request('var_mode')=='test'){
	include_spip('inc/spiplistes_cron');
	cron_spiplistes_cron();
}

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
