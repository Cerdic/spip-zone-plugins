<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Prépare et ecrit le fichier de sauvegarde
| des Tables DW2
+--------------------------------------------+
| VO . de M. ONFRAY modifié pour DW2 et spip 1.9
+--------------------------------------------+
*/


function trouve_table($table, $tableau_tables)
	{
	$trouve = false;
	for ($i=0; $i<count($tableau_tables); $i++)
		{
		if (strstr($table, $tableau_tables[$i])) {$trouve = true; break;}
		}
	return $trouve;
	}


//fonction originale mail_attachement en utilisation libre
//Auteur : Damien Seguy - Url : http://www.nexen.net
//modifiée pour plus de souplesse sur les entêtes
function mail_attachement($to , $sujet , $message , $fichier, $nom, $reply="", $from="")
{
   $limite = "_parties_".md5(uniqid (rand()));
   
   $mail_mime = "Date: ".date("l j F Y, G:i")."\n";
   $mail_mime .= "MIME-Version: 1.0\n";
   $mail_mime .= "Content-Type: multipart/mixed;\n";
   $mail_mime .= " boundary=\"----=$limite\"\n\n";
   
   //Le message en texte simple pour les navigateurs qui n'acceptent pas le HTML
   $texte = "This is a multi-part message in MIME format.\n";
   $texte .= "Ceci est un message est au format MIME.\n";
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


// Ecrit dans un fichier (compressé ou pas)
function ecrire($texte,$fp , $_fputs) {
   #global $fp, $_fputs;
   $_fputs($fp, "$texte\n");
   #if($_fputs=="fputs") { fputs($fp, "$texte\n"); }
   #else { gzputs($fp, "$texte\n"); }
}


function mysql_version() {
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


//
// Sauvegarde ... action !
//

$sauver_base = false;
$fin_sauvegarde_base = false;

// acces admin 
// vo : ... || ($acces_redac && $connect_statut == "1comite")
if ($GLOBALS['connect_statut']=="0minirezo" && $flag_save_dw) {

	//si la compression est impossible, au cas où le webmaster l'aurait activé : on désactive
	if (!$flag_gz) $gz = false;
	$temps = time();
	
	//1-FAUT IL SAUVER (le soldat ryan ?)
	   
	// Lister des fichiers contenus dans le répertoire de sauvegardes
	$entree = array();
	$nbr_entree = 0;
	$myDirectory = @opendir($rep_bases);
	if ($GLOBALS['connect_statut'] == "0minirezo" && (! $myDirectory)) {
		echo(_T('dw:repert_save_existe_pas', array('rep_bases' => $rep_bases)));
	}
	  
	if ($myDirectory) {
		
		while($entryName = readdir($myDirectory)) {
			//filtre uniquement les fichiers du type : save_nom_de_la_base
			if (substr($entryName, 0, strlen($prefixe_save . $base)) == $prefixe_save . $base) {
				$date_fichier = filemtime($rep_bases . $entryName);
				if ($jours_obso > 0 && $temps > ($date_fichier + $jours_obso*3600*24)) @unlink($rep_bases . $entryName);
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
			//echo "on sauve la base<br />";
			//calcul de la date
			$jour = date("d", $temps); //format : 01->31
			$annee = date("y", $temps); //format : 2 chiffres
			$mois = date("m", $temps);
			$heure = date("H", $temps);
			$minutes = date("i", $temps);
					 
			//choix du nom
			if ($gz) $suffixe = ".gz";
			else $suffixe = ".sql";
			$nom_fichier = $prefixe_save .$base."_".$annee.$mois.$jour.$suffixe;
			$chemin_fichier = $rep_bases.$nom_fichier;
        
			//récupère et sépare tous les noms de tables dont on doit éviter de récupérer les données
			if (! empty($eviter)) $tab_eviter = explode(";", $eviter);
			if (! empty($accepter)) $tab_accepter = explode(";", $accepter);
        
			// listing des tables
			$res = @mysql_list_tables($base);
			if ($GLOBALS['connect_statut'] == "0minirezo" && (! $res)) {
				echo(_T('dw:liste_tables_base_impossible', array('base' => $base)));
			}

			if ($res) {
				$num_rows = mysql_num_rows($res);
				$i = 0;
           
				//création du fichier
				if ($gz) $fp = @gzopen($chemin_fichier, "wb");
				else $fp = @fopen($chemin_fichier, "wb");
				if ($GLOBALS['connect_statut'] == "0minirezo" && (! $fp)) {
					echo (_T('dw:crea_fichier_impossible_droits', array('nom_fichier' => $nom_fichier, 'rep_bases' => $rep_bases)));
				}
            
				if ($fp) {
					//sélection du type d'écriture
					$_fputs = ($gz) ? 'gzputs' : 'fputs'; // $_fputs = ($gz) ? gzputs : fputs;

					//ecriture entete du fichier : infos serveurs php/sql/http
					ecrire("# Sauvegarde Tables DW2", $fp, $_fputs);
					ecrire("# DW2 version : ".$GLOBALS['dw2_param']['version_installee'], $fp, $_fputs);
					ecrire("# Base : $base", $fp, $_fputs);
					ecrire("# Serveur : ".$_SERVER['SERVER_NAME'], $fp, $_fputs);
					ecrire("# Date (d/m/y) : $jour/$mois/$annee à $heure" . "h" . $minutes, $fp, $_fputs);
					if (defined('PHP_OS') && eregi('win', PHP_OS)) {
						$os_serveur = "Windows";
					} else {
						$os_serveur = "Linux/Unix";
					}
					ecrire("# OS Serveur : $os_serveur", $fp, $_fputs);
					ecrire("# Version PHP : " . phpversion(), $fp, $_fputs);
					ecrire("# Version mySQL : " . mysql_version(), $fp, $_fputs);
					ecrire("# IP Client : ".$_SERVER['REMOTE_ADDR'], $fp, $_fputs);
					ecrire("# Fichier SQL 100% compatible PhpMyAdmin\n", $fp, $_fputs);
					ecrire("# -------debut du fichier----------", $fp, $_fputs);
               
					while ($i < $num_rows) {
						$tablename = mysql_tablename($res, $i);

						//sélectionne la table avec nom qui ne correspond pas à $accepter
						if (empty($accepter) | trouve_table($tablename, $tab_accepter)) {
                     
							//sauve la structure
							if ($structure) {
								ecrire("\n# Structure de la table $tablename", $fp, $_fputs);
								ecrire("DROP TABLE IF EXISTS `$tablename`;\n", $fp, $_fputs);
								// requete de creation de la table
								$query = "SHOW CREATE TABLE $tablename";
								$resCreate = mysql_query($query);
								$row = mysql_fetch_array($resCreate);
								$schema = $row[1].";";
								ecrire("$schema\n", $fp, $_fputs);
							}
                     
							//sélectionne la table avec nom qui ne correspond pas à $eviter
							if (empty($eviter) | ! (trouve_table($tablename, $tab_eviter))) {
								if ($donnees) {
									// les données de la table
									ecrire("# Données de $tablename", $fp, $_fputs);
									$query = "SELECT * FROM $tablename";
									$resData = @mysql_query($query);
									//peut survenir avec la corruption d'une table, on prévient
									if ($GLOBALS['connect_statut'] == "0minirezo" && (!$resData)) {
										echo _T('dw:donnees_corrupt', array('tablename' => $tablename));
									} else {
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
												ecrire("$lesDonnees", $fp, $_fputs);
											}
										}
									}
								}
							}
						}
						$i++;
					}
					ecrire("# -------fin du fichier------------", $fp, $_fputs);
						
					//on ferme !
					if ($gz) gzclose($fp);
					else fclose($fp);
               
					//envoi par mail
					if (! empty($destinataire_save)) {
						$msg_mail = _T('dw:mail_save_msg_1')."\n\r".
									_T('dw:mail_save_msg_base', array('base' => $base))."\n\r".
									_T('dw:mail_save_msg_serveur', array('serveur' => $_SERVER['SERVER_NAME']))."\n\r".
									_T('dw:date_heure', array('jour' => $jour, 'mois' => $mois, 'annee' => $annee, 'heure' => $heure, 'minutes' => $minutes))."\n\r".
									_T('dw:mail_save_msg_admin', array('admin' => $GLOBALS['connect_login']));
						$sujet_mail = _T('dw:mail_save_sujet', array('nom_site_spip' => $nom_site_spip));

						//envois mail - msg d'erreur que pour les admins
						if ($GLOBALS['connect_statut'] == "0minirezo" && (! mail_attachement($destinataire_save, $sujet_mail, $msg_mail, $chemin_fichier, $nom_fichier, '', $nom_site_spip))) {
							echo _T('dw:echec_mail_save', array('nom_fichier' => $nom_fichier, 'dest_save' => $destinataire_save))."<br />";
						}
					}
						
					//marqueur de bon déroulement du processus
					$fin_sauvegarde_base = true;
					echo _T('dw:sauvegarde_effectuee')."<br />";
					echo $nom_fichier."<br />";
				}
			}
		}
	}
}
			

?>
