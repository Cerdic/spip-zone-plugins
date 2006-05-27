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


function exec_config()
{

global $connect_statut;
global $connect_toutes_rubriques;
global $connect_id_auteur;
global $type;
global $new;
 
 include_ecrire ("inc_config.php3");
 
$nomsite=lire_meta("nom_site"); 
$urlsite=lire_meta("adresse_site"); 

 
init_config();
if ($changer_config == 'oui') {
	appliquer_modifs_config();
}
 
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

// MODE CONFIG: Configuration de spip-listes -----------------------------------


$accepter_visiteurs = lire_meta('accepter_visiteurs');

if($accepter_visiteurs != 'oui'){
$accepter_visiteurs = 'oui';
ecrire_meta("accepter_visiteurs", $accepter_visiteurs);
ecrire_metas();
echo _T('spiplistes:autorisation_inscription');
}

$extra = get_extra(1,'auteur');

//print_r($extra);

$deb = $extra['debut'] ;
//echo "<h1>$deb</h1>";
if( !is_array($extra) ){
        $extra = array();
        $extra["config"] = "simple";
        set_extra(1,$extra,'auteur');
        $extra = get_extra(1,'auteur');
        }
if( !$extra['config']) {
        $extra["config"] = "simple";
        set_extra(1,$extra,'auteur');
        $extra = get_extra(1,'auteur');
        }


if ($changer_config == 'oui') {
$extra['config'] = $statut_abo ;
set_extra(1,$extra,'auteur');
$extra = get_extra(1,'auteur');
}

$config = $extra['config'];



echo "<form action='?exec=spiplistes&mode=config' method='post'>";
echo "<input type='hidden' name='changer_config' value='oui'>";
 
  debut_cadre_relief("redacteurs-24.gif", false, "", _T('spiplistes:mode_inscription'));

	echo "<table border=0 cellspacing=1 cellpadding=3 width=\"100%\">";

	echo "<tr><td background='img_pack/rien.gif' class='verdana2'>";
	  if($spip_version < 1.8 ){
                echo "<h3>"._T('spiplistes:mode_inscription')."</h3>" ;
          }
	 $texte1 = '' ;
	 $texte2 = '' ;
        ($config == 'simple' ) ? $texte1 = "checked"  : $texte2 = "checked" ;

  echo "<input type='radio' name='statut_abo' value='simple' $texte1 id='statut_simple'>";
	echo "<label for='statut_simple'>"._T('spiplistes:abonnement_simple')."</label> ";
	echo "<p><input type='radio' name='statut_abo' value='membre' $texte2 id='statut_membre'>";
	echo "<label for='statut_membre'>"._T('spiplistes:abonnement_code_acces')."</label></b> ";
	echo "</td></tr>";
	echo "<tr><td style='text-align:$spip_lang_right;'>";
	echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo'>";
	echo "</td></tr>";
	echo "</table>\n";

echo "</form>";
fin_cadre_relief();
	
if ($reinitialiser_config == 'oui' AND $Valider_reinit) {
spip_query("DELETE from spip_messages WHERE statut='encour'");
$extra['debut'] = 0 ;
$extra['locked'] = "non" ;
$extra['total_auteurs'] = 0 ;
set_extra(1,$extra,'auteur');
$extra = get_extra(1,'auteur');
}	
	
	debut_cadre_relief("redacteurs-24.gif", false, "", _T('spiplistes:tableau_bord'));
//print_r($extra);
echo "<form action='?exec=spiplistes&mode=config' method='post'>";
echo "<input type='hidden' name='reinitialiser_config' value='oui'>";	

	echo "<br />"._T('spiplistes:lock').$extra['locked'] ;
	echo "<br />"._T('spiplistes:mail_a_envoyer').$extra['total_auteurs'] ;
	echo "<br />"._T('spiplistes:mail_tache_courante'). $extra['debut'] ;
	
echo "<input type='submit' name='Valider_reinit' value='reinitialiser' class='fondo' style='float:right'>";
echo "<hr style='clear:both;visibility:hidden'>";
echo "</form>";	
fin_cadre_relief();  

// SQUELETTES: visionner les patrons---------------------------------------
//
// Définir les squelettes
//

	debut_cadre_relief();

	echo "<table border=0 cellspacing=1 cellpadding=3 width=\"100%\">";
	echo "<tr><td bgcolor='$couleur_foncee' background='img_pack/rien.gif'>
	<b><font face='Verdana,Arial,Sans,sans-serif' size=3 COLOR='#FFFFFF'>"._T('spiplistes:definir_squel')."</font></b></td></tr>";
	echo "<tr><td bgcolor='#FFFFFF' background='img_pack/rien.gif' >";
	echo " </td></tr>";
	echo "<tr><td bgcolor='#EEEECC' background='img_pack/rien.gif' >";


        //un patron ?



	echo "<b><font face='Verdana,Arial,Sans,sans-serif' size=3>";
	echo _T('spiplistes:patron_disponibles')."</font></b>";

    echo "</td></tr>";

    echo "<tr><td bgcolor='#FFFFFF' background='img_pack/rien.gif' >";


        	echo "<form action='spip_listes.php3' METHOD='get'>"; 
          echo "<input type='hidden' name='mode' value='config' />\n";
    echo "<div>";
	  echo "<div style='float:right;width:200px'>";


    $auj = date(_T('spiplistes:format_date'));
    echo "<p align='center'><b>"._T('spiplistes:date_ref')."</b><br /><input type='text' name='date' value=\"$auj\"></p>";
	echo "<p align='center'>"._T('spiplistes:alerte_modif')."<br /><br /><input type='submit' name='Valider' value='"._T('spiplistes:charger_le_patron')."' class='fondo'></p>";
	echo "</div>";
	  
	  
	  $dir = _DIR_PLUGIN_SPIPLISTES."/patrons/";

		// Ouvre un dossier bien connu, et liste tous les fichiers
		if (is_dir($dir)) {
    		if ($dh = opendir($dir)) {
        		$total_option=0;
				while (($file = readdir($dh)) !== false) {
                if($file != '..' && $file !='.' && $file !='') $total_option=$total_option+1;
        		}
        		closedir($dh);
			}
				if ($dh = opendir($dir)) {
        		echo "<select name='patron' size='".($total_option+2)."'>";
				
					while (($file = readdir($dh)) !== false) {
            if($file != '..' && $file !='.' && $file !='')		{
						  $titre_option=ereg_replace('(\.html|\.HTML)','',$file);
						  echo "<option value='$titre_option'>$titre_option</option>\n";
						}
					}
				echo "</select>";
        		closedir($dh);
   		  		}
		}
	  		  
	
	echo "</div>";
	echo "</FORM>";


                echo "<blockquote><i>"._T('spiplistes:definir_squel_texte')."</i></blockquote>";
				

        echo "</td></tr>";


	echo "</table>\n";

	echo "</FORM>";
	
	// doit on visualiser un squelette ?
	if (isset($_GET['patron'])) {
	   $patron = $_GET['patron'];
	   echo "<br /><strong>$patron</strong><br /><br />\n";
     echo _T('spiplistes:date_ref').": $date\n";
     echo "<h3>HTML</h3><a href=\"../patron.php3?patron=$patron&amp;date=$date\">(Plein écran)</a><br /><br />\n";
     echo "<iframe width=\"100%\" height=\"500\" src=\"../patron.php3?patron=$patron&amp;date=$date\"></iframe>\n";
     echo "<h3>"._T('spiplistes:val_texte')."</h3><a href=\"../patron.php3?patron=$patron&amp;date=$date&amp;format=texte\">(Plein écran)</a><br /><br />\n";  
     echo "<iframe width=\"100%\" height=\"500\" src=\"../patron.php3?patron=$patron&amp;date=$date&amp;format=texte\"></iframe>\n";       
  }	
	// doit on visualiser un squelette ? - fin

	fin_cadre_relief();

// SQUELETTES FIN ---------------------------------------------------------


// MODE CONFIG FIN -------------------------------------------------------------



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
