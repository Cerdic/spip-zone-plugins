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
include_spip('inc/distant');
include_spip('inc/affichage');
include_spip('inc/meta');
include_spip('inc/config');


function exec_config()
{

global $connect_statut;
global $connect_toutes_rubriques;
global $connect_id_auteur;
global $statut_abo,$reinitialiser_config, $Valider_reinit,$changer_config;
global $_POST;

 
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

// MODE CONFIG: Configuration de spip-listes -----------------------------------

function configurer_spip_listes() {

  if ($abonnement_config = _request('abonnement_config')) {
 	                $abonnement_config = addslashes($abonnement_config);
 	                ecrire_meta('abonnement_config', $abonnement_config);
 	            } 


  if ($smtp_server = _request('smtp_server')) {
 	                $smtp_server = addslashes($smtp_server);
 	                ecrire_meta('smtp_server', $smtp_server);
 	            } 	            	
 if ($smtp_login = _request('smtp_login')) {
 	                $smtp_login = addslashes($smtp_login);
 	                ecrire_meta('smtp_login', $smtp_login);
 	            }
 	            
 if ($smtp_pass = _request('smtp_pass')) {
 	                $smtp_pass = addslashes($smtp_pass);
 	                ecrire_meta('smtp_pass', $smtp_pass);
 	            } 	
 	            
 if ($smtp_port = _request('smtp_port')) {
 	                $smtp_port = addslashes($smtp_port);
 	                ecrire_meta('smtp_port', $smtp_port);
 	            }   
 	            
  if ($mailer_smtp = _request('mailer_smtp')) {
 	                $mailer_smtp = addslashes($mailer_smtp);
 	                ecrire_meta('mailer_smtp', $mailer_smtp);
 	            }           
 
 if ($smtp_identification = _request('smtp_identification')) {
 	                $smtp_identification = addslashes($smtp_identification);
 	                ecrire_meta('smtp_identification', $smtp_identification);
 	            }
 
  if ($smtp_sender = _request('smtp_sender')) {
 	                $smtp_sender = addslashes($smtp_sender);
 	                ecrire_meta('smtp_sender', $smtp_sender);
 	            }
 
 	             	  
ecrire_metas();
}

configurer_spip_listes();


$accepter_visiteurs = lire_meta('accepter_visiteurs');

if($accepter_visiteurs != 'oui'){
$accepter_visiteurs = 'oui';
ecrire_meta("accepter_visiteurs", $accepter_visiteurs);
ecrire_metas();
echo _T('spiplistes:autorisation_inscription');
}



if(!$abonnement_config = lire_meta('abonnement_config')){
 ecrire_meta('abonnement_config', $abonnement_config);
 ecrire_metas();
 }

$config = lire_meta('abonnement_config') ;

debut_cadre_relief("redacteurs-24.gif", false, "", _T('spiplistes:mode_inscription'));
echo "<form action='".generer_url_ecrire('config')."' method='post'>";
echo "<input type='hidden' name='changer_config' value='oui'>";
 
	echo "<table border=0 cellspacing=1 cellpadding=3 width=\"100%\">";

	echo "<tr><td background='img_pack/rien.gif' class='verdana2'>";
	  if($spip_version < 1.8 ){
                echo "<h3>"._T('spiplistes:mode_inscription')."</h3>" ;
          }
	 $texte1 = '' ;
	 $texte2 = '' ;
        ($config == 'simple' ) ? $texte1 = "checked"  : $texte2 = "checked" ;

  echo "<input type='radio' name='abonnement_config' value='simple' $texte1 id='statut_simple'>";
	echo "<label for='statut_simple'>"._T('spiplistes:abonnement_simple')."</label> ";
	echo "<p><input type='radio' name='abonnement_config' value='membre' $texte2 id='statut_membre'>";
	echo "<label for='statut_membre'>"._T('spiplistes:abonnement_code_acces')."</label></b> ";
	echo "</td></tr>";
	echo "<tr><td style='text-align:$spip_lang_right;'>";
	echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo'>";
	echo "</td></tr>";
	echo "</table>\n";

echo "</form>";
fin_cadre_relief();


	
echo "<form action='".generer_url_ecrire('config')."' method='post'>";

echo '<br />';
		debut_cadre_trait_couleur("", false, "", "Envoi des emails");

		debut_cadre_relief("", false, "", "M&eacute;thode d'envoi");
		
		echo "<div>
		Si vous n'&ecirc;tes pas s&ucirc;rs, choisissez la fonction mail de PHP.
		</div>";

		$mailer_smtp = lire_meta('mailer_smtp');

		echo bouton_radio("mailer_smtp", "non", "Utiliser la fonction mail() de PHP", $mailer_smtp == "non", "changeVisible(this.checked, 'smtp', 'none', 'block');");
		echo "<br />";
		echo bouton_radio("mailer_smtp", "oui", "Utiliser SMTP", $mailer_smtp == "oui", "changeVisible(this.checked, 'smtp', 'block', 'none');");

		if ($mailer_smtp == "oui") $style = "display: block;";
		else $style = "display: none;";
		echo "<div id='smtp' style='$style'>";
		echo "<ul>";
		echo "<li>H&ocirc;te <input type='text' name='smtp_server' value='".lire_meta('smtp_server')."' size='30' class='fondl'>";
		echo "<li>Port <input type='text' name='smtp_port' value='".lire_meta('smtp_port')."' size='4' class='fondl'>";
		echo "<li>Requiert une identification";
		
		$smtp_identification = lire_meta('smtp_identification');
		
		echo bouton_radio("smtp_identification", "oui", "oui", $smtp_identification == "oui", "changeVisible(this.checked, 'smtp-auth', 'block', 'none');");
		echo "&nbsp;";
		echo bouton_radio("smtp_identification", "non", "non", $smtp_identification == "non", "changeVisible(this.checked, 'smtp-auth', 'none', 'block');");

		if ($smtp_identification == "oui") $style = "display: block;";
		else $style = "display: none;";
		echo "<div id='smtp-auth' style='$style'>";
		echo "<ul>";
		echo "<li>Login <input type='text' name='smtp_login' value='".lire_meta('smtp_login')."' size='30' CLASS='fondl'>";
		echo "<li>Password <input type='password' name='smtp_pass' value='".lire_meta('smtp_pass')."' size='30' CLASS='fondl'>";
		echo "</ul>";
		echo "</div>";

		echo "</ul>";
		echo "</div>";
		
		echo "<br /><br />";
		fin_cadre_relief();
		
		if ($mailer_smtp == "oui") $style = "display: block;";
		else $style = "display: none;" ;
		echo "<div style='$style'>";
		debut_cadre_relief("", false, "", "adresse email du <i>sender</i> SMTP");
		echo "<p style='margin:10px'>Lors d'un envoi via la m&eacute;thode SMTP ce champ d&eacute;finit l'adresse de l'envoyeur.</p>";
		echo "<input type='text' name='smtp_sender' value=\"".lire_meta('smtp_sender')."\" style='width:20em' CLASS='forml'>";
		fin_cadre_relief();
		echo "</div>\n";
		
		
		
echo "<input type='submit' name='valid_smtp' value='"._T('spiplistes:valider')."' class='fondo' style='float:right'>";
echo "<hr style='clear:both;visibility:hidden'>";
		
echo "</form>";	

		fin_cadre_trait_couleur();



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


        	echo "<form action='".generer_url_ecrire('config')."' METHOD='post'>"; 
         
    echo "<div>";
	  echo "<div style='float:right;width:200px'>";


    $auj = date(_T('spiplistes:format_date'));
    echo "<p align='center'><b>"._T('spiplistes:date_ref')."</b><br /><input type='text' name='date' value=\"$auj\"></p>";
	echo "<p align='center'><br /><br /><input type='submit' name='Valider' value='"._T('spiplistes:charger_le_patron')."' class='fondo'></p>";
	echo "</div>";
	  
	  
	  $dir = find_in_path("patrons/");

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
	echo "</form>";


                echo "<blockquote><i>"._T('spiplistes:definir_squel_texte')."</i></blockquote>";
				

        echo "</td></tr>";


	echo "</table>\n";

	
	// doit on visualiser un squelette ?
if (isset($_POST['patron'])) {
	   $patron = $_POST['patron'];
	  
	  if (isset($_POST['date'])) 
	   $date = $_POST['date'];
	
	   	
	   echo "<br /><strong>$patron</strong><br /><br />\n";
     echo _T('spiplistes:date_ref').": $date\n";
     echo "<h3>HTML</h3><a href=\"".generer_url_public('patron_switch',"patron=".$patron."&date=".$date)."\">(Plein &eacute;cran)</a><br /><br />\n";
     echo "<iframe width=\"100%\" height=\"500\" src=\"".generer_url_public('patron_switch',"patron=".$patron."&date=".$date)."\"></iframe>\n";
     echo "<h3>"._T('spiplistes:val_texte')."</h3><a href=\"".generer_url_public('patron_switch',"patron=".$patron."&date=".$date."&format=texte")."\">(Plein &eacute;cran)</a><br /><br />\n";  
    
    echo generer_url_public('patron_switch',"patron=$patron&date=$date") ;
$texte_patron = recuperer_page(generer_url_public('patron_switch',"patron=$patron",true)) ;
echo $texte_patron.version_texte($texte_patron) ;
           
    }
	// doit on visualiser un squelette ? - fin

	fin_cadre_relief();

// SQUELETTES FIN ---------------------------------------------------------



$deb = lire_meta('debut');

if ( !($deb) OR ($reinitialiser_config == 'oui' AND $Valider_reinit)) {
spip_query("DELETE from spip_messages WHERE statut='encour'");
ecrire_meta('debut', 0 ) ;
ecrire_meta('lock' , 'non') ;
ecrire_meta('total_auteurs' , 0) ;
ecrire_metas();
}

debut_cadre_relief("redacteurs-24.gif", false, "", _T('spiplistes:tableau_bord'));

echo "<form action='".generer_url_ecrire('config')."' method='post'>";
echo "<input type='hidden' name='reinitialiser_config' value='oui'>";	

	echo "<br />"._T('spiplistes:lock').lire_meta('lock') ;
	echo "<br />"._T('spiplistes:mail_a_envoyer').lire_meta('total_auteurs') ;
	echo "<br />"._T('spiplistes:mail_tache_courante').lire_meta('debut') ;
	

	
echo "<input type='submit' name='Valider_reinit' value='"._T('spiplistes:reinitialiser')."' class='fondo' style='float:right'>";
echo "<hr style='clear:both;visibility:hidden'>";
echo "</form>";	
fin_cadre_relief();

// MODE CONFIG FIN -------------------------------------------------------------

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
