<?php

/**
 * La fonction de sauvegarde complete
 */
function inc_saveauto_dist(){
    global $sauver_base, $fin_sauvegarde_base;
    include_spip('inc/saveauto_fonctions');
    $temps = time();
	$err = '';

    /**
     * recuperer les $prefix_meta['nom_variable' => 'valeur_variable', ...]
     * sous la forme : $nom_variable = 'valeur_variable'
     */
    foreach (lire_config('saveauto',array()) as $cle => $valeur) {
        if ($valeur == 'true') $$cle = true;
        elseif ($valeur == 'false') $$cle = false;
        else $$cle = $valeur;
    }

    /**
     * options complexes des sauvegardes deportees depuis cfg_saveauto :
     * true = clause INSERT avec nom des champs
     */
    $insertComplet = true;

    /**
     * Faut-il sauver?
     * Lister des fichiers contenus dans le repertoire de sauvegardes
     */
    $entree = array();
    $nbr_entree = 0;
    $rep_bases = _DIR_RACINE.$rep_bases;
    $myDirectory = @opendir($rep_bases);
    if (!$myDirectory) {
        $err .= _T('saveauto:repertoire').$rep_bases._T('saveauto:corriger_config')."<br>";
        return $err;
    }
    while($entryName = readdir($myDirectory)) {
        /**
         * filtre uniquement les fichiers dont le nom commence par prefixe_save
         */
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

    /**
     * trie dans l'ordre decroissant les sauvegardes pour mettre la plus recente en index 0
     */
    rsort($entree);

    if ($nbr_entree > 0) {
        /**
         * recuperer la date de la sauvegarde la plus recente
         */
        $derniere_maj = filemtime($rep_bases . $entree[0]);
        if ($temps > ($frequence_maj*24*3600+$derniere_maj)) $sauver_base = true;
    }
    else $sauver_base = true;   //aucune sauvegarde trouvee !!!
    if (!$sauver_base) return;

    /**
     * On sauvegarde
     * calcul de la date
     */
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
    if (!$res) {
        $err .= _T('saveauto:impossible_liste_tables')."<br>";
        return $err;
    }
    $num_rows = sql_count($res);
    $i = 0;

    /**
     * creation du fichier
     */
    $fp = @fopen($chemin_fichier, "wb");
    if ($connect_statut == "0minirezo" && (!$fp)) {
        $err .= _T('saveauto:impossible_creer').$nom_fichier._T('saveauto:verifier_ecriture').$rep_bases."<br>";
        return $err;
    }
    $_fputs = fwrite;

    //ecriture entete du fichier : infos serveurs php/sql/http
    saveauto_ecrire("# "._T('saveauto:fichier_genere'), $fp, $_fputs);
    if ($base) {
        saveauto_ecrire("# "._T('saveauto:base').$base, $fp, $_fputs);
    }
    saveauto_ecrire("# "._T('saveauto:serveur').$_SERVER['SERVER_NAME'], $fp, $_fputs);
    saveauto_ecrire("# "._T('saveauto:date').$jour."/".$mois."/".$annee." : ".$heure."h".$minutes, $fp, $_fputs);
    if (defined('PHP_OS') && preg_match('/win/i', PHP_OS)) $os_serveur = "Windows";
    else $os_serveur = "Linux/Unix";
    saveauto_ecrire("# "._T('saveauto:os').$os_serveur, $fp, $_fputs);
    saveauto_ecrire("# "._T('saveauto:phpversion').phpversion(), $fp, $_fputs);
    saveauto_ecrire("# "._T('saveauto:mysqlversion').saveauto_mysql_version(), $fp, $_fputs);
    saveauto_ecrire("# "._T('saveauto:ipclient').$GLOBALS['ip'], $fp, $_fputs);
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
                $resCreate = sql_query($query);
                $row = sql_fetch($resCreate);
                $schema = $row[1].";";
                saveauto_ecrire("$schema\n", $fp, $_fputs);
            }
            // sauve les donnees
            if ($donnees) {
                saveauto_ecrire("# "._T('saveauto:donnees_table').$tablename, $fp, $_fputs);
                $resData = sql_select('*',$tablename);
                //peut survenir avec la corruption d'une table, on previent
                if ($connect_statut == "0minirezo" && (!$resData)) {
                    $err .= _T('probleme_donnees').$tablename._T('saveauto:corruption')."<br />";
                }
                else {
                    if (sql_count($resData) > 0) {
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

    /**
     * on ferme !
     */
    fclose($fp);

    /**
     * zipper si necessaire
     * si ok on efface le fichier sql + pour l'envoi par mail on utilise le zip
     */
    if ($gz){
    	$fichier_zip = $chemin_fichier.'.zip';
    	if($ok = saveauto_sauvegarde_zip($chemin_fichier,$fichier_zip,$rep_bases)){
    		$chemin_fichier = $fichier_zip;
    		$nom_fichier .= '.zip';
    	}
    }

    /**
     * envoi par mail si necessaire
     */
    if (!empty($destinataire_save)) {
        $msg_mail = _T('saveauto:sauvegarde_ok_mail')."\n\r"._T('saveauto:base').$base."\n\r"._T('saveauto:serveur').$_SERVER['SERVER_NAME']."\n\r"._T('saveauto:date').$jour."/".$mois."/".$annee." : ".$heure."h".$minutes;
        $sujet_mail = _T('saveauto:saveauto')." "._T('saveauto:base').$base." "._T('saveauto:date').$jour."/".$mois."/".$annee;
        if (!saveauto_mail_attachement($destinataire_save, $sujet_mail, $msg_mail, $chemin_fichier, $nom_fichier)) {
            /**
             * msg d'erreur que pour les admins
             */
            if ($connect_statut == "0minirezo")
               $err .= "<script language=\"javascript\">alert(\""._T('saveauto:probleme_envoi').$nom_fichier._T('saveauto:adresse').$destinataire_save.")\";</script>";
        }
    }

    //marqueur de bon deroulement du processus
    $fin_sauvegarde_base = true;
    ecrire_meta("saveauto_creation", $temps);
    return $err;
}

function saveauto_sauvegarde_zip($chemin_source,$fichier_final,$rep_bases){
	include_spip("inc/pclzip");
	$zip = new PclZip($fichier_final);
	$ok = $zip->create($chemin_source,
		PCLZIP_OPT_REMOVE_PATH, $rep_bases);
	if ($zip->error_code < 0) {
		echo 'saveauto erreur zip: '.$zip->errorName(true).' code: ' . $zip->error_code .' pour fichier ' . $fichier_final;
		return false;
	}
	else {
		unlink($chemin_source);
		return true;
	}
}
?>