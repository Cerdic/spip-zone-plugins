<?php
	/**
	 * saveauto : plugin de sauvegarde automatique de la base de données de SPIP
	 *
	 * Auteur : cy_altern d'après une contrib de Silicium (silicium@japanim.net)
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
//modifiée pour plus de souplesse sur les entêtes
function saveauto_mail_attachement($to , $sujet , $message , $fichier, $nom, $reply="", $from="") {
   if (! @mail('toto@truc.net', 'test envoi mail', 'le message de test')) {
	 		echo _T('saveauto:config_inadaptee').' '._T('saveauto:mail_absent').'<br>';
			return false;
	 }
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
   
   //formatage des entêtes
   if (! empty($reply)) $entete = "Reply-to: $reply\n";
   if (! empty($from)) $entete .= "From: $from\n";
   
   return mail($to, $sujet, $texte.$attachement, $entete.$mail_mime);
}


//écrit dans un fichier (compressé ou pas)
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
// désérialiser $meta['prefix_plugin'] en un array $prefix_plugin à partir des données de spip_meta
      	$prefix = 'saveauto';
//      	include_spip('inc/meta');
// récupérer les $prefix_meta['nom_variable' => 'valeur_variable', ...] 
// sous la forme : $nom_variable = 'valeur_variable'				
				foreach (lire_config('saveauto') as $cle => $valeur) {
								$$cle = $valeur;
				}
/*      	lire_metas();
      	global $meta;
      	$meta_prefix = 'meta'.'_'.$prefix;
      	$$meta_prefix = unserialize($meta[$prefix]);
      	foreach ($$meta_prefix as $cle => $valeur) {
      	 			  $$cle = $valeur;
       }
*/			 
   // options complexes des sauvegardes déportées depuis saveauto_conf.php :
         // true = clause INSERT avec nom des champs
         $insertComplet = true;
        
				global $sauver_base, $fin_sauvegarde_base;
				global $connect_statut;
        //accès admin ou accès rédacteur (si autorisé)
        if (($connect_statut == "0minirezo") || ($acces_redac && $connect_statut == "1comite")) {
				  // test support Zlib activé
					 if ($gz_capable = zlib_get_coding_type()) {
				 		  $flag_gz = TRUE;
				   }				 			 
         //si la compression est impossible (support de Zlib pas activé dans php.ini), au cas où le webmaster l'aurait activé : on désactive
					 if ($flag_gz == TRUE AND $gz == 'true') { 
					 		$format_sauve = 'gz';
					 }
					 else {
					 			$format_sauve = 'sql';
					 }					 
           $temps = time();
           
           //1-FAUT IL SAUVER (le soldat ryan ?)
           // Lister des fichiers contenus dans le répertoire de sauvegardes
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
              //trie dans l'ordre décroissant les sauvegardes pour mettre la plus récente en index 0
              rsort($entree);
              
              if ($nbr_entree > 0) {
                 //récupère la date de la sauvegarde la plus récente
                 $derniere_maj = filemtime($rep_bases . $entree[0]);
                 if ($temps > ($frequence_maj*24*3600+$derniere_maj)) $sauver_base = true;
              }
              else $sauver_base = true;//aucune sauvegarde trouvée !!!
                 
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
                 //récupère et sépare tous les noms de tables dont on doit éviter de récupérer les données
                 if (! empty($eviter)) $tab_eviter = explode(";", $eviter);
                 if (! empty($accepter)) $tab_accepter = explode(";", $accepter);
                 
               // listing des tables
								 $sql1 = "SHOW TABLES";
								 $res = spip_query($sql1);								 
                 if (! $res) {
								 		echo _T('saveauto:impossible_liste_tables')."<br>";
                 }
                 if ($res) {
                    $num_rows = spip_num_rows($res);
                    $i = 0;
                    
                    //création du fichier
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
                       //sélection du type d'écriture
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
        
                          //sélectionne la table avec nom qui ne correspond pas à $accepter
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
                             
                             //sélectionne la table avec nom qui ne correspond pas à $eviter
                             if (empty($eviter) | ! (saveauto_trouve_table($tablename, $tab_eviter))) {
                                if ($donnees) {
                                   // les données de la table
                                   saveauto_ecrire("# "._T('saveauto:donnees_table').$tablename, $fp, $_fputs);
                                   $query = "SELECT * FROM $tablename";
                                   $resData = @mysql_query($query);
                                   //peut survenir avec la corruption d'une table, on prévient
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
                                               //on ajoute à la fin une virgule si nécessaire
                                               if ($j<$num_fields-1) $sFieldnames .= ", ";
                                            }
                                            $sFieldnames = "($sFieldnames)";
                                         }
                                         $sInsert = "INSERT INTO `$tablename` $sFieldnames values ";
                                         
                                         
                                         while($rowdata = mysql_fetch_row($resData)) {
                                            $lesDonnees = "";
                                            for ($mp = 0; $mp < $num_fields; $mp++) {
                                               $lesDonnees .= "'" . addslashes($rowdata[$mp]) . "'";
                                               //on ajoute à la fin une virgule si nécessaire
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
                       
                       //envoi par mail
                       if (! empty($destinataire_save)) {
                          $msg_mail = _T('saveauto:sauvegarde_ok')."\n\r"._T('saveauto:base').$base."\n\r"._T('saveauto:serveur').$SERVER_NAME."\n\r"._T('saveauto:date').$jour."/".$mois."/".$annee." : ".$heure."h".$minutes;
                          $sujet_mail = _T('saveauto:saveauto')." "._T('saveauto:base').$base." "._T('saveauto:date').$jour."/".$mois."/".$annee;
                          //msg d'erreur que pour les admins
                          if ($connect_statut == "0minirezo" && (! saveauto_mail_attachement($destinataire_save, $sujet_mail, $msg_mail, $chemin_fichier, $nom_fichier))) {
														 echo _T('saveauto:probleme_envoi').$nom_fichier._T('saveauto:adresse').$destinataire_save."<br />";
													}
                       }
                       
                       //marqueur de bon déroulement du processus
                       $fin_sauvegarde_base = true;
                    }
                 }
              }
           }
        }
}
?>