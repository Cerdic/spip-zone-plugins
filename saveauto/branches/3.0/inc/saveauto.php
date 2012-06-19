<?php

/**
 * La fonction de sauvegarde complete de la base de donnee
 */
function inc_saveauto_dist(){
    global $spip_version_affichee;
    include_spip('inc/saveauto_fonctions');
    $temps = time();

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
     * Vérifier l'existence du répertoire de destination
     */
    if(defined('_DIR_SITE')){
    	$racine = _DIR_SITE;
   	}else{
   		$racine = _DIR_RACINE;
   	}
    $rep_bases = $racine.$rep_bases;
    if (!is_dir($rep_bases)) {
        $err .= _T('saveauto:erreur_repertoire_inexistant',array('rep' => $rep_bases));
    }

    if(!$err && spip_fopen_lock($rep_bases.'.ok', 'a',LOCK_EX) === false){
    	$err .= _T('saveauto:erreur_repertoire_inaccessible',array('rep' => $rep_bases));
    }

    /**
     * Faut il supprimer des sauvegardes existantes
     * si leur date de création sont supérieures à la date maximale
     * de sauvegarde des archives
     */
    if($jours_obso > 0){
	    $sauvegardes = preg_files($rep_bases,"$prefixe_save.+[.](zip|sql)$");
	    foreach($sauvegardes as $sauvegarde){
			$date_fichier = filemtime($sauvegarde);
			if ($temps > ($date_fichier + $jours_obso*3600*24)) {
				supprimer_fichier($sauvegarde);
			}
		}
    }

    if(!$err){
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
	    $nom_fichier = $prefixe_save . $base . "_" . $annee. "_" . $mois. "_" . $jour . "-".$heure."_".$minutes. $suffixe;
	    $chemin_fichier = $rep_bases . $nom_fichier;
	    //recupere et separe tous les noms de tables dont on doit eviter de recuperer les donnees
	    if (!empty($eviter)) $tab_eviter = explode(";", $eviter);
	    if (!empty($accepter)) $tab_accepter = explode(";", $accepter);

	    // listing des tables
	    $sql1 = "SHOW TABLES";
	    $res = sql_query($sql1);
	    if (!$res) {
	        $err .= _T('saveauto:erreur_impossible_liste_tables')."<br>";
	        return $err;
	    }
	    $num_rows = sql_count($res);
	    $i = 0;

	    /**
	     * creation du fichier
	     */

	    /**
	     * Ecriture des entêtes du fichier de sauvegarde
	     * - Le nom de la base
	     * - Le nom du serveur
	     * - La date de génération
	     * - Le type de système d'exploitation
	     * - La version de PHP du serveur
	     * - La version de MySQL du serveur
	     * - L'adresse IP du client qui a généré la sauvegarde
	     * - La version de SPIP installée
	     * - La liste des plugins SPIP installés
	     * - Un commentaire
	     */
	    $contenu = "# "._T('saveauto:info_sql_fichier_genere')."\n";
	    if ($base) {
	        $contenu .= "# "._T('saveauto:info_sql_base').$base."\n";
	    }
	    $contenu .= "# "._T('saveauto:info_sql_serveur').$_SERVER['SERVER_NAME']."\n";
	    $contenu .= "# "._T('saveauto:info_sql_date').$jour."/".$mois."/".$annee." ".$heure."h".$minutes."\n";
	    if (defined('PHP_OS') && preg_match('/win/i', PHP_OS)) $os_serveur = "Windows";
	    else $os_serveur = "Linux/Unix";
	    $contenu .= "# "._T('saveauto:info_sql_os').$os_serveur."\n";
	    $contenu .= "# "._T('saveauto:info_sql_phpversion').phpversion()."\n";
	    $contenu .= "# "._T('saveauto:info_sql_mysqlversion').saveauto_mysql_version()."\n";
	    $contenu .= "# "._T('saveauto:info_sql_ipclient').$GLOBALS['ip']."\n";
	    $contenu .= "# "._T('saveauto:info_sql_spip_version').$spip_version_affichee."\n";

	    /**
	     * Lister les plugins activés
	     */
		if ($cfg = @unserialize($GLOBALS['meta']['plugin'])) {
			$plugins = array_keys($cfg);
			ksort($plugins);
			foreach ($plugins as $plugin) {
				$lsplugs[strtolower($plugin)][] = $alias[$v];
				$versionplug[strtolower($plugin)] = $cfg[$plugin]['version'];
			}
		}
		if ($lsplugs) {
			$cntplugins = count($lsplugs);
			$message_plugin = "# "._T('saveauto:info_sql_plugins_utilises',array('nb'=>$cntplugins))."\n";
			foreach ($lsplugs as $plugin => $c)
				$message_plugin .= "# - $plugin (".$versionplug[$plugin].")"."\n";
		}
		$contenu .= $message_plugin."\n";
	    $contenu .= "# "._T('saveauto:info_sql_compatible_phpmyadmin')."\n"."\n";
	    $contenu .= "# -------"._T('saveauto:info_sql_debut_fichier')."----------"."\n";

	    while ($i < $num_rows) {
	        $tablename = mysql_tablename($res, $i);

	        //selectionne la table avec nom qui correspond a $accepter (ou toutes si $accepter vide)
	        //selectionne la table avec nom qui ne correspond pas a $eviter (ou toutes si $eviter vide)
	        // saveauto_trouve_table($tablename, $tab_accepter) retourne TRUE si un des elements de $tab_accepter correspond a une partie de $tablename
	        if ((empty($accepter) OR saveauto_trouve_table($tablename, $tab_accepter))
	            AND (empty($eviter) OR !(saveauto_trouve_table($tablename, $tab_eviter)))) {
	            //sauve la structure
	            if ($structure) {
	                $contenu .= "\n# "._T('saveauto:info_sql_structure_table',array('table'=>$tablename))."\n";
	                $contenu .= "DROP TABLE IF EXISTS `$tablename`;\n"."\n";
	                // requete de creation de la table
	                $query = "SHOW CREATE TABLE $tablename";
	                $resCreate = mysql_query($query);
	                $row = mysql_fetch_array($resCreate);
	                $schema = $row[1].";";
	                $contenu .= "$schema\n"."\n";
	            }
	            // sauve les donnees
	            if ($donnees) {
	                $contenu .= "# "._T('saveauto:info_sql_donnees_table',array('table'=>$tablename))."\n";
	                $resData = sql_select('*',$tablename);
	                //peut survenir avec la corruption d'une table, on previent
	                if ($connect_statut == "0minirezo" && (!$resData)) {
	                    $err .= _T('saveauto:erreur_probleme_donnees_corruption',array('table'=>$tablename))."<br />";
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
	                            $contenu .= "$lesDonnees"."\n";
	                        }
	                    }
	                }
	            }
	        }
	        $i++;
	    }
	    $contenu .= "# -------"._T('saveauto:info_sql_fin_fichier')."------------"."\n";
		$ok = ecrire_fichier($chemin_fichier, trim($contenu));

		if(!$ok){
	    	$err .= _T('saveauto:erreur_impossible_creer_verifier',array('fichier'=>$nom_fichier,'rep_bases' => $nom_fichier))."<br />";
		}

	    /**
	     * zipper si necessaire
	     * si ok on efface le fichier sql + pour l'envoi par mail on utilise le zip
	     */
	    if (!$err && $gz){
	    	$fichier_zip = $chemin_fichier.'.zip';
	    	if($ok = saveauto_sauvegarde_zip($chemin_fichier,$fichier_zip,$rep_bases)){
	    		$chemin_fichier = $fichier_zip;
	    		$nom_fichier .= '.zip';
	    	}
	    }

		ecrire_meta("saveauto_creation", $temps);
    }

    // Pipeline
	pipeline('post_sauvegarde',
		array(
			'args' => array(
				'err' => $err,
				'chemin_fichier' => $chemin_fichier,
				'nom_fichier'=>$nom_fichier,
				'type' => 'saveauto'
			),
			'data' => $champs
		)
	);

    /**
     * notifications si necessaire
     */
	if ($notifications = charger_fonction('notifications', 'inc')) {
		$notifications('saveauto_save', '',
			array('err' => $err, 'chemin_fichier' => $chemin_fichier, 'nom_fichier'=>$nom_fichier)
		);
	}

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
		supprimer_fichier($chemin_source);
		return true;
	}
}
?>