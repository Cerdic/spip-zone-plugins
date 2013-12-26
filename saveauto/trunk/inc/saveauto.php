<?php

/**
 * La fonction de sauvegarde complete de la base de donnee
 */
function inc_saveauto_dist($tables=array(), $options=array()) {
    global $spip_version_affichee;
	$erreur = '';

    /**
     * recuperer les meta de config $config['nom_variable' => 'valeur_variable', ...]
     * sous la forme : $nom_variable = 'valeur_variable'
     */
	include_spip('inc/config');
	// Si l'appel provient d'une requete manuelle alors $options est non vide et il faut l'utiliser
	// en priorité à la config
	$config = lire_config('saveauto',array());
	$config = array_merge($config, $options);

    foreach ($config as $cle => $valeur) {
        if ($valeur == 'true') $$cle = true;
        elseif ($valeur == 'false') $$cle = false;
        else $$cle = $valeur;
    }

    /**
     * options complexes des sauvegardes deportees depuis cfg_saveauto :
     * true = clause INSERT avec nom des champs
     */
    $insertion_complete = true;

    /**
     * Faut-il sauver?
     * Si un répertoire de stockage est configuré, tester son existence et basculer sur tmp/dump si absent
     * Vérifier l'existence du répertoire tmp/dump/, le créer si besoin et tester son accessibilité
     */
	$dir_dump = (isset($repertoire_save) ? $repertoire_save : _DIR_DUMP);
	if ($dir_dump != _DIR_DUMP AND !@file_exists($dir_dump)) {
		$erreur .= _T('saveauto:erreur_repertoire_perso_inaccessible',array('rep' => $dir_dump));
		$dir_dump = _DIR_DUMP;
	}
	if (!@file_exists($dir_dump)
	AND !$dir_dump = sous_repertoire(_DIR_DUMP,'',false,true)) {
		$dir_dump = preg_replace(','._DIR_TMP.',', '', _DIR_DUMP);
		$dir_dump = sous_repertoire(_DIR_TMP, $dir_dump);
	}
    if(spip_fopen_lock($dir_dump.'.ok', 'a',LOCK_EX) === false){
    	$erreur .= _T('saveauto:erreur_repertoire_inaccessible',array('rep' => $dir_dump));
    }

    if(!$erreur){
	    // Listing des tables :
		include_spip('base/dump');
		// - Si la liste est fournie on l'utilise telle quelle
		// - Sinon il faut calculer la liste des tables à exporter en excluant celles définies en noexport
		if (!$tables) {
			$exclude = lister_tables_noexport();
			$tables = base_lister_toutes_tables('', $tables, $exclude, true);
		}

	    /**
	     * creation du fichier de sauvegarde
	     */
		if ($tables) {
			include_spip('inc/filtres');

			/**
			* On construit le nom du fichier de sauvegarde
			*/
			$temps = time();
			$base = $GLOBALS['connexions'][0]['db'];
			$nom_fichier = $prefixe_save . '_' . $base. '_' . date("Ymd_His", $temps) . '.sql';
			$chemin_fichier = $dir_dump . $nom_fichier;

			// Identifiation de l'auteur de la sauvegarde (CRON ou un id_auteur)
			$auteur = $options['auteur'] ? $options['auteur'] : $GLOBALS['visiteur_session']['id_auteur'];
			if ($id = intval($auteur))
				$auteur = sql_getfetsel('nom', 'spip_auteurs', 'id_auteur=' . sql_quote($id));

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
			* - Un commentaire
			*/
			$contenu = "# "._T('saveauto:info_sql_fichier_genere')."\n";
			$contenu .= "# "._T('saveauto:info_sql_base').$base."\n";
			$contenu .= "# "._T('saveauto:info_sql_serveur').$_SERVER['SERVER_NAME']."\n";
			$contenu .= "# "._T('saveauto:info_sql_date') . affdate_heure(date("Y-m-d H:i:s", $temps)) ."\n";
			$contenu .= "# "._T('saveauto:info_sql_os').php_uname()."\n";
			$contenu .= "# "._T('saveauto:info_sql_phpversion').phpversion()."\n";
			$contenu .= "# "._T('saveauto:info_sql_mysqlversion').informer_mysql_version()."\n";
			$contenu .= "# "._T('saveauto:info_sql_ipclient').$GLOBALS['ip']."\n";
			$contenu .= "# "._T('saveauto:info_sql_auteur').$auteur."\n";
			$contenu .= "# "._T('saveauto:info_sql_spip_version').$spip_version_affichee."\n";
			$contenu .= "# "._T('saveauto:info_sql_compatible_phpmyadmin')."\n"."\n";
			$contenu .= "# -------"._T('saveauto:info_sql_debut_fichier')."----------"."\n";

			foreach($tables as $_cle => $_table) {
				//sauve la structure
				if ($structure) {
					$contenu .= "\n# "._T('saveauto:info_sql_structure_table',array('table'=>$_table))."\n";
					$contenu .= "DROP TABLE IF EXISTS `$_table`;\n"."\n";
					// requete de creation de la table
					$query = "SHOW CREATE TABLE $_table";
					$resCreate = mysql_query($query);
					if ($resCreate) {
						$row = mysql_fetch_array($resCreate);
						if ($row) {
							$schema = $row[1].";";
							$contenu .= "$schema\n"."\n";
						}
					}
				}
				// sauve les donnees
				if ($donnees) {
					$contenu .= "# "._T('saveauto:info_sql_donnees_table',array('table'=>$_table))."\n";
					$resData = sql_select('*',$_table);
					//peut survenir avec la corruption d'une table, on previent
					if ($connect_statut == "0minirezo" && (!$resData)) {
						$erreur .= _T('saveauto:erreur_probleme_donnees_corruption',array('table'=>$_table))."<br />";
					}
					else {
						if (sql_count($resData) > 0) {
							$sFieldnames = "";
							if ($insertion_complete) {
								$num_fields = mysql_num_fields($resData);
								for($j=0; $j < $num_fields; $j++) {
									$sFieldnames .= "`".mysql_field_name($resData, $j) ."`";
									//on ajoute a la fin une virgule si necessaire
									if ($j<$num_fields-1) $sFieldnames .= ", ";
								}
								$sFieldnames = "($sFieldnames)";
							}
							$sInsert = "INSERT INTO `$_table` $sFieldnames values ";

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

			// Cloture du contenu de la sauvegarde
			$contenu .= "# -------"._T('saveauto:info_sql_fin_fichier')."------------"."\n";

			// Ecriture du contenu dans le fichier sql destination
			if (!ecrire_fichier($chemin_fichier, trim($contenu))){
				$erreur .= _T('saveauto:erreur_impossible_creer_verifier',array('fichier'=>$nom_fichier, 'rep_bases' => $dir_dump))."<br />";
			}

			/**
			 * zipper si necessaire
			 * si ok on efface le fichier sql + pour l'envoi par mail on utilise le zip
			 */
			if (!$erreur
			AND (filesize($chemin_fichier) < $max_zip*1000*1000)) {
				$fichier_zip = $chemin_fichier.'.zip';
				$options = array('auteur' => $auteur, 'tables' => $tables);
				if (!$erreur = creer_zip($chemin_fichier, $fichier_zip, $dir_dump, $options)) {
					$chemin_fichier = $fichier_zip;
					$nom_fichier .= '.zip';
				}
			}
		}
		else {
			$erreur .= _T('saveauto:erreur_impossible_liste_tables');
		}
    }

    // Pipeline
	pipeline('post_sauvegarde',
		array(
			'args' => array(
				'err' => $erreur,
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
	if (!$manuel
	AND ($notifications = charger_fonction('notifications', 'inc'))) {
		$notifications('saveauto', '',
			array('err' => $erreur, 'chemin_fichier' => $chemin_fichier, 'nom_fichier'=>$nom_fichier)
		);
	}

    return $erreur;
}

function creer_zip($chemin_source, $fichier_final, $dir_dump, $options=array()) {
	include_spip("inc/pclzip");
	$erreur = '';

	$zip = new PclZip($fichier_final);
	$comment = array('auteur' => $options['auteur'], 'contenu' => $options['tables']);
	$retour = $zip->create($chemin_source,
		PCLZIP_OPT_COMMENT, serialize($comment),
		PCLZIP_OPT_REMOVE_PATH, $dir_dump,
		PCLZIP_OPT_ADD_TEMP_FILE_ON);

	if($retour == 0)
		$erreur = $zip->errorInfo(true);
	else
		supprimer_fichier($chemin_source);

	return $erreur;
}

function informer_mysql_version() {
   $result = sql_query('SELECT VERSION() AS version');
   if ($result != FALSE && sql_count($result) > 0) {
      $row = mysql_fetch_array($result);
      $match = explode('.', $row['version']);
   }
   else {
      $result = sql_query('SHOW VARIABLES LIKE \'version\'');
      if ($result != FALSE && sql_count($result) > 0) {
         $row = mysql_fetch_row($result);
         $match = explode('.', $row[1]);
      }
   }

   if (!isset($match) || !isset($match[0])) $match[0] = 3;
   if (!isset($match[1])) $match[1] = 21;
   if (!isset($match[2])) $match[2] = 0;
   return $match[0] . "." . $match[1] . "." . $match[2];
}

?>
