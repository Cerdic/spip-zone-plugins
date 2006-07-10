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


// Ajouter si on le connait le destinataire
// on fait ca comme un sale directos dans le texte du message :))   
$query = "SELECT texte FROM spip_messages WHERE id_message=$id_message";
$result = spip_query($query);

while($row = spip_fetch_array($result)) {	
	if(($choisir_dest OR $envoi_test)){
	if($envoi_test){
	 $destinataire = $adresse_test ;
	 global $table_prefix;
		$query__ = "SELECT id_auteur FROM ".$table_prefix."_auteurs WHERE email = '$destinataire' ORDER BY id_auteur ASC ";
		if(spip_num_rows(spip_query($query__))==0){
		$erreur_mail_pas_bon = "<h3>"._T('spiplistes:sans_envoi')."</h3>\n"; 
		}

	}
	$texte_mod = "__bLg__".$destinataire."__bLg__".$row['texte'] ;
	$texte_mod = addslashes($texte_mod);
	if(!$erreur_mail_pas_bon)
	spip_query("UPDATE spip_messages SET texte='$texte_mod' WHERE id_message='$id_message'");
	}
}

if(intval($id_message)){
	
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
	
}

//le message

$query_m = "SELECT * FROM spip_messages WHERE id_message=$id_message";
$result_m = spip_query($query_m);

while($row = spip_fetch_array($result_m)) {
	$id_message = $row['id_message'];
	
	$date_heure = $row["date_heure"];
	$date_fin = $row["date_fin"];
	$titre = typo($row["titre"]);
	$texte = $row["texte"];
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
		
	//trouver un dest dans le texte
			
	$destinataire = ''; //secu
	eregi("^__bLg__[0-9@\.A-Z_-]+__bLg__", $texte, $res );
	$destinataire = str_replace("__bLg__","",$res[0]);
		
	if($destinataire != ''){
		//est-ce une liste ?
		if(intval($destinataire)){
		$query_ = spip_query ("SELECT * FROM spip_articles WHERE id_article = '$destinataire' ");
		$row = spip_fetch_array($query_);
		$destinataire = 'la liste : "'.$row['titre'].'"';
		//echo $liste_destinataire ;
		}elseif($destinataire == 'tous'){
		//est-ce l'ensemble des abonnés
			$destinataire = _T('spiplistes:abonees');
			}elseif(email_valide_bloog($destinataire)){				
				$destinataire = "l'email de test : ".$destinataire ;
				}else{$erreur_mail == 'oui';}
	}
	

	debut_cadre_relief('../'._DIR_PLUGIN_SPIPLISTES.'/img_pack/stock_mail_send.gif');


	if ($statut == 'redac' && !$erreur_mail_pas_bon) {
		if ($destinataire!='') {
		echo "<font face='Verdana,Arial,Sans,sans-serif' size=2 color='red'>
		<b>"._T('spiplistes:message_presque_envoye')."</b></font> <br />  &agrave; destination de $destinataire<br />"._T('spiplistes:confirme_envoi');
		}else {
		echo "<br /><font face='Verdana,Arial,Sans,sans-serif' size=2 color='red'><b>"._T('spiplistes:message_en_cours')." <br />"._T('spiplistes:modif_envoi')."</b></font>";
		}
    }elseif($erreur_mail_pas_bon){
    echo $erreur_mail_pas_bon ;
    echo "<br /><font face='Verdana,Arial,Sans,sans-serif' size=2 color='red'><b>"._T('spiplistes:message_en_cours')." <br />"._T('spiplistes:modif_envoi')."</b></font>";
    }

    if ($statut == 'encour'){
        if ($expediteur == $connect_id_auteur  OR ($type == 'nl' AND $connect_statut == '0minirezo') OR ($type == 'auto' AND $connect_statut == '0minirezo')) {
			echo "<div style='float:right'>";
			icone (_T('icone_supprimer_message'), '?exec=spip_listes&detruire_message='.$id_message, 'messagerie-24.gif', 'supprimer.gif');
			echo "</div>";
			}
        echo "<p><font face='Verdana,Arial,Sans,sans-serif' size=2 color='red'>
        <b>"._T('spiplistes:envoi_program')."</b></font><br />  &agrave; destination de $destinataire<br /><br />
        <a href='?exec=spip_listes'>["._T('spiplistes:voir_historique')."]</a></p>";
	}

	if ($statut == 'publie')  {
	echo "<font face='Verdana,Arial,Sans,sans-serif' size=2 color='red'>
	<b>"._T('spiplistes:message_arch')."</b></font>";
	echo "<br />envoy&eacute; &agrave $destinataire le $lejour/$lemois/$lannee";
	}

    fin_cadre_relief();
	
	//ne pas afficher le destinataire
	$texte = eregi_replace("__bLg__[0-9@\.A-Z_-]+__bLg__","",$texte);
	$texte = stripslashes($texte);
	$texte_original = $texte;
	
	
	// ne pas faire ca si y'a du htlm (lent, erreur spip class truc), à revoir
	$temp_style = ereg("<style[^>]*>[^<]*</style>", $texte, $style_reg);
  	if (isset($style_reg[0])) $style_str = $style_reg[0]; 
                         else $style_str = "";
  	$texte = ereg_replace("<style[^>]*>[^<]*</style>", "__STYLE__", $texte);
	$texte = propre($texte); // pb: enleve aussi <style>...  
	$texte = propre_bloog($texte);
	$texte = ereg_replace("__STYLE__", $style_str, $texte);
	
	
	echo "<div style='margin-top:20px;border: 1px solid $la_couleur; background-color: $couleur_fond; padding: 5px;'>"; // debut cadre de couleur
	//debut_cadre_relief("messagerie-24.gif");
	echo "<table width=100% cellpadding=0 cellspacing=0 border=0>";
	echo "<tr><td width=100%>";
if ($statut=="redac") {
		echo "<div style='float:right'>";
		icone (_T('icone_modifier_message'),'?exec=courrier_edit&id_message='.$id_message, 'messagerie-24.gif');
		echo "</div>";	
			}

	echo "<font face='Verdana,Arial,Sans,sans-serif' size=2 color='$la_couleur'><b>$le_type</b></font><br />";
	echo "<font face='Verdana,Arial,Sans,sans-serif'><h1>$titre</h1></font>";
	
	echo "<br /><font face='Georgia,Garamond,Times,serif' size=3>";
	debut_boite_info();
	   echo "<h3>"._T('spiplistes:version')." HTML</h3><a href=\"".generer_url_ecrire('courrier_preview','id_message='.$id_message)."\">(Plein &eacute;cran)</a><br />\n";
	  echo "<iframe src=\"?exec=courrier_preview&id_message=$id_message\" width=\"100%\" height=\"500\"></iframe>\n";
	fin_boite_info();    
	echo "<p>";
	  debut_boite_info();
	echo "<h3> "._T('spiplistes:version')." "._T('spiplistes:val_texte')." </h3>";
    echo "<textarea name='texte' rows='20' class='formo' cols='40' wrap=soft>";
	echo version_texte($texte);
	echo "</textarea><p>\n";

	fin_boite_info();
	echo "</font><br />";

    if($statut=="redac"){
    //envoi de test 
	echo "<form action='?exec=gerer_courrier&id_message=".$id_message."' method='post'>";
			debut_boite_info();
			echo "<div style='font-size:12px;font-familly:Verdana,Garamond,Times,serif;color:#000000;'>";
			if($destinataire==""){
			echo "<b>"._T('spiplistes:envoi')."</b><p style='font-familly : Georgia,Garamond,Times,serif'>"._T('spiplistes:envoi_texte')."</p>";
			debut_cadre_enfonce();
			echo "<div style='font-size:12px;font-familly:Verdana,Garamond,Times,serif;color:#000000;'>";
			echo "<div style='float:right'><input type='submit' name='envoi_test' value='"._T('spiplistes:email_tester')."' class='fondo' /></div>";
			echo "<input type='text' name='adresse_test' value='"._T('spiplistes:email_adresse')."' class='fondo'>" ;
			echo "</div>" ;
			fin_cadre_enfonce() ;
			
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
				}else{
				echo "<div style='float:right'><input type='submit' name='envoi' value='"._T('spiplistes:envoyer')."' class='fondo'></div>";
				$envoyer_a= _T('spiplistes:envoyer_a');
			echo "<div style='font-size:14px;font-weight:bold'>".$envoyer_a." ".$destinataire."</div>";

				}
				}
				echo "</div>";
				
				fin_boite_info();
				echo "</form>";
			
			
				
	echo "</td></tr></table>";
	echo "</div>"; // fin du cadre de couleur
	
	
			
		
}//while		

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
