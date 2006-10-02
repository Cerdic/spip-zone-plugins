<?php 
	/**
	 * saveauto : plugin de sauvegarde automatique de la base de données de SPIP
	 *
	 * Auteur : cy_altern d'après une contrib de Silicium (silicium@japanim.net)
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 *  
	 **/

function exec_saveauto_admin() {
	 			 include_spip("inc/presentation");

				
      // vérifier les droits
         global $connect_statut;
      	 global $connect_toutes_rubriques;
         if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {    
      		  debut_page(_T('titre'), "saveauto_admin", "plugin");
      		  echo _T('avis_non_acces_page');
      		  fin_page();
      		  exit;
      	 }

         $p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
         $pp = explode("/", end($p));
         define('_DIR_PLUGIN_SAVEAUTO',(_DIR_PLUGINS.$pp[0]));

         $page_save_conf = _DIR_PLUGIN_SAVEAUTO."/inc/saveauto_conf.php";

			// trouver la version en cours à partir de plugin.xml
				 $Tlecture_fich_plugin = file(_DIR_PLUGIN_SAVEAUTO.'/plugin.xml');
				 $stop_prochain = 0;
				 foreach ($Tlecture_fich_plugin as $ligne) {
								 if ($stop_prochain == 1) {
									  $version_script = $ligne;
									  break;
								 }
								 if (substr_count($ligne, '<version>') > 0) {
									  $stop_prochain = 1;
								 }
				 }
				 
			// traitement des données postées dans le formulaire de config : recréer le fichier saveauto_conf.php
				 if (isset($_POST['valide_config'])) {
				 		$T_params = array('base', 'destinataire_save', 'jours_obso', 'ecrire_succes', 'gz', 'structure', 'donnees', 'accepter', 'eviter', 'rep_bases', 'frequence_maj', 'prefixe_save', 'acces_redac');
						$a_ecrire = "<?php ";
						foreach ($T_params as $p) {
										$a_ecrire .= "\n $".$p." = ";
										if (($_POST[$p] != "true" AND $_POST[$p] != "false" AND ereg("[a-zA-Z]", $_POST[$p]) != false) OR $_POST[$p] == "") $a_ecrire .= '"';
										$a_ecrire .= $_POST[$p];
										if (($_POST[$p] != "true" AND $_POST[$p] != "false" AND ereg("[a-zA-Z]", $_POST[$p]) != false) OR $_POST[$p] == "") $a_ecrire .= '"';
										$a_ecrire .= ";";
						}
						$a_ecrire .= "\n ?>";
						if ($fconf = @fopen($page_save_conf, "wt")) {
    						fwrite($fconf, $a_ecrire);
    						fclose($fconf);
						}
						else {
								 echo '<font color=red><strong>'._T('saveauto:erreur_ecrire_conf').'</strong></font>';
						}
				 }
				 include($page_save_conf);
				 
         debut_page(_T('saveauto:saveauto'));
         echo "<br />";
         gros_titre(_T('saveauto:config_saveauto'));
         debut_gauche();
         debut_boite_info();
         echo "<strong>"._T('saveauto:plugin_saveauto')."</strong><br /><br />";
				 echo "\r\n"._T('saveauto:help_titre');
				 echo "<br /><br /><strong>"._T('saveauto:version')."</strong>".$version_script;
         fin_boite_info();
				 
         // Lister des fichiers contenus
				 debut_raccourcis();
				 echo "\r\n<table class='arial2' style='border: 1px solid #aaa; width:100%;'>\n";
         echo "\r\n<tr style='background-color: #fff;'><th>"._T('saveauto:sauvegardes_faites')."</th></tr>";
         $entree = array();
				 $rep_bases_conf = $rep_bases;
				 $rep_bases = _DIR_RACINE.$rep_bases;
         if ($myDirectory = opendir($rep_bases)) {
             while($entryName = readdir($myDirectory)) {
                //uniquement les fichiers du type : prefixe_nom_de_la_base
                if (substr($entryName, 0, strlen($prefixe_save . $base)) == $prefixe_save . $base) $entree[] = $entryName;
             }
             closedir($myDirectory);
             //trie dans l'ordre décroissant les sauvegardes
             rsort($entree);
             for ($i=0; $i<count($entree); $i++) {
                echo "<tr style='background-color: #eee;'><td class='verdana11' style='border-top: 1px solid #ccc;'>".$entree[$i];
                $temps = filemtime($rep_bases . $entree[$i]);
                $jour = date("j", $temps); //format numerique : 1->31
                $annee = date("Y", $temps); //format numerique : 4 chiffres
                $mois = date("m", $temps);
                $heure = date("H", $temps);
                $minutes = date("i", $temps);
                $date = "$jour/$mois/$annee : $heure h".$minutes;
                echo "<br /><span style=\"font-size: 11px;\">(".$date." | ".taille_en_octets(filesize($rep_bases.$entree[$i])).")</span></td></tr>\n";
             }
						 echo "</table>";
				 }
				 else {
				 			echo _T('saveauto:repertoire').$rep_bases._T('saveauto:repertoire_absent')."<br />";
				 }
				 fin_raccourcis();				 
				 
         debut_droite();
         echo "\r\n<form action=\"$PHP_SELF?exec=saveauto_admin\" name=\"frm_config\" method=\"post\">";
         debut_cadre_trait_couleur("plugin-24.gif", false, "", _T('saveauto:options_config'));       
				 
				 debut_cadre_couleur();
         echo "<strong>"._T('saveauto:nom_base')."</strong>";
				 echo "<input type='text' name='base' id='base' value='".$base."'>";
				 fin_cadre_couleur();
				 debut_cadre_couleur();
         echo "<strong>"._T('saveauto:repertoire_stockage')."</strong>";
				 echo "<input type='text' name='rep_bases' id='rep_bases' value='".$rep_bases_conf."' style='width: 300px;'>";
				 echo "<br /><span style='font-size: 11px;'>("._T('saveauto:help_rep').")</span>";
				 fin_cadre_couleur();
				 debut_cadre_couleur();
         echo "<strong>"._T('saveauto:prefixe_sauvegardes')."</strong>";
				 echo "<input type='text' name='prefixe_save' id='prefixe_save' value='".$prefixe_save."' style='width: 200px;'>";
				 echo "<br /><span style='font-size: 11px;'>("._T('saveauto:help_prefixe').")</span>";
				 fin_cadre_couleur();
				 debut_cadre_couleur();
         echo "<strong>"._T('saveauto:frequence')."</strong>";
				 echo "<input type='text' name='frequence_maj' id='frequence_maj' value='".$frequence_maj."' style='width: 30px;'>"._T('saveauto:jours');
				 fin_cadre_couleur();
				 debut_cadre_couleur();
				 echo "<strong>"._T('saveauto:compression_gz')."</strong>";
				 echo _T('saveauto:oui');
				 echo "<input type='radio' name='gz' id='gz_true' value='true' ";
				 if ($gz) echo "checked";
				 echo ">";
				 echo "<input type='radio' name='gz' id='gz_false' value='false' ";
				 if (!$gz) echo "checked";
				 echo ">";
 				 echo _T('saveauto:non');
				 echo "<br /><span style='font-size: 11px;'>("._T('saveauto:help_gz').")</span>";
         if (!$flag_gz && $gz) {
				 		echo "<br><font color=red><strong>"._T('saveauto:attention')."</strong>"._T('saveauto:compression_impossible')."</font>";
         }
				 fin_cadre_couleur();
				 debut_cadre_couleur();
				 echo "<strong>"._T('saveauto:structure_donnees')."</strong>";
				 echo "<br />"._T('saveauto:structure')."";
				 echo _T('saveauto:oui');
				 echo "<input type='radio' name='structure' id='structure_true' value='true' ";
				 if ($structure) echo "checked";
				 echo ">";
    		 echo "<input type='radio' name='structure' id='structure_false' value='false' ";
				 if (!$structure) echo "checked";
				 echo ">";
     		 echo _T('saveauto:non');
				 echo "<br />"._T('saveauto:donnees')."";
				 echo _T('saveauto:oui');
				 echo "<input type='radio' name='donnees' id='donnees_true' value='true' ";
				 if ($donnees) echo "checked";
				 echo ">";
    		 echo "<input type='radio' name='donnees' id='donnees_false' value='false' ";
				 if (!$donnees) echo "checked";
				 echo ">";
     		 echo _T('saveauto:non');
				 fin_cadre_couleur();
				 debut_cadre_couleur();
         echo "<strong>"._T('saveauto:envoi_mail')._T('saveauto:adresse')."</strong>";
				 echo "<input type='text' name='destinataire_save' id='destinataire_save' value='".$destinataire_save."' style='width: 200px;'>";
				 echo "<br /><span style='font-size: 11px;'>("._T('saveauto:help_envoi').")</span>";
				 if ($destinataire_save != '' AND !@mail('toto@truc.net', 'test envoi mail', 'le message de test')) {
	 			 		echo "<br><font color=red><strong>"._T('saveauto:attention')."</strong>"._T('saveauto:mail_absent')._T('saveauto:pas_envoi')."</font>";
			   }
         fin_cadre_couleur();
				 debut_cadre_couleur();
         echo "<strong>"._T('saveauto:tables_acceptes')."</strong>";
         echo "<input type='text' name='accepter' id='accepter' value='".$accepter."' style='width: 300px;'>";
				 echo "<br /><span style='font-size: 11px;'>("._T('saveauto:help_accepter').")</span>";
				 fin_cadre_couleur();
				 debut_cadre_couleur();
         echo "<strong>"._T('saveauto:donnees_ignorees')."</strong>";
         echo "<input type='text' name='eviter' id='eviter' value='".$eviter."' style='width: 300px;'>";
				 echo "<br /><span style='font-size: 11px;'>("._T('saveauto:help_eviter').")</span>";
				 fin_cadre_couleur();
				 debut_cadre_couleur();
         echo "<strong>"._T('saveauto:message_succes')."</strong>";
				 echo _T('saveauto:oui');
				 echo "<input type='radio' name='ecrire_succes' id='ecrire_succes_true' value='true' ";
				 if ($ecrire_succes) echo "checked";
				 echo ">";
    		 echo "<input type='radio' name='ecrire_succes' id='ecrire_succes_false' value='false' ";
				 if (!$ecrire_succes) echo "checked";
				 echo ">";
     		 echo _T('saveauto:non');
				 echo "<br /><span style='font-size: 11px;'>("._T('saveauto:help_msg').")</span>";
         fin_cadre_couleur();
		 		 debut_cadre_couleur();
				 echo "<strong>"._T('saveauto:obsolete_jours')."</strong>";
				 echo "<input type='text' name='jours_obso' id='jours_obso' value='".$jours_obso."' style='width: 30px;'>"._T('saveauto:jours');
		 		 echo "<br /><span style='font-size: 11px;'>("._T('saveauto:help_obsolete').")</span>";
				 fin_cadre_couleur();
				 debut_cadre_couleur();
         echo "<strong>"._T('saveauto:acces_redac')."</strong>";
				 echo _T('saveauto:oui');
				 echo "<input type='radio' name='acces_redac' id='acces_redac_true' value='true' ";
				 if ($acces_redac) echo "checked";
				 echo ">";
    		 echo "<input type='radio' name='acces_redac' id='acces_redac_false' value='false' ";
				 if (!$acces_redac) echo "checked";
				 echo ">";
     		 echo _T('saveauto:non');
				 echo "<br /><span style='font-size: 11px;'>("._T('saveauto:help_acces_redac').")</span>";
         fin_cadre_couleur();
				 
				 echo "<input type='submit' name='valide_config' id='valide_config' value='"._T('saveauto:valider')."' style='float: right;'><br />";
				 fin_cadre_trait_couleur();
         echo "</form>";
				 
				 debut_cadre_trait_couleur("base-24.gif", false, "", _T('saveauto:restauration'));
				 echo _T('saveauto:help_restauration');
         fin_cadre_trait_couleur();
				 
        fin_page();

}
?>
