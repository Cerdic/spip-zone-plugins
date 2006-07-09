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
include_spip('inc/barre');

function exec_gerer_courrier()
{

global $connect_statut;
global $connect_toutes_rubriques;
global $connect_id_auteur;
global $type;
global $new;
global $id_message;
global $modifier_message;
global $titre;
global $texte;

global $envoi_test,$change_statut,$supp_dest,$envoi,$adresse_test,$choisir_dest,$destinataire ;
 
$nomsite=lire_meta("nom_site"); 
$urlsite=lire_meta("adresse_site"); 

 
// Admin SPIP-Listes
debut_page("Spip listes", "redacteurs", "spiplistes");

// spip-listes bien installé ?
if (!function_exists(spip_listes_onglets)){
    echo("<h3>erreur: spip-listes est mal installé !</h3>"); 
    echo("<p>Vérifier les étapes d'installation,notamment si vous avez bien renommé <i>mes_options.txt</i> en <i>mes_options.php3</i>.</p>");    
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


// MODE COURRIER: Affichage d'un courrier---------------------------------------



$query_message = "SELECT * FROM spip_messages WHERE id_message=$id_message";
$result_message = spip_query($query_message);
        while($row = spip_fetch_array($result_message)) {
		$type = $row['type'];
		}
	if (!$connect_statut == "0minirezo"){
	echo "<b>"._T('avis_non_acces_message')._T('info_acces_refuse')."</b><p>";
	fin_page();
	exit;
	}



if ($modifier_message == "oui") {	
    $titre = addslashes($titre);
	$texte = addslashes($texte);
	spip_query("UPDATE spip_messages SET titre='$titre', texte='$texte' WHERE id_message='$id_message'");	
}



if ($change_statut) {
spip_query("UPDATE spip_messages SET statut='$change_statut' WHERE id_message='$id_message'");
}

if ($supp_dest) {
	spip_query("DELETE FROM spip_auteurs_messages WHERE id_message='$id_message' AND id_auteur='$supp_dest'");
}

// A sécuriser ?
if ($envoi) {
 spip_query("UPDATE spip_messages SET statut='encour' WHERE id_message='$id_message'");
}

//
//

$query_m = "SELECT * FROM spip_messages WHERE id_message=$id_message";
$result_m = spip_query($query_m);

while($row = spip_fetch_array($result_m)) {
	$id_message = $row['id_message'];
	$date_heure = $row["date_heure"];
	$date_fin = $row["date_fin"];
	$titre = typo($row["titre"]);
	$texte = $row["texte"];
	//$texte = propre($row["texte"]);
	$type = $row["type"];
	$statut = $row["statut"];
	$page = $row["page"];
	$rv = $row["rv"];
	$expediteur = $row['id_auteur'];

	$lejour=journum($row['date_heure']);
	$lemois = mois($row['date_heure']);		
	$lannee = annee($row['date_heure']);		

	
		$le_type = _T('spiplistes:message_type');
		$la_couleur = "red";	
	
    debut_cadre_relief('../'._DIR_PLUGIN_SPIPLISTES.'/img_pack/stock_mail_send.gif');



if ($statut == 'redac') {
		if (!$envoi && $destinataire) 
		echo "<font face='Verdana,Arial,Sans,sans-serif' size=2 color='red'>
		<b>"._T('spiplistes:message_presque_envoye')."<br />"._T('spiplistes:confirme_envoi')."</b></font>";
		elseif (!$envoi) 
		echo "<br /><font face='Verdana,Arial,Sans,sans-serif' size=2 color='red'>
		<b>"._T('spiplistes:message_en_cours')." <br />"._T('spiplistes:modif_envoi')."</b></font>";
    }

    if ($statut == 'encour'){
        echo "<p><font face='Verdana,Arial,Sans,sans-serif' size=2 color='red'>
        <b>"._T('spiplistes:envoi_program')."</b><br />
        <a href='?exec=spip_listes'>["._T('spiplistes:voir_historique')."]</a></font></p>";
			if ($expediteur == $connect_id_auteur  OR ($type == 'nl' AND $connect_statut == '0minirezo') OR ($type == 'auto' AND $connect_statut == '0minirezo')) {
			icone (_T('icone_supprimer_message'), '?exec=gerer_courrier&detruire_message=$id_message', 'messagerie-24.gif', 'supprimer.gif');
			echo "<br />";
			}
	}

	if ($statut == 'publie')  
	echo "<font face='Verdana,Arial,Sans,sans-serif' size=2 color='red'>
	<b>"._T('spiplistes:message_arch')."</b></font>";




    fin_cadre_relief();

	echo "<div style='margin-top:20px;border: 1px solid $la_couleur; background-color: $couleur_fond; padding: 5px;'>"; // debut cadre de couleur
	//debut_cadre_relief("messagerie-24.gif");
	echo "<table width=100% cellpadding=0 cellspacing=0 border=0>";
	echo "<tr><td width=100%>";

	echo "<font face='Verdana,Arial,Sans,sans-serif' size=2 color='$la_couleur'><b>$le_type</b></font><br />";
	echo "<font face='Verdana,Arial,Sans,sans-serif' size=5><b>$titre</b></font>";
	

    if ($statut == 'redac') {
		if ($expediteur == $connect_id_auteur OR ($type == 'nl' AND $connect_statut == '0minirezo')) {
			echo "\n</td> <td align='right'>";
			if (!$envoi) 
		icone (_T('icone_modifier_message'),'?exec=courrier_edit&id_message='.$id_message, 'messagerie-24.gif');
			echo "</td><tr></table>";
		}
	}
	
	

	echo "<p>";

    //////////////////////////////////////////////////////
	// Le message lui-meme
	//
  $texte = eregi_replace("__bLg__[0-9@\.A-Z_-]+__bLg__","",$texte);
	$texte = stripslashes($texte);
	$texte_original = $texte;
	
	$temp_style = ereg("<style[^>]*>[^<]*</style>", $texte, $style_reg);
  if (isset($style_reg[0])) $style_str = $style_reg[0]; 
                         else $style_str = "";
  $texte = ereg_replace("<style[^>]*>[^<]*</style>", "__STYLE__", $texte);

$texte = propre($texte); // pb: enleve aussi <style>...  
$texte = propre_bloog($texte);

  $texte = ereg_replace("__STYLE__", $style_str, $texte);

  echo "<div align='left'>";
	echo "<table width=100% cellpadding=0 cellspacing=0 border=0>";
	echo "<tr><td>";

	echo "<br /><font face='Georgia,Garamond,Times,serif' size=3>";
	debut_boite_info();
  echo "<h2> "._T('spiplistes:version')." HTML </h2>";
  echo "<iframe src=\"?exec=courrier_preview&id_message=$id_message\" width=\"100%\" height=\"500\"></iframe>\n";
	fin_boite_info();    
	echo "<p>";
  debut_boite_info();
	echo "<h2> "._T('spiplistes:version')." "._T('spiplistes:val_texte')." </h2>";
    echo "<textarea name='texte' rows='20' class='formo' cols='40' wrap=soft>";
	echo version_texte($texte);
	echo "</textarea><p>\n";

	fin_boite_info();
	echo "</font>";

	echo "</td></tr></table><p>";	
	
//////////////////////////////////////////////////////
	// Newsletter?
	//

// Ajouter si on le connait le destinataire
// on fait ca comme un sale directos dans le texte du message :))
	
if ($statut == 'redac' AND $type =='nl' ){     
	
	if(!$envoi && ($destinataire && $choisir_dest)){
	$texte_original = "__bLg__".$destinataire."__bLg__".$texte_original ;
	$texte_original = addslashes($texte_original);
	spip_query("UPDATE spip_messages SET texte='$texte_original' WHERE id_message='$id_message'");
	}
	
	// email de test
	elseif($envoi_test){
	//vérifier si l adresse est valide ?
	// si l'abonnéest inscrit ?
     
     if(email_valide_bloog($adresse_test)){
		$query = "SELECT id_auteur FROM ".$table_prefix."_auteurs WHERE email = '$adresse_test' ORDER BY id_auteur ASC ";
		$result_in = spip_query($query);
		$is_inscrit = spip_num_rows($result_in);
			 if($is_inscrit > 0){
			 $texte = "__bLg__".$adresse_test."__bLg__".$texte ;
			 $texte = addslashes($texte);
			 spip_query("UPDATE spip_messages SET texte='$texte' WHERE id_message='$id_message'");
			 } else{$erreur_mail ='oui';}
     
     }  else{$erreur_mail = 'oui';}
 
	}


	if(!$envoi){
	
	//envoi de test ?
	echo "<form action='?exec=gerer_courrier&id_message=".$id_message."' method='post'>";
			debut_boite_info();
			echo "<div style='font-size:12px;font-familly:Verdana,Garamond,Times,serif;color:#000000;'>";
			echo "<b>"._T('spiplistes:envoi')."</b><p style='font-familly : Georgia,Garamond,Times,serif'>"._T('spiplistes:envoi_texte')."</p>";
			debut_cadre_enfonce();
			echo "<div style='font-size:12px;font-familly:Verdana,Garamond,Times,serif;color:#000000;'>";
			echo "<div style='float:right'><input type='submit' name='envoi_test' value='"._T('spiplistes:email_tester')."' class='fondo' /></div>";
			echo "<input type='text' name='adresse_test' value='"._T('spiplistes:email_adresse')."' class='fondo'>" ;
			echo "</div>" ;
			fin_cadre_enfonce() ;
			
			if($envoi_test){
			echo "<h2>"._T('spiplistes:email_test')."</h2>" ;
			}else{
			echo "<h2>"._T('spiplistes:email_test_liste')."</h2>" ;
			}
	
					
					//trouver un dest dans le texte
			
	$query_mess = "SELECT * FROM spip_messages WHERE id_message=$id_message";
	$result_mess = spip_query($query_mess);
	
		while($row4 = spip_fetch_array($result_mess)){
		$texte = $row4['texte'] ;
		$destinataires = ''; //secu
		eregi("^__bLg__[0-9@\.A-Z_-]+__bLg__", $texte, $res );
		$destinataires = str_replace("__bLg__","",$res[0]);
		 //echo"destinataire >> $destinataires ";
		// si pas de dest
		
			if($destinataires == ''){
			
				if($erreur_mail == 'oui'){
				echo "<br />"._T('spiplistes:sans_envoi');
				}else{
				$list = spip_query ("SELECT * FROM spip_articles WHERE statut = 'liste' OR statut = 'inact' ");
				echo "<div style='font-size:14px;font-weight:bold'>"._T('spiplistes:destinataires')."</div>";
				echo "<div style='float:right'><input type='submit' name='choisir_dest' value='"._T('spiplistes:choisir_cette')."' class='fondo'></div>";
				echo "<select name='destinataire' >";
				echo "<option value='tous'>"._T('spiplistes:toutes')."</option>" ;
					while($row = spip_fetch_array($list)) {
					$id_article = $row['id_article'] ;
					$titre = $row['titre'] ;
					echo "<option value='$id_article'>$titre</option>" ;
					}
				echo "</select>";
				echo "</div>";
				}
				
			}else{
		
			if($destinataires == 'tous'){
			$vers = _T('spiplistes:abonees');
			}else{
				if(email_valide_bloog($destinataires)){
				$vers = $destinataires ;
				//echo "<h1>$vers</h1>";
				}else{
				//echo "->$vers";
				$destinataires = intval($destinataires) ;
				$desti = spip_query ("SELECT * FROM spip_articles WHERE id_article = '$destinataires' ");
				$row = spip_fetch_array($desti);
				$vers = $row['titre'];
				}
			}
		
			if($erreur_mail != 'oui'){
			echo "<div style='float:right'><input type='submit' name='envoi' value='"._T('spiplistes:envoyer')."' class='fondo'></div>";
			}
			$envoyer_a= _T('spiplistes:envoyer_a');
			echo "<div style='font-size:14px;font-weight:bold'>".$envoyer_a." -> ".$vers."</div>";
			echo "<p>";
			echo "</div>";
			}
	  
		
		} //while
	
	fin_boite_info();
	echo "</form>";
	
	}// pas en mode envoyer
	
	if ($expediteur == $connect_id_auteur  OR ($type == 'nl' AND $connect_statut == '0minirezo')) {
	echo "<br /><table width='100%'><tr><td>";
	echo "\n<table align='left'><tr><td>";
	icone (_T('icone_supprimer_message'), '?exec=spiplistes&detruire_message='.$id_message, 'messagerie-24.gif');
	echo "</td></tr></table>";
	}

}// statut


	echo "</td></tr></table></div>";
	//fin_cadre_relief();
	echo "</div>"; // fin du cadre de couleur
	


	if ($statut == 'publie' AND  $type == 'nl' ) {
	echo "\n<table align='left'><tr><td>";
	icone (_T('icone_arret_discussion'), '?exec=spiplistes&id_message=$id_message&supp_dest=$connect_id_auteur', 'supprimer.gif');
	echo "</td></tr></table>";
	}


		
	echo "</td></tr></table>";
		
	//////////////////////////////////////////////////////
	// Forums
	//

	echo "<br /><br />";

	


    
	 echo "<br /><br />\n<div align='center'>";
	    icone(_T('icone_poster_message'), generer_url_ecrire("forum_envoi","statut=perso&id_message=$id_message&titre_message=".urlencode($titre)."&url=" . generer_url_retour("gerer_courrier","id_message=$id_message")), "forum-interne-24.gif", "creer.gif");
	    echo  "</div>\n<p align='left'>";

	echo "<p align='left'>";

	$query_forum = "SELECT * FROM spip_forum WHERE statut='perso' AND id_message='$id_message' AND id_parent=0 ORDER BY date_heure DESC LIMIT 0,20";
	$result_forum = spip_query($query_forum);
	afficher_forum($result_forum, "gerer_courrier","id_message=$id_message");

}//while

// MODE COURRIER FIN -----------------------------------------------------------



$spiplistes_version = "SPIP-listes b1.9";
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
