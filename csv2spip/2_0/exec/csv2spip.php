<?php
/* csv2spip est un plugin pour créer/modifier les visiteurs, rédacteurs et administrateurs restreints d'un SPIP à partir de fichiers CSV
*	 					VERSION : 3.1 => plugin pour spip 2.*
*
* Auteur : cy_altern
*  
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
*  
**/
if (!defined("_ECRIRE_INC_VERSION")) return;

define('_LOGO_CSV2SPIP', _DIR_PLUGIN_CSV2SPIP."/img_pack/csv2spip-24.gif");

include_spip('auth/sha256.inc');
include_spip('inc/csv2spip_import');

// a partir de SPIP 2.1 il faut crypter les pass en sha256 a l place du md5 des version precedentes
// commit de creation de la version 2.1: 14864 cf 
// http://core.spip.org/projects/spip/repository/revisions/14864

function csv2spip_crypt_pass($input) {			
	global $spip_version_code;
    if ($spip_version_code < 14864)
		return md5($input);
	else return sha256($input);
}


function exec_csv2spip() {
	global $spip_version_code;

// on assure la variable au cas où...
	$plugin_accesgroupes = 0;
/*
// pas d'acces_groupe pour cette version    
// le plugin acces_groupes est il installé/activé ?
		 $plugin_accesgroupes = 0;
// version compatible >= 1.9.2... nettement plus sure : on teste la présence de la constante chemin_du_plugin 
// et non pas le nom du dossier de plugin stocké dans spip_meta
	if (defined('_DIR_PLUGIN_ACCESGROUPES')) {
		$plugin_accesgroupes = 1;
					 }			
 */
	include_spip('inc/autoriser');
	if(!autoriser('webmestre')) {
	  include_spip('inc/minipres');
	  echo minipres();
	} else {
    
	$commencer_page = charger_fonction('commencer_page', 'inc');
        echo $commencer_page(_T('csvspip:titre_page'));
	echo "\r\n<style type=\"text/css\">				 
    	\r\n.Cerreur { background-color: #f33; display: block; padding: 10px; }
    	\r\n.Cok { width: 47%; background-color: #ddd; display: block; padding: 10px; }
        \r\n.Tpetit { font-size: 75%; }
        \r\n.ss_cadre { background-color: #eee; margin: 5px; padding: 5px; }
        \r\n</style>";
         
	echo gros_titre(_T('csvspip:titre_page'), '',false);
	echo debut_grand_cadre(true);

	echo debut_gauche('',true);
	echo debut_boite_info(true);
	echo "<strong>"._T('csvspip:titre_info')."</strong><br /><br />";
	echo "\r\n"._T('csvspip:help_info');
	$version_script = lire_meta('csv2spip_version');
	echo "<br /><br /><strong>"._T('csvspip:version')."</strong>".$version_script;
	if ($_FILES['userfile']['name'] != '') {  
		echo "<br /><br /><a href=\"".$PHP_SELF."?exec=csv2spip\"><img src=\"".find_in_path('images/cal-today.gif')."\"> "._T('csvspip:retour_saisie')."</a>";
	}				 
	echo fin_boite_info(true);
	echo debut_droite('',true);
				 
	if (empty($_FILES['userfile']['name']))
	    csv2spip_formulaire();
 	elseif ($_FILES['userfile']['error'] != 0) { 
		echo debut_cadre_couleur(_LOGO_CSV2SPIP, true, "", _T('csvspip:titre_etape1'));
        	echo "<br><span class=\"Cerreur\">"._T('csvspip:err_etape1.1_debut').$_FILES['userfile']['tmp_name']._T('csvspip:err_etape1.1_fin').$_FILES['userfile']['error']."</span>";
		echo fin_cadre_couleur(true);
	} else {
	// Etape 1 : analyser le fichier 
		$f = $_FILES['userfile']['tmp_name'];

		echo debut_cadre_couleur(_LOGO_CSV2SPIP, true, "", _T('csvspip:titre_etape1'));
		echo "<br />"._T('csvspip:ok_etape1'). $_FILES['userfile']['name']."<br />";
		echo fin_cadre_couleur(true);

		$res = csv2spip_analyse(csv2spip_normalise($f));

        // Etape 2 : transfert des données vers une table SQL temporaire
		if (is_string($res))
		  echo "<br /><span class=\"Cerreur\">", $res, "</span>";
		else {
			include_spip('inc/csv2spip_tables');
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			// ignorer les lignes vides rencontrees
			foreach($res as $insert) 
			  if (is_array($insert))
			    sql_insertq('spip_tmp_csv2spip', $insert);

	// Passer aux etapes suivantes
			csv2spip_etapes();

	// Fin: suppresion de la table temporaire
			sql_drop_table('spip_tmp_csv');
		}
	}

	echo fin_grand_cadre(true),fin_page();
	}
}

// Formulaire de saisie du fichier CSV et des options de config		
function csv2spip_formulaire()
{
	  echo "<script language=\"JavaScript\"> ";
	  echo "	 function aff_masq(id_elem, vis) { ";
	  echo "		vis == 0 ? s_vis = 'none' : s_vis = 'block'; ";
	  echo "		document.getElementById(id_elem).style.display = s_vis; ";
	  echo "		this.checked = 'checked'; ";
	  echo "	}";
	  echo "</script>";

          // debut_cadre_formulaire();
	  echo "\r\n<form name=\"csv2spip\" enctype=\"multipart/form-data\" action=\"".$PHP_SELF."?exec=csv2spip\" method=\"post\" onsubmit=\"return (verifSaisie());\">";
    		echo debut_cadre_couleur("cal-today.gif", true, "", _T('csvspip:titre_choix_fichier'));
            echo "<strong>"._T('csvspip:choix_fichier')."</strong><input name=\"userfile\" type=\"file\">";
			 	echo "<br><br /><strong>"._T('csvspip:nom_groupe_redac')."</strong><input type=\"text\" name=\"groupe_redacs\" value=\"REDACTEURS\">";
				echo "<br><br /><strong>"._T('csvspip:nom_groupe_admin')."</strong><input type=\"text\" name=\"groupe_admins\" value=\"ADMINS\">";
				echo "<br><br /><strong>"._T('csvspip:nom_groupe_visit')."</strong><input type=\"text\" name=\"groupe_visits\" value=\"VISITEURS\">";
       	    echo "<br><span style=\"font-size: 10px;\">"._T('csvspip:help_nom_groupe_admin')."</span>";		
				echo fin_cadre_couleur(true);
             
				echo debut_cadre_couleur("mot-cle-24.gif", true, "", _T('csvspip:options_maj'));
				echo "<strong>"._T('csvspip:maj_utils')."</strong>";
    		echo _T('csvspip:oui')."<input type=\"radio\" name=\"maj_gene\" value=\"1\"  checked=\"checked\" onClick=\"aff_masq('maj_avance', 1);\">"; 
    		echo "<input type=\"radio\" name=\"maj_gene\" value=\"0\" onClick=\"aff_masq('maj_avance', 0);\">"._T('csvspip:non');
            echo "<div id=\"maj_avance\" class=\"ss_cadre\">";
    		echo "<br /><strong>"._T('csvspip:maj_mdp')."</strong>"; 
    		echo _T('csvspip:oui')."<input type=\"radio\" name=\"maj_mdp\" value=\"1\"  checked=\"checked\">"; 
    		echo "<input type=\"radio\" name=\"maj_mdp\" value=\"0\">"._T('csvspip:non');
/*
// plugin acces_groupes n'existe pas dans cette version de SPIP				
            echo "<br /><br /><strong>"._T('csvspip:maj_grpes')."</strong>";
				echo "<ul style=\"padding: 0px; margin: 0px 0px 0px 30px;\">";
				echo "<li style=\"list-style-image: url('".find_in_path('images/redac-12.gif')."');\"><strong>"._T('csvspip:redacs').":</strong> ";
				echo _T('csvspip:oui')."<input type=\"radio\" name=\"maj_grpes_redac\" value=\"1\" checked=\"checked\">";
				echo "<input type=\"radio\" name=\"maj_grpes_redac\" value=\"0\">"._T('csvspip:non');
				echo "</li>";
				echo "<li style=\"list-style-image: url('".find_in_path('images/admin-12.gif')."');\"><strong>"._T('csvspip:admins').":</strong> ";
				echo _T('csvspip:oui')."<input type=\"radio\" name=\"maj_grpes_admin\" value=\"1\" checked=\"checked\">";
				echo "<input type=\"radio\" name=\"maj_grpes_admin\" value=\"0\">"._T('csvspip:non'); 
				echo "</li>";
				echo "<li style=\"list-style-image: url('".find_in_path('images/visit-12.gif')."');\"><strong>"._T('csvspip:visits').":</strong> ";
				echo _T('csvspip:oui')."<input type=\"radio\" name=\"maj_grpes_visit\" value=\"1\" checked=\"checked\" >";
				echo "<input type=\"radio\" name=\"maj_grpes_visit\" value=\"0\">"._T('csvspip:non');
				echo "</li>";				 
    		echo "</ul>";
				echo "<span style=\"font-size: 10px;\">"._T('csvspip:help_maj_grpes')."</span><br>"; 
*/            
            echo "<br /><br /><img src=\"".find_in_path('images/admin-12.gif')."\" alt=\"admins uniquement\"> <strong>"._T('csvspip:maj_rub_adm')."</strong>";
       	    echo "<input type=\"radio\" name=\"maj_rub_adm\" value=\"1\" checked=\"checked\">"._T('csvspip:oui');   
            echo "<input type=\"radio\" name=\"maj_rub_adm\" value=\"0\">"._T('csvspip:non'); 
				echo "<br /><span style=\"font-size: 10px;\">"._T('csvspip:help_maj_rub_adm')."</span><br>"; 
				echo "</div>";				 
				echo fin_cadre_couleur(true);
             
				echo debut_cadre_couleur(_DIR_PLUGIN_CSV2SPIP."/img_pack/supprimer_utilisateurs-24.gif", true, "", _T('csvspip:suppr_absents'));
    		echo "<strong>"._T('csvspip:suppr_utilis')."</strong><ul style=\"padding: 0px; margin: 0px 0px 0px 30px;\">";
				echo "<li style=\"list-style-image: url('".find_in_path('images/redac-12.gif')."');\">"._T('csvspip:suppr_redac')."";
				echo _T('csvspip:oui')."<input type=\"radio\" name=\"eff_redac\" value=\"1\" onClick=\"aff_masq('archi', 1);\">";
				echo "<input type=\"radio\" name=\"eff_redac\" value=\"0\" checked=\"checked\" onClick=\"if (document.csv2spip.eff_admin[1].checked == true) { aff_masq('archi', 0) };\">"._T('csvspip:non');
				echo "</li>";
				echo "<li style=\"list-style-image: url('".find_in_path('images/admin-12.gif')."');\">"._T('csvspip:suppr_admin');
				echo _T('csvspip:oui')."<input type=\"radio\" name=\"eff_admin\" value=\"1\" onClick=\"aff_masq('archi', 1);\">";
				echo "<input type=\"radio\" name=\"eff_admin\" value=\"0\" checked=\"checked\" onClick=\"if (document.csv2spip.eff_redac[1].checked == true) { aff_masq('archi', 0) };\">"._T('csvspip:non'); 
				echo "</li>";
				echo "<li style=\"list-style-image: url('".find_in_path('images/visit-12.gif')."');\">"._T('csvspip:suppr_visit')."";
				echo _T('csvspip:oui')."<input type=\"radio\" name=\"eff_visit\" value=\"1\" >";
				echo "<input type=\"radio\" name=\"eff_visit\" value=\"0\" checked=\"checked\" >"._T('csvspip:non');
				echo "</li>";				 
    		echo "</ul><span style=\"font-size: 10px;\">"._T('csvspip:help_suppr_redac')."</span><br>"; 
    		echo "<div style=\"display: none\" id=\"archi\" class=\"ss_cadre\"><br /><strong>"._T('csvspip:suprr_articles')."</strong>";
            echo _T('csvspip:oui')."<input type=\"radio\" name=\"supprimer_articles\" value=\"1\" onClick=\"aff_masq('transfert', 0);\">";   
            echo "<input type=\"radio\" name=\"supprimer_articles\" value=\"0\" checked=\"checked\" onClick=\"aff_masq('transfert', 1);\">"._T('csvspip:non'); 
            echo "<div id=\"transfert\" class=\"ss_cadre\"><br><strong>"._T('csvspip:transfert_archive')."</strong>";
           	echo "<input type=\"radio\" name=\"archivage\" value=\"1\" checked=\"checked\" onClick=\"aff_masq('rub_transfert', 1);\">"._T('csvspip:oui');   
            echo "<input type=\"radio\" name=\"archivage\" value=\"0\" onClick=\"aff_masq('rub_transfert', 0);\">"._T('csvspip:non'); 
            echo "<div id=\"rub_transfert\" class=\"ss_cadre\"><br>";
            $sql9 = sql_query("SELECT COUNT(*) AS nb_rubriques FROM spip_rubriques");
    		$data9 = mysql_fetch_array($sql9);
    		$nb_rubriques = $data9['nb_rubriques'];
    		$annee = date("Y"); 
    		echo "<strong>"._T('csvspip:nom_rubrique_archives')."</strong>";
    		echo "<input type=\"text\" name=\"rub_archivage\" value=\"Archives annee ".($annee - 1).'-'.$annee."\" style=\"width: 200px;\">";
    		echo "";
            if ($nb_rubriques > 0) {   		
                echo"<br><br><strong>"._T('csvspip:choix_parent_archive')."</strong>"; 
                $sql10 = sql_query("SELECT id_rubrique, titre, id_secteur FROM spip_rubriques ORDER BY titre");
                echo "<select name=\"rub_parent_archivage\">";
                echo "<option value=\"0,0\" selected=\"selected\">"._T('csvspip:racine_site')."</option>";
            		
             	while ($data10 = mysql_fetch_array($sql10)) { 
             	    echo "<option value=\"".$data10['id_rubrique'].",".$data10['id_secteur']."\">".$data10['titre']."</option>";
            	}				 						
               	echo "</select><br>";
            }
            else { 
                echo "<br>"._T('csvspip:pas_de_rubriques')."<br>";
            }  		
            echo "</div></div>";
            echo "<br><br><strong>"._T('csvspip:traitement_supprimes')."</strong><br>";
            echo "<input type=\"radio\" name=\"auteurs_poubelle\" value=\"1\">"._T('csvspip:auteurs_poubelle')."  <br>"; 
            echo "<input type=\"radio\" name=\"auteurs_poubelle\" value=\"0\" checked=\"checked\">"._T('csvspip:attribuer_articles'); 
            echo "<input type=\"text\" name=\"nom_auteur_archives\" value=\"archives".($annee - 1)."-".$annee."\">"._T('csvspip:passe_egale_login');
            echo "</div>";
            echo fin_cadre_couleur(true);
            
            echo debut_cadre_couleur("rubrique-24.gif", true, "", _T('csvspip:creation_rubriques'));
            //			 echo "<h3>"._T('csvspip:creation_rubriques')."</h3>";
            echo "<strong>"._T('csvspip:rubrique_ss_groupes')."</strong>"; 
            echo _T('csvspip:oui')."<input type=\"radio\" name=\"rub_prof\" value=\"1\" checked=\"checked\" onClick=\"aff_masq('rub_adm', 1);\">";   
            echo "<input type=\"radio\" name=\"rub_prof\" value=\"0\" onClick=\"aff_masq('rub_adm', 0);\">"._T('csvspip:non');
            echo "<br><span style=\"font-size: 10px;\">"._T('csvspip:profs_admins')."</span>";
            echo "<br /><div id=\"rub_adm\" class=\"ss_cadre\">";
            if ($nb_rubriques > 0) {   		
                echo "<br /><strong>"._T('csvspip:choix_parent_rubriques')."</strong>"; 
              	echo "<select name=\"rub_parent\">";
              	echo "<option value=\"0,0\" selected=\"selected\">"._T('csvspip:racine_site')."</option>";
                $sql10 = sql_query("SELECT id_rubrique, titre, id_secteur FROM spip_rubriques ORDER BY titre");
            	while ($data10 = mysql_fetch_array($sql10)) { 
             	    echo "<option value=\"".$data10['id_rubrique'].",".$data10['id_secteur']."\">".$data10['titre']."</option>";
            	}  	
                echo "</select>";
            }
            else {  
              	echo "<br>"._T('csvspip:pas_de_rubriques');
            } 		
            echo "<br /><br /><strong>"._T('csvspip:article_rubrique')."</strong>"; 
            echo _T('csvspip:oui')."<input type=\"radio\" name=\"art_rub\" value=\"1\">";   
            echo "<input type=\"radio\" name=\"art_rub\" value=\"0\" checked=\"checked\">"._T('csvspip:non');
            echo "<br><span style=\"font-size: 10px;\">"._T('csvspip:help_articles')."</span>";
            echo "<br /></div>";
            echo "<br /><div id=\"rub_adm_defaut\">";
            echo "<strong>"._T('csvspip:choix_rub_admin_defaut')."</strong>";
            echo "<input type=\"text\" name=\"rub_admin_defaut\" value=\""._T('csvspip:nom_rub_admin_defaut')."\" style=\"width: 200px;\">";
            echo "<br><span style=\"font-size: 10px;\">"._T('csvspip:help_rub_admin_defaut')."</span>";
            if ($nb_rubriques > 0) {   		
                echo "<br/><br/><strong>"._T('csvspip:choix_parent_rub_admin_defaut')."</strong>"; 
              	echo "<select name=\"rub_parent_admin_defaut\">";
              	echo "<option value=\"0,0\" selected=\"selected\">"._T('csvspip:racine_site')."</option>";
                $sql108 = sql_query("SELECT id_rubrique, titre, id_secteur FROM spip_rubriques ORDER BY titre");
            	while ($data108 = mysql_fetch_array($sql108)) { 
             	    echo "<option value=\"".$data108['id_rubrique'].",".$data108['id_secteur']."\">".$data108['titre']."</option>";
            	}  	
                echo "</select><br />";
            }
            else {  
              	echo "<br>"._T('csvspip:pas_de_rubriques')."<br>";
            } 		
            echo "</div>"; 
            echo fin_cadre_couleur(true);

/* 
// pas la peine de s'escagacer avec accès_groupe dans cette version: ce plugin n'existe pas pour SPIP 2.* ...            
            echo debut_cadre_couleur(_DIR_PLUGIN_CSV2SPIP."/img_pack/groupe-24.png", true, "", _T('csvspip:acces_groupes'));
            echo "<strong>"._T('csvspip:option_acces_groupes')."</strong>"; 
            if ($plugin_accesgroupes == 1) {
          		 echo "<ul style=\"padding: 0px; margin: 0px 0px 0px 30px;\">";
        		 echo "<li style=\"list-style-image: url('".find_in_path('images/redac-12.gif')."');\">"._T('csvspip:ss_groupes_redac')." ";
        		 echo _T('csvspip:oui')."<input type=\"radio\" name=\"ss_groupes_redac\" value=\"1\">";
        		 echo "<input type=\"radio\" name=\"ss_groupes_redac\" value=\"0\" checked=\"checked\">"._T('csvspip:non')."</li>";
        		 echo "<li style=\"list-style-image: url('".find_in_path('images/admin-12.gif')."');\">"._T('csvspip:ss_groupes_admin')." ";
        		 echo _T('csvspip:oui')."<input type=\"radio\" name=\"ss_groupes_admin\" value=\"1\">";
        		 echo "<input type=\"radio\" name=\"ss_groupes_admin\" value=\"0\" checked=\"checked\">"._T('csvspip:non')."</li>"; 
        		 echo "<li style=\"list-style-image: url('".find_in_path('images/visit-12.gif')."');\">"._T('csvspip:ss_groupes_visit')." ";
        		 echo _T('csvspip:oui')."<input type=\"radio\" name=\"ss_groupes_visit\" value=\"1\" >";
        		 echo "<input type=\"radio\" name=\"ss_groupes_visit\" value=\"0\" checked=\"checked\" >"._T('csvspip:non')."</li>";
          		 echo "</ul>";
            	 echo "<span style=\"font-size: 10px;\">"._T('csvspip:help_acces_groupes')."</span>";
            	 echo "<br /><br /><strong>"._T('csvspip:ss_grpes_reinitialiser')."</strong>";
        		 echo _T('csvspip:oui')."<input type=\"radio\" name=\"ss_grpes_reinitialiser\" value=\"1\" checked=\"checked\">";
        		 echo "<input type=\"radio\" name=\"ss_grpes_reinitialiser\" value=\"0\">"._T('csvspip:non')."<br />";
            	 echo "<span style=\"font-size: 10px;\">"._T('csvspip:help_reinitialiser')."</span>";
            }
            else {
            	echo "<br /><span class=\"Cerreur\">"._T('csvspip:abs_acces_groupes')."</span><br />";
            }
            echo fin_cadre_couleur(true);
*/            
            echo "<input type=\"submit\" value=\""._T('csvspip:lancer')."\" style=\"background-color: #FF8000; font-weight: bold; font-size: 14px;\">";
            echo "</form><br><br />";
            
            echo debut_cadre_trait_couleur("fiche-perso-24.gif", true, "", _T('csvspip:titre_help')); 
          // inclure le fichier help de la langue
            $code_langue = (!$GLOBALS['spip_lang'] ? lire_meta("langue_site") : $GLOBALS['spip_lang']);
            if (!find_in_path(_DIR_PLUGIN_CSV2SPIP.'lang/csvspip_help_'.$code_langue.'.php')) $code_langue = 'fr';
            include(_DIR_PLUGIN_CSV2SPIP.'lang/csvspip_help_'.$code_langue.'.php');
            echo "<a href=\""._DIR_PLUGIN_CSV2SPIP."tests_csv2spip/csv2spip_modele.csv\">csv2spip_modele.csv</a>";
            echo fin_cadre_trait_couleur(true);
            
}
		 

// TRAITEMENT DES DONNEES ENVOYEES PAR LE FORMULAIRE DE SAISIE

function csv2spip_etapes()
{
// Etape 0 : définition des noms de tables SPIP

	$Tauteurs = 'spip_auteurs';
	$Tauteurs_rubriques = 'spip_auteurs_rubriques';
	$Tarticles =  'spip_articles';
	$Tauteurs_articles = 'spip_auteurs_articles';
	$Taccesgroupes_groupes = 'spip_accesgroupes_groupes';
	$Taccesgroupes_auteurs = 'spip_accesgroupes_auteurs';
		
	$err_total = 0;

        // étape 3 : si nécessaire création des rubriques pour les admins restreints et des groupes pour accesgroupes
        $_POST['groupe_admins'] != '' ? $groupe_admins = strtolower($_POST['groupe_admins']) : $groupe_admins = '-1';
        $_POST['groupe_visits'] != '' ? $groupe_visits = strtolower($_POST['groupe_visits']) : $groupe_visits = '-1';
        $_POST['groupe_redacs'] != '' ? $groupe_redacs = strtolower($_POST['groupe_redacs']) : $groupe_redacs = '-1';
        echo debut_cadre_couleur(_LOGO_CSV2SPIP, true, "", _T('csvspip:titre_etape3'));
        
        // étape 3.1 : création des rubriques pour les admins restreints
        if ($_POST['rub_prof'] == 1 AND $groupe_admins != '-1') {
		$Terr_rub = array();
        	$Tres_rub = array();
        	$date_rub_ec = date("Y-m-j H:i:s");
        	$Tch_rub = explode(',', $_POST['rub_parent']);
        	$rubrique_parent = $Tch_rub[0];
        	$secteur = $Tch_rub[1];
        	$sql8 = sql_select('ss_groupe', 'spip_tmp_csv2spip', "LOWER(groupe) = '$groupe_admins' AND ss_groupe != ''", "ss_groupe");
		if(true)
                while ($data8 = sql_fetch($sql8)) {
        		    $rubrique_ec = $data8['ss_groupe']; 
            		$sql7 = sql_query("SELECT COUNT(*) AS rub_existe FROM spip_rubriques WHERE titre = '$rubrique_ec' LIMIT 1");
            		$data7 = mysql_fetch_array($sql7);
            		if ($data7['rub_existe'] > 0) {
        //print '<br>etape3 : rubrique '.$rubrique_ec.' existe';
            		    continue;
            		}
            		sql_query("INSERT INTO spip_rubriques (id_rubrique, id_parent, titre, id_secteur, statut, date) VALUES ('', '$rubrique_parent', '$rubrique_ec', '$secteur', 'publie', '$date_rub_ec')" );
              		if (mysql_error() != '') {
              		    $Terr_rub[] = array('ss_groupe' => $rubrique_ec, 'erreur' => mysql_error());
              		}
        			else {
        			    $Tres_rub[] = $rubrique_ec;
        			}
        		}
        	}		
        	if (count($Terr_rub) > 0) {  
        	    echo "<br><span class=\"Cerreur\">"._T('csvspip:err_etape3.1');
        		foreach ($Terr_rub as $er) {
        		    echo "<br>"._T('csvspip:rubrique').$er['ss_groupe']._T('csvspip:erreur').$er['erreur'];
        		}
        		echo "</span>";
                $err_total ++;
        	}		
        	else {
        	    echo "<br>"._T('csvspip:ok_etape3.1_debut').count($Tres_rub)._T('csvspip:ok_etape3.1_fin')."<br>";
        	}
        
        // gestion de la rubrique par défaut des admins restreints
        if ($groupe_admins != '-1') {
          // faut-il créer la rubrique par défaut?
        	$cree_rub_adm_defaut = 0;
        	if ($_POST['rub_prof'] == 0) {
        	    $sql20 = sql_query("SELECT COUNT(*) AS nb_admins FROM spip_tmp_csv2spip WHERE LOWER(groupe) = '$groupe_admins'");
        		$rows20 = mysql_fetch_array($sql20);
        		if ($rows20['nb_admins'] > 0) {
        		    $cree_rub_adm_defaut = 1;
        		}							 
        	}
        	else {
        	    $sql19 = sql_query("SELECT COUNT(*) AS nb_sans_ssgrpe FROM spip_tmp_csv2spip WHERE LOWER(groupe) = '$groupe_admins' AND ss_groupe = ''");
        		$rows19 = mysql_fetch_array($sql19);
        		if ($rows19['nb_sans_ssgrpe'] > 0) {
        		    $cree_rub_adm_defaut = 1;
        		}
        	}
        //print '<br>$cree_rub_adm_defaut	= '.$cree_rub_adm_defaut;
             // création de la rubrique par défaut
        	if ($cree_rub_adm_defaut == 1) {
        	    $date_rub_defaut = date("Y-m-j H:i:s");
        		$Tch_rub_defaut = explode(',', $_POST['rub_parent_admin_defaut']);
        		$rubrique_parent_defaut = $Tch_rub_defaut[0];
        		$secteur_defaut = $Tch_rub_defaut[1];
        	 	$rubrique_defaut = ($_POST['rub_admin_defaut'] != '' ? $_POST['rub_admin_defaut'] : _T('csvspip:nom_rub_admin_defaut') );
        		$sq21 = sql_query("SELECT COUNT(*) AS rub_existe FROM spip_rubriques WHERE titre = '$rubrique_defaut' LIMIT 1");
        		$rows21 = mysql_fetch_array($sq21);
        		if ($rows21['rub_existe'] < 1) {
        		    sql_query("INSERT INTO spip_rubriques (id_rubrique, id_parent, titre, id_secteur, statut, date) 
        			 		    VALUES ('', '$rubrique_parent_defaut', '$rubrique_defaut', '$secteur_defaut', 'prive', '$date_rub_defaut')" );
        	 		if (mysql_error() != '') {
        			    echo "<br><span class=\"Cerreur\">"._T('csvspip:err_cree_rub_defaut').mysql_error()."</span>";
        				$err_total ++;
        			}
            		else {
            		    echo "<br>"._T('csvspip:ok_cree_rub_defaut').$rubrique_defaut."<br />";
        				$id_rub_admin_defaut = mysql_insert_id();
            		}
        		}
        		else {
        		    $sql1001 = sql_query("SELECT id_rubrique FROM spip_rubriques WHERE titre = '$rubrique_defaut' LIMIT 1");
        			$rows1001 = mysql_fetch_array($sql1001);
        			$id_rub_admin_defaut = $rows1001['id_rubrique'];
        		}
        	}
        }
/*
// pas d'acces_groupe pour cette version            
        // étape 3.2 : création des groupes pour le plugin acces_groupes				
        $_POST['ss_groupes_redac'] == 1 ? $ss_groupes_redac = 1 : $ss_groupes_redac = 0;
        $_POST['ss_groupes_admin'] == 1 ? $ss_groupes_admin = 1 : $ss_groupes_admin = 0;
        $_POST['ss_groupes_visit'] == 1 ? $ss_groupes_visit = 1 : $ss_groupes_visit = 0;
        if ($ss_groupes_redac == 1 OR $ss_groupes_admin == 1 OR $ss_groupes_visit == 1) {
           // si le plugin acces_groupes est activé
        	if ($plugin_accesgroupes == 1) {					 		 
        	    $Terr_acces_groupes = array();
        		$Tres_acces_groupes = array();
        		$Tgroupes_accesgroupes = array();
        		$Tres_vider_aceesgroupes = array();
        		$Terr_vider_aceesgroupes = array();
        				$date_grpe_ec = date("Y-m-j H:i:s");							 
        		$sql_sup = '';
        		$sql_liaison = " WHERE ";
        		$ss_groupes_admin != 1 ? $sql_sup .= $sql_liaison." LOWER(groupe) != '$groupe_admins'" : $sql_sup .= "";
        		$sql_sup != '' ? $sql_liaison = " AND " : $sql_liaison = " WHERE ";
        		$ss_groupes_visit != 1 ? $sql_sup .= $sql_liaison." LOWER(groupe) != '$groupe_visits'" : $sql_sup .= "";
        		$sql_sup != '' ? $sql_liaison = " AND " : $sql_liaison = " WHERE ";
        		$ss_groupes_redac != 1 ? $sql_sup .= $sql_liaison." LOWER(groupe) != '$groupe_redacs'" : $sql_sup .= "";
        //echo '<br>$ch_sql = '."SELECT ss_groupe FROM spip_tmp_csv2spip ".$sql_sup." GROUP BY ss_groupe";							 
        		$sql18= sql_query("SELECT ss_groupe FROM spip_tmp_csv2spip ".$sql_sup." GROUP BY ss_groupe");
        //echo '<br>mysql_error $sql18 = '.mysql_error();							 
        		while ($data18 = mysql_fetch_array($sql18)) {
        		  // créer les sous-groupes
        		    if ($data18['ss_groupe'] != '') {
        			    $grpe_ec = $data18['ss_groupe']; 				
        //echo '<br>$grpe_ec = _'.$grpe_ec.'_';
            			$sql17 = sql_query("SELECT id_grpacces FROM $Taccesgroupes_groupes WHERE nom = '$grpe_ec' LIMIT 1");
        //echo '<br>mysql_error $sql17 = '.mysql_error();											 
            		  // le groupe existe déja
        				if (mysql_num_rows($sql17) > 0) {
            			  // stocker l'id_grpacces du groupe dans $Tgrpes_accesgroupes[$nom_ss-grpe]
        					$data17 = mysql_fetch_array($sql17);
        					$Tgroupes_accesgroupes[$grpe_ec] = $data17['id_grpacces'];
        			      // si nécessaire vider le groupe de ses utilisateurs
        					if ($_POST['ss_grpes_reinitialiser'] == 1) {
        					    $id_grpacces_asupr = $data17['id_grpacces'];
        						sql_query("DELETE FROM $Taccesgroupes_auteurs WHERE id_grpacces = $id_grpacces_asupr");
        						if (mysql_error() != '') {
        						    $Terr_vider_accesgroupes[] = array('ss_groupe' => $grpe_ec, 'erreur' => mysql_error());
        						}
        						else {
        						    $Tres_vider_accesgroupes[] = $id_grpacces_asupr;
        						}
        					}
        					continue;
            			}
        				$desc_grpe_csv2spip = _T('csvspip:grpe_csv2spip');
            			sql_query("INSERT INTO $Taccesgroupes_groupes (id_grpacces, nom, description, actif, proprio, demande_acces) 
        							 VALUES ('', '$grpe_ec', '$desc_grpe_csv2spip', 1, 0, 0)" );
              			$id_grpacces_new = mysql_insert_id();
        				if (mysql_error() != '') {
              			    $Terr_acces_groupes[] = array('ss_groupe' => $grpe_ec, 'erreur' => mysql_error());
        					$err_total ++;
              			}
        				else {
        				  // stocker l'id_grpacces du groupe dans $Tgrpes_accesgroupes[$nom_ss-grpe]
        					$Tgroupes_accesgroupes[$grpe_ec] = $id_grpacces_new;
        					$Tres_acces_groupes[] = $grpe_ec;
        				}
        			}
        		}
        		echo "<br />"._T('csvspip:etape3.2')."<br />";
        		if (count($Terr_vider_accesgroupes) > 0 OR count($Terr_acces_groupes) > 0) {
          		    echo "<br><span class=\"Cerreur\">"._T('csvspip:err_etape3.2');
        		}
          		if (count($Terr_vider_accesgroupes) > 0) {
        	  	    foreach ($Terr_vider_accesgroupes as $Ver) {
        			    echo "<br />"._T('csvspip:err_vider_accesgroupes').$Ver['ss_groupe']._T('csvspip:erreur').$Ver['erreur'];
        			}
        		}
        		else {
        		    echo "<br />"._T('csvspip:ok_vider_accesgroupes').count($Tres_vider_accesgroupes)._T('csvspip:groupe')."<br />";
        		}
        		if (count($Terr_acces_groupes) > 0) {  
        		    echo "<br />";
        			foreach ($Terr_acces_groupes as $Ger) {
          			    echo "<br />"._T('csvspip:groupe_').$Ger['ss_groupe']._T('csvspip:erreur').$Ger['erreur'];
          			}
          			echo "</span>";
        	  	    $err_total ++;
        		}			
          		else {
          		    echo "<br />"._T('csvspip:ok_etape3.2_debut').count($Tres_acces_groupes)._T('csvspip:ok_etape3.2_fin')."<br>";
          		}
        		if (count($Terr_vider_accesgroupes) > 0 OR count($Terr_acces_groupes) > 0) {
          		    echo "</span>";
        		}
        	}  // fin if acces_groupes actif
        	else {   // plugin acces_groupes inactif et $_POST['acces_groupes'] == 1 (en principe pas possible...)
        	    echo "<br /><span class=\"Cerreur\">"._T('csvspip:abs_acces_groupes')."</span><br />"; 
        		$err_total ++;
        	}
        }
*/            
        echo fin_cadre_couleur(true);
        
        // étape 4 : intégration des rédacteurs, des visiteurs et des administrateurs							
        // redacteurs
        $Tres_nvx = array();
        $Terr_nvx = array();
        $Tres_maj = array();
        $Terr_maj = array();
        $Tres_eff = array();
        $Terr_eff = array();
        $Tres_poub = array();
        $Terr_poub = array();
        $TresR_ss_grpe = array();
        $TerrR_ss_grpe = array();
        $TerrR_eff_accesgroupes = array();
        
        // admins
        $TresA_nvx = array();
        $TerrA_nvx = array();
        $TresA_maj = array();
        $TerrA_maj = array();
        $TresA_eff = array();
        $TerrA_eff = array();
        $TresA_ss_grpe = array();
        $TerrA_ss_grpe = array();
        $TerrA_eff_accesgroupes = array();
        $TerrA_eff_rub_admins = array();
        
        // visiteurs
        $TresV_nvx = array();
        $TerrV_nvx = array();
        $TresV_maj = array();
        $TerrV_maj = array();
        $TresV_eff = array();
        $TerrV_eff = array();
        $TresV_ss_grpe = array();
        $TerrV_ss_grpe = array();
        $TerrV_eff_accesgroupes = array();
        
        // communs
        $Tres_maj_grpacces = array();
        $Terr_maj_grpacces = array();
        $Tres_maj_rub_admin = array();
        $Terr_maj_rub_admin = array();
        
        // LA boucle : gère 1 à 1 les utilisateurs de spip_tmp_csv2spip en fonction des options => TOUS !
        $sql157 = sql_query("SELECT * FROM spip_tmp_csv2spip");
	echo "<br>", sql_count($sql157), " entrees*****";
        while ($data157 = sql_fetch($sql157)) {
            if ($data157['pseudo_spip'] != '') {
        	    $nom = ucwords($data157['pseudo_spip']);
        //print '<br>pseudo_spip existe : $data157[pseudo_spip] = '.$data157['pseudo_spip'].' $nom = '.$nom;									
        	}
        	else {
        	    $nom = strtoupper($data157['nom']).' '.ucfirst($data157['prenom']);
        	}
        //print '<br>$nom = '.$nom.' $data157[nom] = '.$data157['nom'].' $data157[pseudo_spip] = _'.$data157['pseudo_spip'].'_';							 
        	$groupe = strtolower($data157['groupe']);
        	$ss_groupe = $data157['ss_groupe'];
        	$pass = $data157['mdp'];
        	$mel = $data157['mel'];
        	$login = $data157['nom'];
        	$login_minuscules = strtolower($login);
        	$groupe != $groupe_admins ? ($groupe != $groupe_visits ? $statut = '1comite' : $statut = '6forum') : $statut = '0minirezo';
        			 
        	$sql423 = sql_query("SELECT COUNT(*) AS nb_user FROM $Tauteurs WHERE LOWER(login) = '$login_minuscules' LIMIT 1");
        	$data423 = mysql_fetch_array($sql423);							 
        	$nb_user = $data423['nb_user'];	
        // 4.1 : l'utilisateur n'est pas inscrit dans la base spip_auteurs
        	if ($nb_user < 1) {
        	    $pass = csv2spip_crypt_pass($pass);
        		sql_query("INSERT INTO $Tauteurs (id_auteur, nom, email, login, pass, statut) VALUES ('', '$nom', '$mel', '$login', '$pass', '$statut')");
        		$id_spip = mysql_insert_id();
        		if (mysql_error() == '') {
//        		    include_spip("inc/indexation");
//        		    marquer_indexer('spip_auteurs', $id_auteur);
                  // Mettre a jour les fichiers .htpasswd et .htpasswd-admin
                    include_spip("inc/acces");
                    ecrire_acces();
        	  // insertion de l'id_spip dans la base tmp
        		    sql_query("UPDATE spip_tmp_csv2spip SET id_spip = '$id_spip' WHERE LOWER(nom) = '$login_minuscules' LIMIT 1");
        		    $groupe != $groupe_admins ? ($groupe != $groupe_visits ? $Tres_nvx[] = $login: $TresV_nvx[] = $login) : $TresA_nvx[] = $login;
        		}
        		else {
        	        $groupe != $groupe_admins ? ($groupe != $groupe_visits ? $Terr_nvx[] = array('login' => $login, 'erreur' => mysql_error()) : $TerrV_nvx[] = array('login' => $login, 'erreur' => mysql_error()) ) :  $TerrA_nvx[] = array('login' => $login, 'erreur' => mysql_error());
        	    }
            }
            else {
        // 4.2 : l'utilisateur est déja inscrit dans la base spip_auteurs
             // trouver l'id_auteur spip
        	    $sql44 = sql_query("SELECT id_auteur FROM $Tauteurs WHERE LOWER(login) = '$login_minuscules' LIMIT 1");
        	    if (mysql_num_rows($sql44) > 0) {
        	        $result44 = mysql_fetch_array($sql44);
        		    $id_spip = $result44['id_auteur'];
        			sql_query("UPDATE spip_tmp_csv2spip SET id_spip = '$id_spip' WHERE LOWER(nom) = '$login_minuscules' LIMIT 1");											 
        	    } 
          // faut il faire la maj des existants ?
        		if ($_POST['maj_gene'] == 1) {
        			  // 4.2.1 faire la maj des infos perso si nécessaire
        		    if ($_POST['maj_mdp'] == 1) {
          		        $pass = csv2spip_crypt_pass($pass);
          				sql_query("UPDATE $Tauteurs SET nom = '$nom', email = '$mel', statut = '$statut', pass = '$pass', alea_actuel = '' WHERE id_auteur = $id_spip LIMIT 1");
          				if (mysql_error() == '') {
            			    $groupe != $groupe_admins ? ($groupe != $groupe_visits ? $Tres_maj[] = $login : $TresV_maj[] = $login) : $TresA_maj[] = $login;
              			}
              			else {
              			    $groupe != $groupe_admins ? ($groupe != $groupe_visits ? $Terr_maj[] = array('login' => $login, 'erreur' => mysql_error()) : $TerrV_maj[] = array('login' => $login, 'erreur' => mysql_error())) : $TerrA_maj[] = array('login' => $login, 'erreur' => mysql_error());
              			}
          			}
/*
// pas d'acces_groupe pour cette version    
        	  // 4.2.2 réinitialisation des groupes acces_groupes si nécessaire
        		    if ( ($_POST['maj_grpes_redac'] == 1 AND $statut == '1comite') 
        		 	  OR ($_POST['maj_grpes_admin'] == 1 AND $statut == '0minirezo')
        		 	  OR ($_POST['maj_grpes_visit'] == 1 AND $statut == '6forum')) {
        		        sql_query("DELETE FROM $Taccesgroupes_auteurs WHERE id_auteur = $id_spip");
        			    if (mysql_error() == '') {
            			    $Tres_maj_grpacces[] = $login;
              			}
              			else {
              			    $Terr_maj_grpacces[] = array('login' => $login, 'erreur' => mysql_error());
              			}
        		    }
*/                    
        	  // 4.2.3 suppression des droits sur les rubriques administrées si nécessaire
        		    if ($_POST['maj_rub_adm'] == 1 AND $statut == '0minirezo') {
        		        sql_query("DELETE FROM $Tauteurs_rubriques WHERE id_auteur = $id_spip");
        			    if (mysql_error() == '') {
            			    $Tres_maj_rub_admin[] = $login;
              			}
              			else {
              			    $Terr_maj_rub_admin[] = array('login' => $login, 'erreur' => mysql_error());
              			}
        		    }
        	    }
            }
        
/*
// pas d'acces_groupe pour cette version            								 
        // 4.3 : intégrer l'auteur dans son ss-groupe acces_groupes si nécessaire 
        	if (($ss_groupes_redac == 1 AND $statut == '1comite') OR ($ss_groupes_admin == 1 AND $statut == '0minirezo') OR ($ss_groupes_visit == 1 AND $statut == '6forum')) {
        	    if ($id_grpacces_ec = $Tgroupes_accesgroupes[$ss_groupe]) {
        		    $sql55 = sql_query("SELECT COUNT(*) AS existe_auteur FROM $Taccesgroupes_auteurs WHERE id_grpacces = $id_grpacces_ec AND id_auteur = $id_spip LIMIT 1");
        		    $result55 = mysql_fetch_array($sql55);
        	  // l'utilisateur n'existe pas dans la table _accesgroupes_auteurs
        		    if ($result55['existe_auteur'] == 0) {
        		        sql_query("INSERT INTO $Taccesgroupes_auteurs (id_grpacces, id_auteur, dde_acces, proprio)
        							VALUES ($id_grpacces_ec, $id_spip, 0, 0)");
        				if (mysql_error() == '') {
        				    $groupe != $groupe_admins ? ($groupe != $groupe_visits ? $TresR_ss_grpe[] = $login : $TresV_ss_grpe[] = $login) : $TresA_ss_grpe[] = $login;
        				}
        				else {
        				    $groupe != $groupe_admins ? ($groupe != $groupe_visits ? $TerrR_ss_grpe[] = array('login' => $login, 'erreur' => mysql_error()) : $TerrV_ss_grpe[] = array('login' => $login, 'erreur' => mysql_error()) ) :  $TerrA_ss_grpe[] = array('login' => $login, 'erreur' => mysql_error());
        				}
        		    }
        		}
        		else {
        		    $groupe != $groupe_admins ? ($groupe != $groupe_visits ? $Terr_ss_grpe[] = array('login' => $login, 'erreur' => _T('csvspip:err_integ_accesgroupes').$ss_groupe) : $TerrV_ss_grpe[] = array('login' => $login, 'erreur' => _T('csvspip:err_integ_accesgroupes').$ss_groupe) ) :  $TerrA_ss_grpe[] = array('login' => $login, 'erreur' => _T('csvspip:err_integ_accesgroupes').$ss_groupe);
        		}
        	}
*/         
        }  // fin du while traitant les comptes 1 à 1
        
    // 4.4 : gestion des suppressions
    // VERSION 2.3 de effacer les absents
        $ch_maj = 0;
        $eff_absv = 0;
        $eff_absr = 0;
        $eff_absa = 0;
    	if ($_POST['eff_visit'] == 1) {
    //					 $ch_maj = 1;
            $eff_absv = 1;
        }
        if ($_POST['eff_redac'] == 1) {
            $ch_maj = 1;
    	    $eff_absr = 1;
        }
        if ($_POST['eff_admin'] == 1) {
            $ch_maj = 1;
    	    $eff_absa = 1;
        }

    // paramétrage auteur et dossier d'archive
    	if ($ch_maj !== 0) {
    		  // si auteurs supprimés (pas de poubelle), récupérer l'id du rédacteur affecté aux archives + si nécessaire, créer cet auteur (groupe = poubelle)
      	    if ($_POST['auteurs_poubelle'] != 1) {
      		    $nom_auteur_archives = $_POST['nom_auteur_archives'];
      			$sql615 = sql_query("SELECT id_auteur FROM $Tauteurs WHERE login = '$nom_auteur_archives' LIMIT 1");
      			if (mysql_num_rows($sql615) > 0) {
      			    $data615 = mysql_fetch_array($sql615);
      				$id_auteur_archives = $data615['id_auteur'];
      			}
      			else {
      			    sql_query("INSERT INTO $Tauteurs (id_auteur, nom, login, pass, statut) VALUES ('', '$nom_auteur_archives', '$nom_auteur_archives', '$nom_auteur_archives', '5poubelle')");
      				$id_auteur_archives = mysql_insert_id();
      			}
      			$nom_rub_archivesR = $nom_auteur_archives;
      			$id_rub_parent_archivesA = $nom_auteur_archives;
      			$id_rub_parent_archivesR = $id_auteur_archives;
      			$nom_rub_archivesA = $nom_auteur_archives;
      			$id_auteur_archivesA = $id_auteur_archives;
      			$nom_auteur_archivesR = $nom_auteur_archives;
      			$id_auteur_archivesR = $id_auteur_archives;
    				
    		// si archivage, récup de l'id de la rubrique archive + si nécessaire, créer la rubrique				 		
    			if ($_POST['supprimer_articles'] != 1 AND $_POST['archivage'] != 0) {
    			    $supprimer_articlesr = 0;
    				$supprimer_articlesa = 0;
    				$archivager =1;
    				$archivagea = 1;
    					 
    				$nom_rub_archives = $_POST['rub_archivage'];
    			// $_POST['rub_parent_archivage'] de la forme : "id_rubrique,id_secteur"
    				$Tids_parent_rub_archives = explode(',', $_POST['rub_parent_archivage']);
    				$id_rub_parent_archives = $Tids_parent_rub_archives[0];
    				$id_sect_parent_archives = $Tids_parent_rub_archives[1];
    				$date_rub_archives = date("Y-m-j H:i:s");
    				$sql613 = sql_query("SELECT id_rubrique, id_secteur FROM spip_rubriques WHERE titre = '$nom_rub_archives' AND id_parent = '$id_rub_parent_archives' LIMIT 1");
    				if (mysql_num_rows($sql613) > 0) {
    				    $data613 = mysql_fetch_array($sql613);
    					$id_rub_archives = $data613['id_rubrique'];
    				}
    				else {
    				    sql_query("INSERT INTO spip_rubriques (id_rubrique, id_parent, titre, id_secteur, statut, date) VALUES ('', '$id_rub_parent_archives', '$nom_rub_archives', '$id_sect_parent_archives', 'publie', '$date_rub_archives')" );
    					$id_rub_archives = mysql_insert_id();
    				}
      			}
      		}
        }
    			        						
      // 4.4.1 : traitement des visiteurs actuels de la base spip_auteurs => si effacer les absV = OK
        if ($eff_absv == 1) {
    	    $sql1471 = sql_query("SELECT COUNT(*) AS nb_redacsV FROM $Tauteurs WHERE statut = '6forum'");
        	$data1471 = mysql_fetch_array($sql1471);
        	if ($data1471['nb_redacsV'] > 0) {
      		  // pas de poubelle pour les visiteurs => suppression puisque pas d'articles
        		$sql1591 = sql_query("SELECT id_auteur, login FROM $Tauteurs WHERE statut = '6forum'");
      			while ($data1591 = mysql_fetch_array($sql1591)) {
        		    $login_sp = strtolower($data1591['login']);
      				$id_auteur_ec = $data1591['id_auteur'];
        			$sql4561 = sql_query("SELECT COUNT(*) AS nb FROM spip_tmp_csv2spip WHERE LOWER(nom) = '$login_sp' LIMIT 1");
        			$data4561 = mysql_fetch_array($sql4561);
             // l'utilisateur n'est pas dans le fichier CSV importé => le supprimer
          			if ($data4561['nb'] == 0) {
      				  // traitement des visiteurs à effacer												
      					sql_query("DELETE FROM $Tauteurs WHERE id_auteur = '$id_auteur_ec' AND statut = '6forum' LIMIT 1");
      					if (mysql_error() == 0) {
          				    $TresV_eff[] = $login;
/*
// pas d'acces_groupe pour cette version            								                                 
    				  // effacer toutes les références à ce visiteur dans acces_groupes
    					    sql_query("DELETE FROM $Taccesgroupes_auteurs WHERE id_auteur = $id_auteur_ec");
    					    if (mysql_error() != '') {
    					        $TerrV_eff_accesgroupes[] = array('id_auteur' => $id_auteur_ec, 'erreur' => mysql_error());
    					    }
*/                            
            			}
            			else {
            			    $TerrV_eff[] = array('id_auteur' => $id_auteur_ec, 'erreur' => mysql_error());
            			}
      				}
      			}
              // optimisation de la table après les effacements
      			sql_query("OPTIMIZE TABLE $Tauteurs, $Taccesgroupes_auteurs");
      		}	
        }
        
      // 4.4.2 : traitement des rédacteurs actuels de la base spip_auteurs => si effacer les absents redac = OK
        if ($eff_absr == 1) {
            $sql147 = sql_query("SELECT COUNT(*) AS nb_redacsR FROM $Tauteurs WHERE statut = '1comite'");
          	$data147 = mysql_fetch_array($sql147);
          	if ($data147['nb_redacsR'] > 0) {
        	  // si archivage, récup de l'id de la rubrique archive + si nécessaire, créer la rubrique				 		
        		if ($supprimer_articlesr != 1 AND $archivager != 0) {
        		    $nom_rub_archivesR = $rub_archivager;
        			$sql613 = sql_query("SELECT id_rubrique, id_secteur FROM spip_rubriques WHERE titre = '$nom_rub_archivesR' AND id_parent = '$id_rub_parent_archivesR' LIMIT 1");
        			if (mysql_num_rows($sql613) > 0) {
        			    $data613 = mysql_fetch_array($sql613);
        				$id_rub_archivesR = $data613['id_rubrique'];
        			}
        			else {
        			    sql_query("INSERT INTO spip_rubriques (id_rubrique, id_parent, titre, id_secteur, statut, date) VALUES ('', '$id_rub_parent_archivesR', '$nom_rub_archivesR', '$id_sect_parent_archivesR', 'publie', '$date_rub_archivesR')" );
    				    $id_rub_archivesR = mysql_insert_id();
        			}
        		}
          		$sql159 = sql_query("SELECT id_auteur, login FROM $Tauteurs WHERE statut = '1comite' AND bio != 'archive'");
          		$cteur_articles_deplacesR = 0;
        		$cteur_articles_supprimesR = 0;
        		$cteur_articles_modif_auteurR = 0;
        		while ($data159 = mysql_fetch_array($sql159)) {
          		    $login_sp = strtolower($data159['login']);
        			$id_auteur_ec = $data159['id_auteur'];
          			$sql456 = sql_query("SELECT COUNT(*) AS nb FROM spip_tmp_csv2spip WHERE nom = '$login_sp' LIMIT 1");
          			$data456 = mysql_fetch_array($sql456);
                  // l'utilisateur n'est pas dans le fichier CSV importé => le supprimer
            		if ($data456['nb'] == 0) {
        			  // traitement éventuel des articles de l'auteur à supprimer
        			    $sql757 = sql_query("SELECT COUNT(*) AS nb_articles_auteur FROM $Tauteurs_articles WHERE id_auteur = '$id_auteur_ec'");
        				$data757 = mysql_fetch_array($sql757);
    //print '<br><br>id_auteur = '.$id_auteur_ec;
    //print '<br>nb_articles_auteur = '.$data757['nb_articles_auteur'];
    //print '<br>$supprimer_articlesr = '.$supprimer_articlesr;
    //print '<br>$archivager = '.$archivager;
        				if ($data757['nb_articles_auteur'] > 0) {
            			    if ($supprimer_articlesr != 1) {
                			    if ($archivager != 0) {
        						    $sql612 = sql_query("SELECT id_article FROM $Tauteurs_articles WHERE id_auteur = $id_auteur_ec");
                					if (mysql_num_rows($sql612) > 0) {
    //print '<br>départ UPDATE';
                					    while ($data612 = mysql_fetch_array($sql612)) {
                						    $id_article_ec = $data612['id_article'];
        									sql_query("UPDATE $Tarticles SET id_rubrique = '$id_rub_archivesR', id_secteur = '$id_sect_parent_archivesR' WHERE id_article = '$id_article_ec' LIMIT 1");
        									$cteur_articles_deplacesR ++;
                						}
                					} 
                   					if ($auteurs_poubeller != 1) {
                  					    sql_query("UPDATE $Tauteurs_articles SET id_auteur = '$id_auteur_archivesR' WHERE id_auteur = '$id_auteur_ec'");
                  					}	   														
                				}
            				}
            				else {
            				    $sql756 = sql_query("SELECT id_article FROM $Tauteurs_articles WHERE id_auteur = '$id_auteur_ec'");
    //print '<br>départ DELETE';
            					while ($data756 = mysql_fetch_array($sql756)) {
            					    $id_article_a_effac = $data756['id_article'];
            						sql_query("DELETE FROM $Tarticles WHERE id_article = '$id_article_a_effac' LIMIT 1");
        							$cteur_articles_supprimesR ++;
            					}
            					sql_query("DELETE FROM $Tauteurs_articles WHERE id_auteur = '$id_auteur_ec'");
            				}
        				}
        			  // traitement des auteurs à effacer												
        				if ($auteurs_poubeller != 1) {
        				    sql_query("DELETE FROM $Tauteurs WHERE id_auteur = '$id_auteur_ec' AND statut = '1comite' LIMIT 1");
            				if (mysql_error() == 0) {
                			    $TresR_eff[] = $login;
/*
// pas d'acces_groupe pour cette version            								                                 
    						  // effacer toutes les références à ce visiteur dans acces_groupes
    							sql_query("DELETE FROM $Taccesgroupes_auteurs WHERE id_auteur = $id_auteur_ec");
    							if (mysql_error() != '') {
    							    $TerrR_eff_accesgroupes[] = array('id_auteur' => $id_auteur_ec, 'erreur' => mysql_error());
    							}
*/                                
                  			}
    						else {
                  			    $TerrR_eff[] = array('id_auteur' => $id_auteur_ec, 'erreur' => mysql_error());
                  			}
        				}
        				else {
        				    sql_query("UPDATE $Tauteurs SET statut = '5poubelle' WHERE id_auteur = '$id_auteur_ec' LIMIT 1");
              				if (mysql_error() == 0) {
                  			    $TresR_poub[] = $id_auteur_ec;
                    		}
                    		else {
                    		    $TerrR_poub[] = array('id_auteur' => $id_auteur_ec, 'erreur' => mysql_error());
                    		}
        				}
        			}
        		}
              // optimisation de la table après les effacements
        		sql_query("OPTIMIZE TABLE $Tauteurs, $Tarticles, $Tauteurs_articles, $Taccesgroupes_auteurs");
        	}		
        }
     // 4.4.3 : traitement des administrateurs restreints actuels de la base spip_auteurs => si effacer les absA = OK
        if ($eff_absa == 1) {
    	    $sql1473 = sql_query("SELECT COUNT(*) AS nb_redacsA FROM $Tauteurs
    								 LEFT JOIN $Tauteurs_rubriques
    								 ON $Tauteurs_rubriques.id_auteur = $Tauteurs.id_auteur
    								 WHERE statut = '0minirezo'");
    //echo '<br>mysql_error 1473 = '.mysql_error();
      		$data1473 = mysql_fetch_array($sql1473);
      		if ($data1473['nb_redacsA'] > 0) {
      		    $sql1593 = sql_query("SELECT Tauteurs.id_auteur, Tauteurs.login FROM $Tauteurs AS Tauteurs, $Tauteurs_rubriques AS Tauteurs_rubriques WHERE statut = '0minirezo' AND Tauteurs.id_auteur = Tauteurs_rubriques.id_auteur");
      			$cteur_articles_deplacesA = 0;
    			$cteur_articles_supprimesA = 0;
    			$cteur_articles_modif_auteurA = 0;
    			while ($data1593 = mysql_fetch_array($sql1593)) {
      			    $login_sp = strtolower($data1593['login']);
    				$id_auteur_ec = $data1593['id_auteur'];
      				$sql4563 = sql_query("SELECT COUNT(*) AS nbA FROM spip_tmp_csv2spip WHERE nom = '$login_sp' LIMIT 1");
      				$data4563 = mysql_fetch_array($sql4563);
                  // l'utilisateur n'est pas dans le fichier CSV importé => le supprimer
        			if ($data4563['nbA'] == 0) {
    				  // traitement éventuel des articles de l'admin à supprimer
    					$sql7573 = sql_query("SELECT COUNT(*) AS nb_articles_auteur FROM $Tauteurs_articles WHERE id_auteur = '$id_auteur_ec'");
    					$data7573 = mysql_fetch_array($sql7573);
    					if ($data7573['nb_articles_auteur'] > 0) {
        				    if ($supprimer_articlesa != 1) {
            				    if ($archivagea != 0) {
    							    $sql6123 = sql_query("SELECT id_article FROM $Tauteurs_articles WHERE id_auteur = '$id_auteur_ec'");
            						    if (mysql_num_rows($sql6123) > 0) {
            							    while ($data6123 = mysql_fetch_array($sql6123)) {
            								    $id_article_ec = $data6123['id_article'];
    											sql_query("UPDATE $Tarticles SET id_rubrique = '$id_rub_archivesA', id_secteur = '$id_sect_parent_archivesA' WHERE id_article = '$id_article_ec' LIMIT 1");
    											$cteur_articles_deplacesA ++;
            								}
            							} 
               							if ($auteurs_poubellea != 1) {
              							    sql_query("UPDATE $Tauteurs_articles SET id_auteur = '$id_auteur_archivesA' WHERE id_auteur = '$id_auteur_ec'");
              							}	   														
            						}
        						}
        						else {
        						    $sql7563 = sql_query("SELECT id_article FROM $Tauteurs_articles WHERE id_auteur = '$id_auteur_ec'");
        							while ($data7563 = mysql_fetch_array($sql7563)) {
        							    $id_article_a_effac = $data7563['id_article'];
        								sql_query("DELETE FROM $Tarticles WHERE id_article = '$id_article_a_effac' LIMIT 1");
    									$cteur_articles_supprimesA ++;
        							}
        							sql_query("DELETE FROM $Tauteurs_articles WHERE id_auteur = '$id_auteur_ec'");
        						}
    						}
    				      // traitement des admins à effacer												
    						if ($auteurs_poubellea != 1) {
    						    sql_query("DELETE FROM $Tauteurs WHERE id_auteur = '$id_auteur_ec' AND statut = '0minirezo' LIMIT 1");
        						if (mysql_error() == 0) {
            					    $TresA_eff[] = $login;
/*
// pas d'acces_groupe pour cette version            								 
    						      // effacer toutes les références à ce visiteur dans acces_groupes
    							    sql_query("DELETE FROM $Taccesgroupes_auteurs WHERE id_auteur = $id_auteur_ec");
    							    if (mysql_error() != '') {
    							        $TerrA_eff_accesgroupes[] = array('id_auteur' => $id_auteur_ec, 'erreur' => mysql_error());
    							    }
*/                                    
              					  // virer l'administation de toutes les rubriques pour cet admin
    							    sql_query("DELETE FROM $Tauteurs_rubriques WHERE id_auteur = $id_auteur_ec");
    							    if (mysql_error() != '') {
    							        $TerrA_eff_rub_admins[] = array('id_auteur' => $id_auteur_ec, 'erreur' => mysql_error());
    							    }
              					}
              					else {
    						        $TerrA_eff[] = array('id_auteur' => $id_auteur_ec, 'erreur' => mysql_error());
              					}
    						}
    						else {
    						    sql_query("UPDATE $Tauteurs SET statut = '5poubelle' WHERE id_auteur = '$id_auteur_ec' LIMIT 1");
          						if (mysql_error() == 0) {
              					    $TresA_poub[] = $id_auteur_ec;
                				}
                				else {
                				    $TerrA_poub[] = array('id_auteur' => $id_auteur_ec, 'erreur' => mysql_error());
                				}
    						}
    					}
    				}
                  // optimisation de la table après les effacements
    				sql_query("OPTIMIZE TABLE $Tauteurs, $Tarticles, $Tauteurs_articles, $Taccesgroupes_auteurs");
    			}
            }   //   fin effacer les abs (4.4)  V 2.3
        
          // résultats étape 4
            echo debut_cadre_couleur(_LOGO_CSV2SPIP, true, "", _T('csvspip:titre_etape4'));
            echo "<br>"._T('csvspip:etape4.1')."<br>";
            if (count($TerrV_nvx) > 0) {		
        	    echo "<span class=\"Cerreur\">"._T('csvspip:err_visit');
        	    foreach ($TerrV_nvx as $Ven) { 
        		    echo _T('csvspip:utilisateur').$Ven['login']._T('csvspip: erreur').$Ven['erreur']."<br>";
        	    }
        	    echo "</span>";
        	    $err_total ++;
            }
            else {
        	    echo "<br>"._T('csvspip:creation').count($TresV_nvx)._T('csvspip:comptes_visit_ok')."<br>";					 			
            }
            if (count($Terr_nvx) > 0) {		
        	    echo "<span class=\"Cerreur\">"._T('csvspip:err_redac');
        	    foreach ($Terr_nvx as $en) { 
        	 	    echo _T('csvspip:utilisateur').$en['login']._T('csvspip:erreur').$en['erreur']."<br>";
        	    }					
        	    echo "</span>";
                $err_total ++;
            }
            else {
        	    echo "<br>"._T('csvspip:creation').count($Tres_nvx)._T('csvspip:comptes_redac_ok')."<br>";					 
            }
        
            if (count($TerrA_nvx) > 0) {		
        	    echo "<span class=\"Cerreur\">"._T('csvspip:err_admin');
        	    foreach ($TerrA_nvx as $Pen) { 
        		    echo _T('csvspip:utilisateur').$Pen['login']._T('csvspip: erreur').$Pen['erreur']."<br>";
        	    }
        	    echo "</span>";
        	    $err_total ++;
            }
            else {
        	    echo "<br>"._T('csvspip:creation').count($TresA_nvx)._T('csvspip:comptes_admin_ok')."<br>";					 			
            }
        
          // 4.2 résultats maj des existants
            if ($_POST['maj_gene'] == 1) {
        	    echo "<br>"._T('csvspip:etape4.2')."<br>";
        		if ($_POST['maj_mdp'] == 1) { 					
              	    echo "<br>"._T('csvspip:etape4.2.1')."<br>";
        			if (count($TerrV_maj) > 0) {
            		    echo "<span class=\"Cerreur\">"._T('csvspip:err_visit');
              			foreach ($TerrV_maj as $Vem) { 
            	 		    echo _T('csvspip:visit').$Vem['login']._T('csvspip: erreur').$Vem['erreur']."<br>";
            			}		
            			echo "</span>";
            		 	$err_total ++;
            		}
            		else {
            		    echo "<br />"._T('csvspip:ok_etape4.2.1').count($TresA_maj)._T('csvspip:comptes_visit_ok')."<br>";
            		}  					
        		 	if (count($Terr_maj) > 0) {		
        			    echo "<span class=\"Cerreur\">"._T('csvspip:err_redac');
        				foreach ($Terr_maj as $em) { 
        				    echo _T('csvspip:redac').$em['login']._T('csvspip: erreur').$em['erreur']."<br>";
        				}		 
        				echo "</span>";
        				$err_total ++;
            		}
            		else {
            		    echo "<br>"._T('csvspip:ok_etape4.2.1').count($Tres_maj)._T('csvspip:comptes_redac_ok')."<br>";							
            		} 
              		if (count($TerrA_maj) > 0) {
            		    echo "<span class=\"Cerreur\">"._T('csvspip:err_admin');
              			foreach ($TerrA_maj as $Pem) { 
            	 		    echo _T('csvspip:admin').$Pem['login']._T('csvspip: erreur').$Pem['erreur']."<br>";
            			}		
            			echo "</span>";
            		 	$err_total ++;
            		}
            		else {
            		    echo "<br />"._T('csvspip:ok_etape4.2.1').count($TresA_maj)._T('csvspip:comptes_admin_ok')."<br>";
            		}  					
        		}
        		if ($_POST['maj_grpes_redac'] == 1 OR $_POST['maj_grpes_admin'] == 1 OR $_POST['maj_grpes_visit'] == 1) {
        		    echo "<br>"._T('csvspip:etape4.2.2')."<br>";
        			if (count($Terr_maj_grpacces) > 0) {
        			    echo "<span class=\"Cerreur\">"._T('csvspip:err_maj_grpacces');
              			foreach ($Terr_maj_grpacces as $Peg) { 
            	 		    echo _T('csvspip:utilisateur').$Peg['login']._T('csvspip: erreur').$Peg['erreur']."<br>";
            			}		
            			echo "</span>";
            		 	$err_total ++;
        			}
        			else {
        			    echo "<br />"._T('csvspip:ok_maj_grpacces').count($Tres_maj_grpacces)._T('csvspip:utilisateurs')."<br>";
        			}
        		}
        		if ($_POST['maj_rub_adm'] == 1) {
        		    echo "<br>"._T('csvspip:etape4.2.3')."<br>";
        			if (count($Terr_maj_rub_admin) > 0) {
        			    echo "<span class=\"Cerreur\">"._T('csvspip:err_maj_rub_adm');
              			foreach ($Terr_maj_rub_admin as $Pera) { 
            	 		    echo _T('csvspip:utilisateur').$Pera['login']._T('csvspip: erreur').$Pera['erreur']."<br>";
            			}		
            			echo "</span>";
            		 	$err_total ++;
        		    }
        			else {
        			    echo "<br />"._T('csvspip:ok_maj_rub_adm').count($Tres_maj_rub_admin)._T('csvspip:utilisateurs')."<br>";
        			}
        		}
            }
/*    
// pas d'acces_groupe pour cette version    
          // 4.3 résultats intégration des utilisateurs dans les groupes acces_groupes
            if ($_POST['ss_groupes_redac'] == 1 OR $_POST['ss_groupes_admin'] == 1 OR $_POST['ss_groupes_visit'] == 1) {
        	    echo "<br />"._T('csvspip:etape4.3')."<br>";
            }
*/        
          // 4.4 résultats effacer les absents
            if ($eff_absv == 1 OR $eff_absr == 1 OR $eff_absa == 1) {
        	    echo "<br />"._T('csvspip:etape4.4')."<br>";
            }
          
          // résultats effacer les visiteurs
            if ($eff_absv == 1) {  					
        	    echo "<br />"._T('csvspip:etape4.4.1')."<br>";
        		if (count($TerrV_eff) > 0 OR count($TerrV_poub) > 0) {	
        	 	    echo "<span class=\"Cerreur\">"._T('csvspip:err_visit');
        		    foreach ($TerrV_eff as $Vee) {
        			    echo _T('csvspip:visit').$Vee['login']._T('csvspip: erreur').$Vee['erreur'];
        			}	
        			$err_total ++;
        	    }
        	    else { 
        	 	    echo "<br />"._T('csvspip:suppression_debut').count($TresV_eff)._T('csvspip:comptes_visit_ok')."<br>";
        	    }
            }  					
        
          // résultats effacer les redacteurs
            if ($eff_absr == 1) { 
        	    echo "<br />"._T('csvspip:etape4.4.2')."<br>";
         	    if (count($TerrR_eff) > 0 OR count($TerrR_poub) >0) {
        	 	    echo "<span class=\"Cerreur\">"._T('csvspip:suppr_redac');
        			foreach ($TerrR_eff as $ee) { 
        			    echo '<br/>'._T('csvspip:redac').$ee['login']._T('csvspip: erreur').' '.$ee['erreur'];
        			}
        			echo "<span class=\"Cerreur\">"._T('csvspip:redac_poubelle');
        			foreach ($TerrR_poub as $ep) { 
        				echo '<br/>'._T('csvspip:redac').$ep['login']._T('csvspip: erreur').' '.$ep['erreur'];
        			}
         		    $err_total ++;
                }
        	    else { 
        	 	    echo "<br />"._T('csvspip:suppression_debut').count($TresR_eff)._T('csvspip:comptes_redac_ok')."<br>";
        			echo "<br />"._T('csvspip:poubelle_debut').count($TresR_poub)._T('csvspip:comptes_redac_ok')."<br>";
        	    }
        	    if ($archivager != 0) {
        	 	    echo "<br />"._T('csvspip:archivage_debut').$cteur_articles_deplacesR._T('csvspip:archivage_fin').$nom_rub_archivesR;
        	    }  
        	    if ($supprimer_articlesr == 1) {
        	 	    echo "<br />"._T('csvspip:suppression_debut').$cteur_articles_supprimesR._T('csvspip:suppression_fin')."<br>";
        	    }
            }  					
        
          // résultats effacer les admins
            if ($eff_absa == 1) { 			
        	    echo "<br />"._T('csvspip:etape4.4.3')."<br>";
         	    if (count($TerrA_eff) > 0 OR count($TerrA_poub) >0) {
        	 	    echo "<span class=\"Cerreur\">"._T('csvspip:suppr_redac');
        			foreach ($TerrA_eff as $Aee) { 
        			    echo "<br />"._T('csvspip:admin').$Aee['login']._T('csvspip: erreur').' '.$Aee['erreur'];
        			}
        			echo "<span class=\"Cerreur\">"._T('csvspip:redac_poubelle');
        			foreach ($TerrA_poub as $Aep) { 
        				echo "<br />"._T('csvspip:admin').$Aep['login']._T('csvspip: erreur').' '.$Aep['erreur'];
        			}
         		    $err_total ++;
                }
        	    else { 
        	 	    echo "<br />"._T('csvspip:suppression_debut').count($TresA_eff)._T('csvspip:comptes_admin_ok')."<br>";
        			echo "<br />"._T('csvspip:poubelle_debut').count($TresA_poub)._T('csvspip:comptes_admin_ok')."<br>";
        	    }
        	    if ($archivagea != 0) {
        	 	    echo "<br />"._T('csvspip:archivage_debut').$cteur_articles_deplacesA._T('csvspip:archivage_fin').$nom_rub_archivesA;
        	    }  
        	    if ($supprimer_articlesa == 1) {
        	 	    echo "<br />"._T('csvspip:suppression_debut').$cteur_articles_supprimesA._T('csvspip:suppression_fin')."<br>";
        	    }
/*    
// pas d'acces_groupe pour cette version    
        	    if (count ($TerrA_eff_accesgroupes) > 0) {
        	 	    echo "<span class=\"Cerreur\">"._T('csvspip:suppr_redac');
        			foreach ($TerrA_eff_accesgroupes as $Aec) { 
        			    echo _T('csvspip:err_eff_adm_accesgroupes').$Aec['login']._T('csvspip: erreur').$Aec['erreur'];
        			}
        			echo "</span>";
        			$err_total ++;
        	    }
*/                
        	    if (count ($TerrA_eff_rub_admins) > 0) {
        	 	    echo "<span class=\"Cerreur\">"._T('csvspip:suppr_redac');
        			foreach ($TerrA_eff_rub_admins as $Aer) { 
        			    echo _T('csvspip:err_eff_adm_rub').$Aer['login']._T('csvspip: erreur').$Aer['erreur'];
        			}
        			echo "</span>";
        			$err_total ++;					 		
        	    }
            }   // fin effacer les absents V 2.3
            echo fin_cadre_couleur(true);			
        
        // étape 5 : si nécessaire intégration des admins comme administrateurs restreints de la rubrique de leur sous-groupe
        //$id_rub_admin_defaut
    	    if ($groupe_admins != '-1') {
    		    $Terr_adm_rub = array();
    			$Tres_adm_rub = array();
    		    $sql54 = sql_query("SELECT ss_groupe, nom, id_spip FROM spip_tmp_csv2spip WHERE LOWER(groupe) = '$groupe_admins'");
    		    while ($data54 = mysql_fetch_array($sql54)) {
    		        $login_adm_ec = strtolower($data54['nom']);
    			    $id_adm_ec = $data54['id_spip'];
    			    if ($_POST['rub_prof'] == 1) {
    					if ($data54['ss_groupe'] != '') {
    				        $ss_grpe_ec = $data54['ss_groupe'];
      						$sql55 = sql_query("SELECT id_rubrique FROM spip_rubriques WHERE titre = '$ss_grpe_ec' LIMIT 1");
      						$data55 = mysql_fetch_array($sql55);
      						$id_rubrique_adm_ec = $data55['id_rubrique'];									 		
    				    }
    				    else {
    				        $id_rubrique_adm_ec = $id_rub_admin_defaut;
    					    $ss_grpe_ec = '';
    				    }
    			    }
    			    $sql57 = sql_query("SELECT COUNT(*) AS existe_adm_rub FROM $Tauteurs_rubriques WHERE id_auteur = '$id_adm_ec' AND id_rubrique = '$id_rubrique_adm_ec' LIMIT 1");
    			    $data57 = mysql_fetch_array($sql57);
    			    if ($data57['existe_adm_rub'] == 0) {
        //print '<br>rubrique $ss_grpe_ec = '.$ss_grpe_ec.' $id_rubrique_adm_ec = '.$id_rubrique_adm_ec.'$id_adm_ec = '.$id_adm_ec;								 
    			        sql_query("INSERT INTO $Tauteurs_rubriques (id_auteur, id_rubrique) VALUES ('$id_adm_ec', '$id_rubrique_adm_ec')");
    				    if (mysql_error() != '') {
    					    $Terr_adm_rub[] = array('login' => $login_adm_ec, 'rubrique' => $ss_grpe_ec, 'erreur' => mysql_error());
    					}
    					else {
    					    $Tres_adm_rub[] = $login_adm_ec;
    				    }
    			    }
    		    }
    		    echo debut_cadre_couleur(_LOGO_CSV2SPIP, true, "", _T('csvspip:titre_etape5'));
        //						 echo "<h2>"._T('csvspip:titre_etape5')."</h2>";
    	        if (count($Terr_adm_rub) > 0) {
    		        echo "<span class=\"Cerreur\">"._T('csvspip:err_admin_rubrique');
    			    foreach ($Terr_adm_rub as $ear) { 
    	 		        echo _T('csvspip:admin').$ear['login']._T('csvspip:rubrique_').$ear['rubrique']._T('csvspip: erreur').$ear['erreur']."<br>";
    			    }	
    			    echo "</span>";
    	 		    $err_total ++;
    		    }
    		    else {
    		        echo 'Attribution d\'une sous-rubrique pour '.count($Tres_adm_rub).' administrateurs restreints = OK<br>';
    		    }
    		    echo fin_cadre_couleur(true);
    	    }
         	
        // Etape 6 : si nécessaire création d'un article par rubrique 					
            if ($_POST['art_rub'] == 1 AND $_POST['rub_prof'] == 1) {
                $Terr_art_rub = array();
            	$Tres_art_rub = array();
            	$sql57 = sql_query("SELECT ss_groupe, nom FROM spip_tmp_csv2spip WHERE groupe = '$groupe_admins' AND ss_groupe != '' GROUP BY ss_groupe");
            	while ($data57 = mysql_fetch_array($sql57)) {
            	    $titre_rub_ec = $data57['ss_groupe'];
            		$sql58 = sql_query("SELECT id_rubrique, id_parent, id_secteur FROM spip_rubriques WHERE titre = '$titre_rub_ec' AND id_parent = '$rubrique_parent' LIMIT 1");
            		$data58 = mysql_fetch_array($sql58);
            		$id_rub_ec = $data58['id_rubrique'];
            		$id_parent_ec = $data58['id_parent'];
            		$id_sect_ec = $data58['id_secteur'];
            		$date_ec = date("Y-m-d H:i:s");
            		$titre_ec = 'Bienvenue dans la rubrique '.$titre_rub_ec;
            		$sql432 = sql_query("SELECT id_article FROM $Tarticles WHERE id_rubrique = '$id_rub_ec' AND titre = '$titre_ec' LIMIT 1");
            		if (mysql_num_rows($sql432) < 1) {
            		    $data432 = mysql_fetch_array($sql432);
            			sql_query("INSERT INTO $Tarticles (id_article, id_rubrique, id_secteur, titre, date, statut ) VALUES ('', '$id_rub_ec', '$id_sect_ec', '$titre_ec', '$date_ec', 'publie')");
            			if (mysql_error() != '') {
            			    $Terr_art_rub[] = array('rubrique' => $titre_rub_ec, 'erreur' => mysql_error());
            			}
            			else {
            			    $Tres_art_rub[] = $titre_rub_ec;
            			}
            		}
            	}
            	echo debut_cadre_couleur(_LOGO_CSV2SPIP, true, "", _T('csvspip:titre_etape6'));
            //						 echo "<h3>"._T('csvspip:titre_etape6')."</h3>";
            	if (count($Terr_art_rub) > 0) {
            	    echo "<span class=\"Cerreur\">"._T('csvspip:err_article');
            		foreach ($Terr_art_rub as $eart) { 
            	 	    echo _T('csvspip:rubrique_').$eart['rubrique']._T('csvspip:erreur').$eart['erreur']."<br>";
            		}	
            		echo "</span>";
            	 	$err_total ++;
            	}
            	else {
            	    echo _T('csvspip:ok_etape6_debut').count($Tres_art_rub)._T('csvspip:ok_etape6_fin')."<br>";
            	}
            	echo fin_cadre_couleur(true);
            }
}

?>
