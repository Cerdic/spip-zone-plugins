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
global $connect_id_auteur,$couleur_foncee;
global $statut_abo,$reinitialiser_config, $Valider_reinit,$changer_config;
global $_POST;

 
$nomsite=$GLOBALS['meta']['nom_site']; 
$urlsite=$GLOBALS['meta']['adresse_site']; 

 
// Admin SPIP-Listes
echo debut_page("Spip listes", "redacteurs", "spiplistes");

// spip-listes bien installé ?
if (!function_exists(spip_listes_onglets)){
    echo("<h3>erreur: spip-listes est mal installé !</h3>");   
    echo fin_page();
	  exit;
}

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

// MODE CONFIG: Configuration de spip-listes -----------------------------------

function configurer_spip_listes() {

 
 
 
  if ($abonnement_config = _request('abonnement_config')) {
 	                $abonnement_config = addslashes($abonnement_config);
 	                ecrire_meta('abonnement_config', $abonnement_config);
 	            } 
 	            
  if ($adresse_defaut = _request('email_defaut') AND email_valide($adresse_defaut)) {
 	                ecrire_meta('email_defaut', $adresse_defaut);
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



if(!$abonnement_config = $GLOBALS['meta']['abonnement_config']){
 ecrire_meta('abonnement_config', $abonnement_config);
 ecrire_metas();
 }

$config = $GLOBALS['meta']['abonnement_config'] ;

echo debut_cadre_relief("redacteurs-24.gif", false, "", _T('spiplistes:mode_inscription'));
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
echo fin_cadre_relief();


	
echo "<form action='".generer_url_ecrire('config')."' method='post'>";

echo '<br />';
		echo debut_cadre_relief("", false, "", "Envoi des emails");


		echo debut_cadre_trait_couleur("", false, "", "Adresse d'envoi par d&eacute;faut");
		$adresse_defaut = (email_valide($GLOBALS['meta']['email_defaut'])) ? $GLOBALS['meta']['email_defaut'] : $GLOBALS['meta']['email_webmaster'];
		echo "<input type='text' name='email_defaut' value='".$adresse_defaut."' size='30' CLASS='fondl'>";

		echo fin_cadre_trait_couleur();



		echo debut_cadre_trait_couleur("", false, "", "M&eacute;thode d'envoi");
		
		echo "<div>
		Si vous n'&ecirc;tes pas s&ucirc;r, choisissez la fonction mail de PHP.
		</div>";

		$mailer_smtp = $GLOBALS['meta']['mailer_smtp'];

		echo bouton_radio("mailer_smtp", "non", "Utiliser la fonction mail() de PHP", $mailer_smtp == "non", "changeVisible(this.checked, 'smtp', 'none', 'block');");
		echo "<br />";
		echo bouton_radio("mailer_smtp", "oui", "Utiliser SMTP", $mailer_smtp == "oui", "changeVisible(this.checked, 'smtp', 'block', 'none');");

		if ($mailer_smtp == "oui") $style = "display: block;";
		else $style = "display: none;";
		echo "<div id='smtp' style='$style'>";
		echo "<ul>";
		echo "<li>H&ocirc;te <input type='text' name='smtp_server' value='".$GLOBALS['meta']['smtp_server']."' size='30' class='fondl'>";
		echo "<li>Port <input type='text' name='smtp_port' value='".$GLOBALS['meta']['smtp_port']."' size='4' class='fondl'>";
		echo "<li>Requiert une identification";
		
		$smtp_identification = $GLOBALS['meta']['smtp_identification'];
		
		echo bouton_radio("smtp_identification", "oui", "oui", $smtp_identification == "oui", "changeVisible(this.checked, 'smtp-auth', 'block', 'none');");
		echo "&nbsp;";
		echo bouton_radio("smtp_identification", "non", "non", $smtp_identification == "non", "changeVisible(this.checked, 'smtp-auth', 'none', 'block');");

		if ($smtp_identification == "oui") $style = "display: block;";
		else $style = "display: none;";
		echo "<div id='smtp-auth' style='$style'>";
		echo "<ul>";
		echo "<li>Login <input type='text' name='smtp_login' value='".$GLOBALS['meta']['smtp_login']."' size='30' CLASS='fondl'>";
		echo "<li>Password <input type='password' name='smtp_pass' value='".$GLOBALS['meta']['smtp_pass']."' size='30' CLASS='fondl'>";
		echo "</ul>";
		echo "</div>";

		echo "</ul>";
		echo "</div>";
		
		echo "<br /><br />";
		echo fin_cadre_trait_couleur();
		
		if ($mailer_smtp == "oui") $style = "display: block;";
		else $style = "display: none;" ;
		echo "<div style='$style'>";
		echo debut_cadre_relief("", false, "", "adresse email du <i>sender</i> SMTP");
		echo "<p style='margin:10px'>Lors d'un envoi via la m&eacute;thode SMTP ce champ d&eacute;finit l'adresse de l'envoyeur.</p>";
		echo "<input type='text' name='smtp_sender' value=\"".$GLOBALS['meta']['smtp_sender']."\" style='width:20em' CLASS='forml'>";
		echo fin_cadre_relief();
		echo "</div>\n";
		
		
		
echo "<input type='submit' name='valid_smtp' value='"._T('spiplistes:valider')."' class='fondo' style='float:right'>";
echo "<hr style='clear:both;visibility:hidden'>";
		
echo "</form>";	

		echo fin_cadre_relief();


if (($reinitialiser_config == 'oui' AND $Valider_reinit)) {
ecrire_meta('spiplistes_lots' , _request('spiplistes_lots')) ;
ecrire_metas();
}

echo debut_cadre_relief("redacteurs-24.gif", false, "", _T('spiplistes:tableau_bord'));

echo "<form action='".generer_url_ecrire('config')."' method='post'>";
echo "<input type='hidden' name='reinitialiser_config' value='oui'>";	
echo "<label for='spiplistes_lots'>Nombre d'envois par lot</label>" ;
echo "<input type='text' name='spiplistes_lots' value=\"".$GLOBALS['meta']['spiplistes_lots']."\" style='width:3em' CLASS='forml'>";
	

	
echo "<input type='submit' name='Valider_reinit' value='"._T('spiplistes:reinitialiser')."' class='fondo' style='float:right'>";
echo "<hr style='clear:both;visibility:hidden'>";
echo "</form>";	
echo fin_cadre_relief();

// MODE CONFIG FIN -------------------------------------------------------------



//$spiplistes_version = "SPIP-listes 1.9b2";
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
