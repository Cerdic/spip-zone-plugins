<?php
	/**
	 * saveauto : plugin de sauvegarde automatique de la base de donn�es de SPIP
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 *  
	 **/

function saveauto_trouve_table($table, $tableau_tables) {
	$trouve = false;
	for ($i=0; $i<count($tableau_tables); $i++)	{
		if (strstr($table, $tableau_tables[$i])) {
			 $trouve = true; 
			 break;
		}
	}
	return $trouve;
}


//fonction originale mail_attachement en utilisation libre
//Auteur : Damien Seguy
//Url : http://www.nexen.net
//modifi�e pour plus de souplesse sur les ent�tes
function saveauto_mail_attachement($to , $sujet , $message , $fichier, $nom, $reply="", $from="") {
   if (!function_exists('mail')) {
	 		echo _T('saveauto:config_inadaptee').' '._T('saveauto:mail_absent').'<br>';
			return false;
	 }
   $from = $reply = lire_config('email_webmaster');

	 $limite = "_parties_".md5(uniqid (rand()));
   
   $mail_mime = "Date: ".date("l j F Y, G:i")."\n";
   $mail_mime .= "MIME-Version: 1.0\n";
   $mail_mime .= "Content-Type: multipart/mixed;\n";
   $mail_mime .= " boundary=\"----=$limite\"\n\n";
   
   //Le message en texte simple pour les navigateurs qui n'acceptent pas le HTML
   $texte = _T('saveauto:message_MIME')."\n";
   $texte .= "------=$limite\n";
   $texte .= "Content-Type: text/plain; charset=\"iso-8859-1\"\n";
   $texte .= "Content-Transfer-Encoding: 32bit\n\n";
   $texte .= $message;
   $texte .= "\n\n";
   
   //le fichier
   $attachement = "------=$limite\n";
   $attachement .= "Content-Type: application/octet-stream; name=\"$nom\"\n";
   $attachement .= "Content-Transfer-Encoding: base64\n";
   $attachement .= "Content-Disposition: attachment; filename=\"$nom\"\n\n";
   
   $fp = fopen($fichier, "rb");
   $buff = fread($fp, filesize($fichier));
   
   fclose($fp);
   $attachement .= chunk_split(base64_encode($buff));
   
   $attachement .= "\n\n\n------=$limite\n";
   
   //formatage des ent�tes
   if (! empty($reply)) $entete = "Reply-to: $reply\n";
   if (! empty($from)) $entete .= "From: $from\n";
   
   return mail($to, $sujet, $texte.$attachement, $entete.$mail_mime);
}


//�crit dans un fichier (compress� ou pas)
function saveauto_ecrire ($texte, $fp, $_fputs) {
   $_fputs($fp, "$texte\n");
}


function saveauto_mysql_version() {
   $result = mysql_query('SELECT VERSION() AS version');
   if ($result != FALSE && @mysql_num_rows($result) > 0) {
      $row = mysql_fetch_array($result);
      $match = explode('.', $row['version']);
   }
   else {
      $result = @mysql_query('SHOW VARIABLES LIKE \'version\'');
      if ($result != FALSE && @mysql_num_rows($result) > 0) {
         $row = mysql_fetch_row($result);
         $match = explode('.', $row[1]);
      }
   }
   
   if (!isset($match) || !isset($match[0])) $match[0] = 3;
   if (!isset($match[1])) $match[1] = 21;
   if (!isset($match[2])) $match[2] = 0;
   return $match[0] . "." . $match[1] . "." . $match[2];
}

// fonction principale : sauvegarde la base
function saveauto_sauvegarde() {
// d�s�rialiser $meta['prefix_plugin'] en un array $prefix_plugin � partir des donn�es de spip_meta
      	$prefix = 'saveauto';
// r�cup�rer les $prefix_meta['nom_variable' => 'valeur_variable', ...] 
// sous la forme : $nom_variable = 'valeur_variable'				
        foreach (lire_config('saveauto') as $cle => $valeur) {
        				$$cle = $valeur;
        }

   // options complexes des sauvegardes d�port�es depuis cfg_saveauto :
         // true = clause INSERT avec nom des champs
         $insertComplet = true;
         
        
				global $sauver_base, $fin_sauvegarde_base;
				global $connect_statut;
        //acc�s admin ou acc�s r�dacteur (si autoris�)
        if (($connect_statut == "0minirezo") || ($acces_redac && $connect_statut == "1comite")) {
           
           $format_sauve = 'sql';
/* TO DO: � remplacer par l'utilisation de la lib int�gr�e dans SPIP
				  // test support Zlib activ�
					 if ($gz_capable = zlib_get_coding_type()) {
				 		  $flag_gz = TRUE;
				   }				 			 
         //si la compression est impossible (support de Zlib pas activ� dans php.ini), au cas o� le webmaster l'aurait activ� : on d�sactive
					 if ($flag_gz == TRUE AND $gz == 'true') { 
					 		$format_sauve = 'gz';
					 }
					 else {
					 			$format_sauve = 'sql';
					 }
*/                 			 
           $temps = time();
           
           //1-FAUT IL SAUVER (le soldat ryan ?)
           // Lister des fichiers contenus dans le r�pertoire de sauvegardes
           $entree = array();
           $nbr_entree = 0;
           $rep_bases = _DIR_RACINE.$rep_bases;
					 $myDirectory = @opendir($rep_bases);
           if (! $myDirectory) {
					 		echo _T('saveauto:repertoire').$rep_bases._T('saveauto:corriger_config')."<br>";
           }
           if ($myDirectory) {
              while($entryName = readdir($myDirectory)) {
                 //filtre uniquement les fichiers dont le nom commence par prefixe_save
                 if (substr($entryName, 0, strlen($prefixe_save . $base)) == $prefixe_save . $base) {
                    $date_fichier = filemtime($rep_bases . $entryName);
                    if ($jours_obso > 0 && $temps > ($date_fichier + $jours_obso*3600*24)) {
											 @unlink($rep_bases . $entryName);
										}
                    else {
                       $entree[] = $entryName;
                       $nbr_entree++;
                    }
                 }
              }
              closedir($myDirectory);
              //trie dans l'ordre d�croissant les sauvegardes pour mettre la plus r�cente en index 0
              rsort($entree);
              
              if ($nbr_entree > 0) {
                 //r�cup�re la date de la sauvegarde la plus r�cente
                 $derniere_maj = filemtime($rep_bases . $entree[0]);
                 if ($temps > ($frequence_maj*24*3600+$derniere_maj)) $sauver_base = true;
              }
              else $sauver_base = true;//aucune sauvegarde trouv�e !!!
                 
              //2-ON SAUVE (willy)
              if ($sauver_base) {
        	  //echo "on sauve la base<br>";
                 //calcul de la date
                 $jour = date("d", $temps); //format numerique : 01->31
                 $annee = date("Y", $temps); //format numerique : 4 chiffres
                 $mois = date("m", $temps);
                 $heure = date("H", $temps);
                 $minutes = date("i", $temps);
                 
                 //choix du nom
								 $suffixe = '.'.$format_sauve;
                 $nom_fichier = $prefixe_save . $base . "_" . $annee. "_" . $mois. "_" . $jour . $suffixe;
                 $chemin_fichier = $rep_bases . $nom_fichier;
                 //r�cup�re et s�pare tous les noms de tables dont on doit �viter de r�cup�rer les donn�es
                 if (! empty($eviter)) $tab_eviter = explode(";", $eviter);
                 if (! empty($accepter)) $tab_accepter = explode(";", $accepter);
                 
               // listing des tables
								 $sql1 = "SHOW TABLES";
								 $res = spip_query($sql1);								 
                 if (! $res) {
								 		echo _T('saveauto:impossible_liste_tables')."<br>";
                 }
                 if ($res) {
                    $num_rows = sql_count($res);
                    $i = 0;
                    
                    //cr�ation du fichier
                    if ($format_sauve == 'gz') {
											 $fp = @gzopen($chemin_fichier, "wb");
										}
                    else {
												 $fp = @fopen($chemin_fichier, "wb");
										}
                    if ($connect_statut == "0minirezo" && (! $fp)) {
											 echo _T('saveauto:impossible_creer').$nom_fichier._T('saveauto:verifier_ecriture').$rep_bases."<br>";
                    }
                    if ($fp) {
                       //s�lection du type d'�criture
                        $format_sauve == 'gz' ? $_fputs = gzputs : $_fputs = fwrite;
                       
                       //ecriture entete du fichier : infos serveurs php/sql/http
                       saveauto_ecrire("# "._T('saveauto:fichier_genere'), $fp, $_fputs);
                       if ($base) {
											 		saveauto_ecrire("# "._T('saveauto:base').$base, $fp, $_fputs);
											 }
                       saveauto_ecrire("# "._T('saveauto:serveur').$SERVER_NAME, $fp, $_fputs);
                       saveauto_ecrire("# "._T('saveauto:date').$jour."/".$mois."/".$annee." : ".$heure."h".$minutes, $fp, $_fputs);
                       if (defined('PHP_OS') && eregi('win', PHP_OS)) $os_serveur = "Windows"; 
											 else $os_serveur = "Linux/Unix";
                       saveauto_ecrire("# "._T('saveauto:os').$os_serveur, $fp, $_fputs);
                       saveauto_ecrire("# "._T('saveauto:phpversion').phpversion(), $fp, $_fputs);
                       saveauto_ecrire("# "._T('saveauto:mysqlversion').saveauto_mysql_version(), $fp, $_fputs);
                       saveauto_ecrire("# "._T('saveauto:ipclient').$REMOTE_ADDR, $fp, $_fputs);
                       saveauto_ecrire("# "._T('saveauto:compatible_phpmyadmin')."\n", $fp, $_fputs);
                       saveauto_ecrire("# -------"._T('saveauto:debut_fichier')."----------", $fp, $_fputs);
                       
                       while ($i < $num_rows) {
                          $tablename = mysql_tablename($res, $i);
        
                          //s�lectionne la table avec nom qui ne correspond pas � $accepter
                          if (empty($accepter) | saveauto_trouve_table($tablename, $tab_accepter)) {
                             //sauve la structure
                             if ($structure) {
                                saveauto_ecrire("\n# "._T('saveauto:structure_table').$tablename, $fp, $_fputs);
                                saveauto_ecrire("DROP TABLE IF EXISTS `$tablename`;\n", $fp, $_fputs);
                                // requete de creation de la table
                                $query = "SHOW CREATE TABLE $tablename";
                                $resCreate = mysql_query($query);
                                $row = mysql_fetch_array($resCreate);
                                $schema = $row[1].";";
                                saveauto_ecrire("$schema\n", $fp, $_fputs);
                             }
                             
                             //s�lectionne la table avec nom qui ne correspond pas � $eviter
                             if (empty($eviter) | ! (saveauto_trouve_table($tablename, $tab_eviter))) {
                                if ($donnees) {
                                   // les donn�es de la table
                                   saveauto_ecrire("# "._T('saveauto:donnees_table').$tablename, $fp, $_fputs);
                                   $query = "SELECT * FROM $tablename";
                                   $resData = @mysql_query($query);
                                   //peut survenir avec la corruption d'une table, on pr�vient
                                   if ($connect_statut == "0minirezo" && (!$resData)) {
																	 		echo _T('probleme_donnees').$tablename._T('saveauto:corruption')."<br />";
																	 }
                                   else {
                                      if (@mysql_num_rows($resData) > 0) {
                                         $sFieldnames = "";
                                         if ($insertComplet) {
                                            $num_fields = mysql_num_fields($resData);
                                            for($j=0; $j < $num_fields; $j++) {
                                               $sFieldnames .= "`".mysql_field_name($resData, $j) ."`";
                                               //on ajoute � la fin une virgule si n�cessaire
                                               if ($j<$num_fields-1) $sFieldnames .= ", ";
                                            }
                                            $sFieldnames = "($sFieldnames)";
                                         }
                                         $sInsert = "INSERT INTO `$tablename` $sFieldnames values ";
                                         
                                         
                                         while($rowdata = mysql_fetch_row($resData)) {
                                            $lesDonnees = "";
                                            for ($mp = 0; $mp < $num_fields; $mp++) {
                                               $lesDonnees .= "'" . addslashes($rowdata[$mp]) . "'";
                                               //on ajoute � la fin une virgule si n�cessaire
                                               if ($mp<$num_fields-1) $lesDonnees .= ", ";
                                            }
                                            $lesDonnees = "$sInsert($lesDonnees);";
                                            saveauto_ecrire("$lesDonnees", $fp, $_fputs);
                                         }
                                      }
                                   }
                                }
                             }
                             
                          }
        				  $i++;
                       }
                       saveauto_ecrire("# -------"._T('saveauto:fin_fichier')."------------", $fp, $_fputs);
                       
                       //on ferme !
                       if ($format_sauve == 'gz') gzclose($fp);
                       else fclose($fp);
                       
                       // zipper si necessaire (si ok on efface le fichier sql + pour l'envoi par mail on utilise le zip)
                       if ($gz = TRUE){
                           include_spip("inc/pclzip");
                           $fichier_zip = $chemin_fichier.'.zip';
                           $zip = new PclZip($fichier_zip);
                           $ok = $zip->create($chemin_fichier);
                           if ($zip->error_code < 0) {
                                spip_log('saveauto erreur zip ' . $zip->error_code .' pour fichier ' . $fichier_zip);
                        		die($zip->errorName(true));  //$zip->error_code
                           }
                           else {
                               unlink($chemin_fichier);
                               $chemin_fichier = $fichier_zip;
                               $nom_fichier .= '.zip';
                           }
                       }
                       
                       //envoi par mail si necessaire
                       if (!empty($destinataire_save)) {
                           $msg_mail = _T('saveauto:sauvegarde_ok_mail')."\n\r"._T('saveauto:base').$base."\n\r"._T('saveauto:serveur').$SERVER_NAME."\n\r"._T('saveauto:date').$jour."/".$mois."/".$annee." : ".$heure."h".$minutes;
                           $sujet_mail = _T('saveauto:saveauto')." "._T('saveauto:base').$base." "._T('saveauto:date').$jour."/".$mois."/".$annee;
                           if (!saveauto_mail_attachement($destinataire_save, $sujet_mail, $msg_mail, $chemin_fichier, $nom_fichier)) {
                             //msg d'erreur que pour les admins
							       if ($connect_statut == "0minirezo") 
                                   echo "<script language=\"javascript\">alert(\""._T('saveauto:probleme_envoi').$nom_fichier._T('saveauto:adresse').$destinataire_save.")\";</script>";
							   }
                       }
                       
                       //marqueur de bon d�roulement du processus
                       $fin_sauvegarde_base = true;
                    }
                 }
              }
           }
        }
}
?>