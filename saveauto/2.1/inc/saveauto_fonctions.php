<?php
/**
 * saveauto : plugin de sauvegarde automatique de la base de donnees de SPIP
 *
 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
 *
 **/

function saveauto_trouve_table($table, $tableau_tables) {
    $trouve = false;
    foreach ($tableau_tables as $t)	{
        if (strstr($table, $t)) {
            $trouve = true;
            break;
        }
    }
    return $trouve;
}


//fonction originale mail_attachement en utilisation libre
//Auteur : Damien Seguy
//Url : http://www.nexen.net
//modifiee pour plus de souplesse sur les entetes
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

    //formatage des entetes
    if (! empty($reply)) $entete = "Reply-to: $reply\n";
    if (! empty($from)) $entete .= "From: $from\n";

    return mail($to, $sujet, $texte.$attachement, $entete.$mail_mime);
}


//ecrit dans un fichier
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
    global $sauver_base, $fin_sauvegarde_base;
    global $connect_statut;
    $temps = time();

    // recuperer les $prefix_meta['nom_variable' => 'valeur_variable', ...]
    // sous la forme : $nom_variable = 'valeur_variable'
    foreach (lire_config('saveauto',array()) as $cle => $valeur) {
        if ($valeur == 'true') $$cle = true;
        elseif ($valeur == 'false') $$cle = false;
        else $$cle = $valeur;
    }

    // options complexes des sauvegardes deportees depuis cfg_saveauto :
    // true = clause INSERT avec nom des champs
    $insertComplet = true;

    // verifier le statut pour lancement: admin ou redacteur si config
    if (($connect_statut != "0minirezo") AND (!$acces_redac OR $connect_statut != "1comite")) return;

  //1-FAUT IL SAUVER (le soldat ryan ?)
    // Lister des fichiers contenus dans le repertoire de sauvegardes
    $entree = array();
    $nbr_entree = 0;
    $rep_bases = _DIR_RACINE.$rep_bases;
    $myDirectory = @opendir($rep_bases);
    if (!$myDirectory) {
        echo _T('saveauto:repertoire').$rep_bases._T('saveauto:corriger_config')."<br>";
        return;
    }
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
    //trie dans l'ordre decroissant les sauvegardes pour mettre la plus recente en index 0
    rsort($entree);

    if ($nbr_entree > 0) {
        //recuperer la date de la sauvegarde la plus recente
        $derniere_maj = filemtime($rep_bases . $entree[0]);
        if ($temps > ($frequence_maj*24*3600+$derniere_maj)) $sauver_base = true;
    }
    else $sauver_base = true;   //aucune sauvegarde trouvee !!!
    if (!$sauver_base) return;

  //2-ON SAUVE (willy)
    //calcul de la date
    $jour = date("d", $temps); //format numerique : 01->31
    $annee = date("Y", $temps); //format numerique : 4 chiffres
    $mois = date("m", $temps);
    $heure = date("H", $temps);
    $minutes = date("i", $temps);

    //choix du nom
    $suffixe = '.sql';
    $nom_fichier = $prefixe_save . $base . "_" . $annee. "_" . $mois. "_" . $jour . $suffixe;
    $chemin_fichier = $rep_bases . $nom_fichier;
    //recupere et separe tous les noms de tables dont on doit eviter de recuperer les donnees
    if (!empty($eviter)) $tab_eviter = explode(";", $eviter);
    if (!empty($accepter)) $tab_accepter = explode(";", $accepter);

    // listing des tables
    $sql1 = "SHOW TABLES";
    $res = spip_query($sql1);
    if (! $res) {
        echo _T('saveauto:impossible_liste_tables')."<br>";
        return;
    }
    $num_rows = sql_count($res);
    $i = 0;

    //cr�ation du fichier
    $fp = @fopen($chemin_fichier, "wb");
    if ($connect_statut == "0minirezo" && (!$fp)) {
        echo _T('saveauto:impossible_creer').$nom_fichier._T('saveauto:verifier_ecriture').$rep_bases."<br>";
        return;
    }
    $_fputs = fwrite;

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

        //selectionne la table avec nom qui correspond a $accepter (ou toutes si $accepter vide)
        //selectionne la table avec nom qui ne correspond pas a $eviter (ou toutes si $eviter vide)
        // saveauto_trouve_table($tablename, $tab_accepter) retourne TRUE si un des elements de $tab_accepter correspond a une partie de $tablename
        if ((empty($accepter) OR saveauto_trouve_table($tablename, $tab_accepter))
            AND (empty($eviter) OR !(saveauto_trouve_table($tablename, $tab_eviter)))) {
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
            // sauve les donnees
            if ($donnees) {
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
                                //on ajoute a la fin une virgule si necessaire
                                if ($j<$num_fields-1) $sFieldnames .= ", ";
                            }
                            $sFieldnames = "($sFieldnames)";
                        }
                        $sInsert = "INSERT INTO `$tablename` $sFieldnames values ";

                        while($rowdata = mysql_fetch_row($resData)) {
                            $lesDonnees = "";
                            for ($mp = 0; $mp < $num_fields; $mp++) {
                                $lesDonnees .= "'" . addslashes($rowdata[$mp]) . "'";
                                //on ajoute a la fin une virgule si necessaire
                                if ($mp<$num_fields-1) $lesDonnees .= ", ";
                            }
                            $lesDonnees = "$sInsert($lesDonnees);";
                            saveauto_ecrire("$lesDonnees", $fp, $_fputs);
                        }
                    }
                }
            }
        }
        $i++;
    }
    saveauto_ecrire("# -------"._T('saveauto:fin_fichier')."------------", $fp, $_fputs);

    //on ferme !
    fclose($fp);
    // zipper si necessaire (si ok on efface le fichier sql + pour l'envoi par mail on utilise le zip)
    if ($gz){
        include_spip("inc/pclzip");
        $fichier_zip = $chemin_fichier.'.zip';
        $zip = new PclZip($fichier_zip);
        $ok = $zip->create($chemin_fichier);
        if ($zip->error_code < 0) {
            echo 'saveauto erreur zip: '.$zip->errorName(true).' code: ' . $zip->error_code .' pour fichier ' . $fichier_zip;
            return;  //die($zip->errorName(true))$zip->error_code
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

    //marqueur de bon deroulement du processus
    $fin_sauvegarde_base = true;
}
?>