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

function exec_courrier_edit()
{

global $connect_statut;
global $connect_toutes_rubriques;
global $connect_id_auteur;
global $type;
global $new;
global $id_message;
 
 
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

// MODE EDIT: Rédaction d'un courrier ------------------------------------------


     debut_cadre_relief('../'._DIR_PLUGIN_SPIPLISTES.'/img_pack/stock_insert-slide.gif');
     //Charger un patron ?    

	  // inclusion du script de gestion des layers de SPIP
		

		// Titre du bloc
		echo bouton_block_invisible(md5(_T('spiplistes:charger_patron')));
			echo "<a href=\"javascript:swap_couche('$compteur_block', '$spip_lang_rtl');\">"._T('spiplistes:charger_patron')."</a>";
			
		// Bloc invisible
		echo debut_block_invisible(md5(_T('spiplistes:charger_patron')));
			
		 echo "<table><tr><td>";				
	  
	  echo "<form action='?exec=import_patron&mode=courrier&id_message=$id_message' METHOD='post'>";  
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
            if($file != '..' && $file !='.' && $file !='')	{
						    $titre_option=ereg_replace('(\.html|\.HTML)','',$file);
						    echo "<option value='$titre_option'>$titre_option</option>\n";
						}
					}
				echo "</select>";
        		closedir($dh);
   		  		}
		}
	  		  
	echo "</td><td>";


	  echo "<input type='Hidden' name='inclure_patron' value=\"oui\">";
	  echo "<input type='Hidden' name='id_message' value=\"$id_message\">";
	    echo "<input type='Hidden' name='nomsite' value=\"$nomsite\">";

    $auj = date('Y/m/d');
    echo "<p align='center'>"._T('spiplistes:date_ref')."<br /><input type='text' name='date' style='text-align:center' value=\"$auj\"></p>";
	echo "<p align='center' style='padding-left:20px'>"._T('spiplistes:alerte_modif')."<br /><br /><input type='submit' name='Valider' value='"._T('spiplistes:charger_le_patron')."' class='fondo'></p>";
	echo "</FORM>";

	echo "</td></tr></table>";


		// Fin du bloc
		echo fin_block();

     fin_cadre_relief();

     //Ecrire dans le formulaire


	echo "<form action='?exec=gerer_courrier&id_message=$id_message' METHOD='post' name='formulaire'>";

	if ($type == 'nl') $le_type = _T('spiplistes:email_collec');

	echo "<font face='Verdana,Arial,Sans,sans-serif' size=2 color='green'><b>$le_type</b></font><p>";
    echo "<font face='Verdana,Arial,Helvetica,sans-serif' size=1 color='red'>"._T('spiplistes:alerte_edit')."</font><p>";

	echo "<input type='Hidden' name='modifier_message' value=\"oui\">";
	echo "<input type='Hidden' name='id_message' value=\"$id_message\">";
	if(!intval($id_message))
	echo "<input type='Hidden' name='new' value=\"oui\">";

	echo _T('texte_titre_obligatoire')."<br />";

	echo "<input type='text' class='formo' name='titre' value=\"$titre\" size='40'>";


	echo "<p><b>"._T('info_texte_message_02')."</b>";
    echo aide ("raccourcis");
    echo"<br />";
	echo afficher_barre('document.formulaire.texte');
	echo "<TEXTAREA id='text_area' name='texte' ".$GLOBALS['browser_caret']." class='formo' ROWS='20' COLS='40' wrap=soft>";
	echo $texte;
	echo "</TEXTAREA>\n";

	echo "<p align='right'><input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo'>";
	echo "</FORM>";




// MODE EDIT FIN ---------------------------------------------------------------

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
