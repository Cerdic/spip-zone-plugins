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
include_spip ('base/spip-listes');
include_spip('inc/plugin');



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

global $table_prefix ;
 
// Admin SPIP-Listes
echo debut_page("Spip listes", "redacteurs", "spiplistes");

// spip-listes bien installé ?

///////////////////////
	
	spiplistes_verifier_tables_spip_listes();

//////////////////////////


if ($connect_statut != "0minirezo" ) {
	echo "<p><b>"._T('spiplistes:acces_a_la_page')."</b></p>";
	echo fin_page();
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





if ($detruire_message) {
	spip_query("DELETE FROM spip_courriers WHERE id_courrier=$detruire_message");
	spip_query("DELETE FROM spip_auteurs_messages WHERE id_message=$detruire_message");
	spip_query("DELETE FROM spip_forum WHERE id_message=$detruire_message");
}



/// afficher un tableau de messages



///

$messages_vus = '';

spiplistes_afficher_en_liste(_T('spiplistes:aff_encours'),  '../'._DIR_PLUGIN_SPIPLISTES.'/img_pack/24_send-receive.gif', 'messages', 'encour', '', 'position') ;



spiplistes_afficher_en_liste(_T('spiplistes:aff_redac'), '../'._DIR_PLUGIN_SPIPLISTES.'/img_pack/stock_mail.gif', 'messages', 'redac', '', 'position') ;



// afficher les messages auto
$list = spip_query ("SELECT * FROM ".$table_prefix."_listes WHERE message_auto='oui' ");
$message_pile = spip_num_rows($list);
if ($message_pile > 0){

echo debut_cadre_enfonce('../'._DIR_PLUGIN_SPIPLISTES.'/img_pack/stock_timer.gif');
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
			
			
            while($row = spip_fetch_array($list)) {
            $id_article = $row['id_liste'] ;
			$titre = $row['titre'] ;
                         
					
                        $sablier = time() - strtotime($row['maj']) ;
                        $proch = round( ( (24*3600*$row['periode']) - $sablier) / (3600*24) ) ;


				if($i == 0){
				echo "<tr style='padding:5px'>" ;
				$i = 1 ;
				}else {
				echo "<tr style='padding:5px' class='row_even'>" ;
				$i = 0 ;
				}
	
			
              	$date_dernier = strtotime($row['maj']) ;
                $date_dernier = date(_T('spiplistes:format_date'),$date_dernier) ;

			echo "<td><a href='".generer_url_public('patron_switch',"patron=".$row['patron']."&date=".$date_dernier)."'> ".$row['patron']."</a><br />"._T('spiplistes:Tous_les')." ".$row['periode']." "._T('spiplistes:jours')."</td><td><a href='?exec=gerer_liste&id_liste=$id_article'>$titre</a><br />" ;
			echo "</td>" ;
					echo "<td>";
			if($proch != 0) echo "dans <b>$proch</b> "._T('spiplistes:jours')."</td>";
                        else echo "<b>aujourd'hui</b></td>";

                        
echo "</tr>" ;
			}
			
			
	echo "</table>" ;


echo fin_cadre_enfonce();
}// message pile


echo "<br /><br />";


spiplistes_afficher_en_liste(_T('spiplistes:messages_auto_envoye'),'../'._DIR_PLUGIN_SPIPLISTES.'/img_pack/stock_mail.gif', 'messages', 'auto', '', 'position') ;


spiplistes_afficher_en_liste(_T('spiplistes:aff_envoye'), '../'._DIR_PLUGIN_SPIPLISTES.'/img_pack/stock_mail.gif', 'messages', 'publie', '', 'position') ;




// MODE HISTORIQUE FIN ---------------------------------------------------------

echo "<p style='font-family: Arial, Verdana,sans-serif;font-size:10px;font-weight:bold'>".$GLOBALS['spiplistes_version']."<p>" ;

    echo fin_gauche(), fin_page();
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
