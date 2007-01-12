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
include_spip('inc/affichage');
include_spip('base/spip-listes');
include_spip('public/assembler');



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


	if ($new == "oui") { 
	$statut = 'redac'; 
	$type = 'nl'; 
	$query = "INSERT INTO spip_courriers (titre, date, statut, type, id_auteur) VALUES ('".addslashes(filtrer_entites(_T('texte_nouveau_message')))."', NOW(), '$statut', '$type', $connect_id_auteur)"; 
	$result = spip_query($query); 
	$id_message = spip_insert_id(); 
	}
 
 
 
// Admin SPIP-Listes
echo debut_page("Spip listes", "redacteurs", "spiplistes");



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

// MODE EDIT: Rédaction d'un courrier ------------------------------------------

$query = "SELECT * FROM spip_courriers WHERE id_courrier=$id_message";
$result = spip_query($query);

if ($row = spip_fetch_array($result)) {
	$id_message = $row['id_courrier'];
	$date_heure = $row["date"];
	$titre = entites_html($row["titre"]);
	$texte = entites_html($row["texte"]);
	$type = $row["type"];
	$statut = $row["statut"];
	$expediteur = $row["id_auteur"];
	if (!($expediteur == $connect_id_auteur OR ($type == 'nl' AND $connect_statut == '0minirezo'))) die();
	}

   
    if ($type == 'nl') $le_type = _T('spiplistes:email_collec');

	echo "<p><font face='Verdana,Arial,Sans,sans-serif' size=2 color='green'><b>$le_type</b></font></p>";
    echo "<p style='margin-bottom:10px'><font face='Verdana,Arial,Helvetica,sans-serif' size=1 color='red'>"._T('spiplistes:alerte_edit')."</font><br/><br /></p>";


   
   
     echo debut_cadre_relief('../'._DIR_PLUGIN_SPIPLISTES.'/img_pack/stock_insert-slide.gif');
    
    
       
     //Charger un patron ?    

	  // inclusion du script de gestion des layers de SPIP
		if($texte ==''){
// Titre du bloc
		echo bouton_block_visible(md5(_T('spiplistes:charger_patron')));
		echo "<a href=\"javascript:swap_couche('$compteur_block', '$spip_lang_rtl');\">"._T('spiplistes:charger_patron')."</a>";	
		// Bloc invisible
		echo debut_block_visible(md5(_T('spiplistes:charger_patron')));			
	  
		}else{
		// Titre du bloc
		echo bouton_block_invisible(md5(_T('spiplistes:charger_patron')));
		echo "<a href=\"javascript:swap_couche('$compteur_block', '$spip_lang_rtl');\">"._T('spiplistes:charger_patron')."</a>";	
		// Bloc invisible
		echo debut_block_invisible(md5(_T('spiplistes:charger_patron')));			
	    }
	    
	  echo "<form action='".generer_url_ecrire("import_patron","id_message=$id_message")."' METHOD='post'>";  
	  $dir = find_in_path("patrons/");

		// Ouvre un dossier bien connu, et liste tous les fichiers
		if (is_dir($dir)) {
    		if ($dh = opendir($dir)) {
        		$total_option=0;
				while (($file = readdir($dh)) !== false) {
                	$titre_option=ereg_replace('(\.html|\.HTML)','',$file);
            		if($file != '..' && $file !='.' && $file !='' && !ereg('texte$', $titre_option) ) $total_option=$total_option+1;
        		}
        		closedir($dh);
			}
				if ($dh = opendir($dir)) {
        		echo "<select style='float:left;width:150px' name='patron' size='".($total_option+2)."'>";
				$i=0;
					while (($file = readdir($dh)) !== false) {
           			$titre_option=ereg_replace('(\.html|\.HTML)','',$file);

            if($file != '..' && $file !='.' && $file !='' && !ereg('texte$', $titre_option) )	{
						   $selected=($i==0)?'selected':'' ;
						    echo "<option value='$titre_option' $selected>$titre_option</option>\n";
						    $i++;
						}
					}
				echo "</select>";
        		closedir($dh);
   		  		}
		}
	  		  

echo "<link rel='stylesheet' href='".url_absolue(find_in_path('img_pack/date_picker.css'))."' type='text/css' media='all' />";
echo '<script src="'.url_absolue(find_in_path('javascript/datepicker.js')).'" type="text/javascript"></script>';
echo '<script src="'.url_absolue(find_in_path('javascript/jquery-dom.js')).'" type="text/javascript"></script>';

echo "\n\n<script type=\"text/javascript\"><!-- \n$(document).ready(function(){ \n $.datePicker.setDateFormat('yyyy-mm-dd');\n".
	unicode2charset(charset2unicode(recuperer_fond('formulaires/date_picker_init'),'html'))." \n $('input.date-picker').datePicker({startDate:'01/01/1900'});\n }); \n //--></script> ";


	  echo "<input type='Hidden' name='inclure_patron' value=\"oui\">";
	  echo "<input type='Hidden' name='id_message' value=\"$id_message\">";
	    echo "<input type='Hidden' name='nomsite' value=\"$nomsite\">";

    $auj = date('Y-m-d');
    echo "<div style='margin-left:200px;height:200px'><p>"._T('spiplistes:date_ref')."</p><div><input type=\"text\" class=\"date-picker\" name=\"date\" style=\"text-align:center;width:8em\" value=\"$auj\"></div>";
    
	echo "<p style='padding-left:20px;clear:both'>"._T('spiplistes:alerte_modif')."<br /><br /><input type='submit' name='Valider' value='"._T('spiplistes:charger_le_patron')."' class='fondo'></p></div>";
	echo "</form>";


		// Fin du bloc
		echo fin_block();

     echo fin_cadre_relief();

     echo "<br />";
     
 	

	echo "<form id='choppe_patron-1' action='".generer_url_ecrire("gerer_courrier","id_message=$id_message")."' METHOD='post' name='formulaire'>";
	echo "<input type='Hidden' name='modifier_message' value=\"oui\">";
	echo "<input type='Hidden' name='id_message' value=\"$id_message\">";
	if(!intval($id_message))
	echo "<input type='Hidden' name='new' value=\"oui\">";

	echo "<b>Sujet du courrier</b> (obligatoire)<br />";

	echo "<input type='text' class='formo' name='titre' value=\"$titre\" size='40'>";
	echo "<br />";
    echo "<br />";
	echo "<b>Texte du courrier</b> (HTML autoris&eacute;)";
    echo aide ("raccourcis");
    echo "<br />";
	echo afficher_barre('document.formulaire.texte');
	echo "<textarea id='text_area' name='texte' ".$GLOBALS['browser_caret']." class='formo' ROWS='20' COLS='40' wrap=soft>";
	echo $texte;
	echo "</textarea>\n";

	echo "<p align='right'><input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo'>";
	echo "</form>";



// MODE EDIT FIN ---------------------------------------------------------------

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
