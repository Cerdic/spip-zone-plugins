<?php


function formulaires_importer_cog_charger()
{
    include_spip('cog_config');
    include_spip('inc/cog_import');
    include_spip('inc/config');
    $tab_objet = cog_config_tab_fichier();
    $emplacement = _DIR_TMP . lire_config('cog/chemin_donnee');
    $emplacement .= (substr($emplacement, -1) == "/") ? '' : "/";
    foreach ($tab_objet as &$objet) {
        $fichier_manquant = false;

        $tab_fichier = cog_tab_fichier_telecharger($objet['fichier']);

        foreach ($tab_fichier as $fichier) {
            $infos_fichier = pathinfo($fichier);
            if (preg_match('`^http`i', $fichier)) {

                $nom_fichier = $emplacement . $infos_fichier['filename'] . '.' . $infos_fichier['extension'];
                if ($infos_fichier['extension'] == 'zip') {
                    $nom_fichier = $emplacement . $infos_fichier['filename'] . '.txt';
					$nom_fichier = str_replace('-txt', '', $nom_fichier);
                }
                if (!file_exists($nom_fichier)) {
                    $nom_fichier = $emplacement . $infos_fichier['filename'] . '.xls';
                    if (!file_exists($nom_fichier)) {
                        $fichier_manquant = true;
                    }
                }
            } else {
                $nom_fichier = realpath(_DIR_PLUGIN_COG . '/data') . '/' . $infos_fichier['filename'] . '.txt';
                if (!file_exists($nom_fichier)) {
                    $fichier_manquant = true;
                }
            }
        }
        $objet['fichier_manquant'] = $fichier_manquant;
    }
    return array('objet' => '', 'tab_objet' => $tab_objet);
}


function formulaires_importer_cog_verifier_dist()
{
    include_spip('cog_config');
    include_spip('inc/config');
    $tab_objet = cog_config_tab_fichier();
    $emplacement = _DIR_TMP . lire_config('cog/chemin_donnee');
    $emplacement .= (substr($emplacement, -1) == "/") ? '' : "/";
    $erreurs = array();
    if ($objet = _request('objet')) {
        if (!isset($tab_objet[$objet])) {
            $erreurs['fichier'] = _T('cog:fichier_incorrect');
            $erreurs['message_erreur'] .= _T('cog:fichier_incorrect');
        } else {
            $tab_objet = $tab_objet[$objet]['fichier'];
            if (is_array($tab_objet)) {
                $tab_objet = $tab_objet;
            } else {
                $tab_objet = array($tab_objet);
            }
            foreach ($tab_objet as $fichier) {
                $infos_fichier = pathinfo($fichier);
                $extension = $infos_fichier['extension'];
                if ($extension == 'zip') {
                    $extension = 'txt';
                }

                $fichier = $infos_fichier['filename'] . '.' . $extension;
				$fichier = str_replace('-txt', '', $fichier);
                $emplacement_local = realpath(_DIR_PLUGIN_COG . '/data') . '/';
                if (!file_exists($emplacement . $fichier) && !file_exists($emplacement_local . $fichier)) {
                    $fichier = $infos_fichier['filename'] . '.xls';
                }
                if (!file_exists($emplacement . $fichier) && !file_exists($emplacement_local . $fichier)) {
                    $erreurs['fichier'] .= _T('cog:fichier_introuvable') . " " . $emplacement . $infos_fichier['filename'] . '.[txt|xls]';
                    $erreurs['message_erreur'] .= _T('cog:fichier_introuvable');
                }
            }
        }
    }

    return $erreurs;
}

// https://code.spip.net/@inc_editer_mot_dist
function formulaires_importer_cog_traiter_dist()
{

    include_spip('cog_administrations');
    $options = array(
        'truncate' => _request("option_truncate"),
        'replace' => _request("option_ecraser"),
        'filtre' => _request("option_filtre")
    );
    $objet = _request("objet");
    if (function_exists($fonction = 'cog_import_' . _request("fichier"))) {
        list($message, $erreurs) = $fonction($objet, $options);
    } else {
        list($message, $erreurs) = cog_import($objet, $options);
    }
    cog_nouvelle_definition_regionale();
    $retour['editable'] = true;
    if (count($erreurs) == 0) {
        $retour['message_ok'] = $message;
    } else {
        $retour['message_erreur'] = implode('<br />', $erreurs);
    }

    return $retour;
}


function cog_import_cog_epci($objet, $options)
{

    $options['decalage'] = 0;
    return cog_import($objet, $options);

}


function cog_applique_filtre($tab_value, $tab_filtres)
{
    foreach ($tab_filtres as $col => $filtre) {
        if (isset($tab_value[$col])) {
            if ($tab_value[$col] != $filtre) {
                return false;
            }
        }
    }
    return true;
}


function cog_renvoyer_valeur($ligne, $correspondance)
{
    if (isset($correspondance['col'])) {
        return $ligne[$correspondance['col']];
    } else {
        return $ligne[$correspondance];
    }
}


function cog_ramener_valeur(&$ligne, &$correspondance, $objet, &$contenu_fichier, $one = true)
{
    include_spip('inc/config');
    include_spip('cog_config');
    $tab_result = array();
    $objet_config = cog_config_tab_fichier($objet);
    $cle_fichier = $correspondance['fichier'];
    $col_key1 = $correspondance['lien_col_' . objet_type($objet)];
    $col_key2 = $correspondance['lien_col_' . $cle_fichier];
    //Charger le fichier en mémoire
    if (!isset($contenu_fichier[$cle_fichier])) {
        $emplacement = _DIR_TMP . lire_config('cog/chemin_donnee');
        $emplacement .= (substr($emplacement, -1) == "/") ? '' : "/";
        if (isset($objet_config['xls'])) {
            $nom_fichier = $emplacement . $objet_config['xls'][$cle_fichier]['fichier_csv'];
        } else {
            $nom_fichier = $emplacement . $objet_config['fichier'][$cle_fichier]['fichier'];
        }
        $pointeur_fichier = fopen($nom_fichier, "r");
        if ($pointeur_fichier <> 0) {
            $ligne_temp = fgets($pointeur_fichier, 4096);
            $indice = 0;
            $anc_code = '';
            while (!feof($pointeur_fichier)) {
                $ligne_temp = fgets($pointeur_fichier, 4096);
                $ligne_temp = explode("\t", $ligne_temp);
                if (isset($ligne_temp[$col_key2]) and !empty($ligne_temp[$col_key2])) {
                    $contenu_fichier[$cle_fichier][$col_key2][trim($ligne_temp[$col_key2])][] = $ligne_temp;
                }
                $indice++;
            }
        }
    }
    if (isset($contenu_fichier[$cle_fichier][$col_key2][$ligne[$col_key1]][0])) {
        if ($one) {
            return $contenu_fichier[$cle_fichier][$col_key2][$ligne[$col_key1]][0][$correspondance['num_col']];
        } else {
            $tab_result = array();
            foreach ($contenu_fichier[$cle_fichier][$col_key2][$ligne[$col_key1]] as $ligne_temp) {
                //echo("<br />".$ligne_temp[$correspondance['col']]);
                $tab_result[] = $ligne_temp[$correspondance['num_col']];
            }
            return $tab_result;
        }
    }
    return '';
}


function cog_import($objet, $options)
{
    include_spip('cog_config');
    include_spip('inc/config');
    $erreurs = array();
    $contenu_fichier = array();
    $message = "";
    $tab_filtres = array();
    $option_truncate = false;
    if (isset($options['truncate'])) {
        $option_truncate = $options['truncate'];
    }
    $option_replace = false;
    if (isset($options['replace'])) {
        $option_replace = $options['replace'];
    }
    $option_filtre = '';
    if (isset($options['filtre'])) {
        $option_filtre = $options['filtre'];
    }
    $option_decalage = 1;
    if (isset($options['decalage'])) {
        $option_decalage = $options['decalage'];
    }


    $filtres = explode(';', $option_filtre);

    foreach ($filtres as $filtre) {
        $tab_temp = explode('=', $filtre);
        $tab_filtres[$tab_temp[0]] = $tab_temp[1];
    }
    $tab_objet = cog_config_tab_fichier();
    $emplacement = _DIR_TMP . lire_config('cog/chemin_donnee');
    $emplacement .= (substr($emplacement, -1) == "/") ? '' : "/";
    $message = 'Importation du fichier ' . $objet . "<br />";
//	$message.= 'Emplacement du fichier : '.$emplacement."<br />";

    if (is_array($tab_objet[$objet]['fichier'])) {
        $fichier_modele = $tab_objet[$objet]['fichier'][0];
    } else {
        $fichier_modele = $tab_objet[$objet]['fichier'];
    }

    $infos_fichier = pathinfo($fichier_modele);
    $extension = $infos_fichier['extension'];
    if ($extension == 'zip') {
        $extension = 'txt';
		$infos_fichier['filename'] = str_replace('-txt', '', $infos_fichier['filename']);
    }

    if (!file_exists($fichier_modele = realpath(_DIR_PLUGIN_COG . '/data') . '/' . $infos_fichier['filename'] . '.' . $extension)) {

        if (!file_exists($fichier_modele = $emplacement . $infos_fichier['filename'] . '.' . $extension)) {
            if (file_exists($emplacement . $infos_fichier['filename'] . '.xls')) {
                foreach ($tab_objet[$objet]['xls'] as $extraction) {
                    if (!file_exists($emplacement . $extraction['fichier_csv'])) {
                        conversion_fichier_excel($emplacement . $infos_fichier['filename'] . '.xls',
                            $emplacement . $extraction['fichier_csv'], $extraction['onglet'], $extraction['colonnes'],
                            $extraction['ligne_depart'], $extraction['ligne_arrive']);
                    }
                }
                $fichier_modele = $emplacement . $tab_objet[$objet]['xls'][objet_type($objet)]['fichier_csv'];
            }

        }
    }
    $table = table_objet_sql($objet);
    $tab_description = description_table($table);
    if ($option_truncate == 1) {
        $message .= 'Purge de la table ' . $table . "<br />";
        spip_mysql_query('truncate table ' . $table);
        sql_delete($table, array("1" => "1"));
        if (isset($tab_objet[$objet]['relation'])) {
            sql_delete("spip_cog_communes_liens", 'objet=' . sql_quote($objet));
        }
    }

    $req_relation = array();
    if (isset($tab_objet[$objet]['relation'])) {
        $tab_commune = sql_allfetsel('concat(departement,code) as code,id_cog_commune', 'spip_cog_communes');
        foreach ($tab_commune as $com) {
            $tab_temp[$com['code']] = $com['id_cog_commune'];
        }
        $tab_commune = $tab_temp;
    }


    $cle_unique = isset($tab_objet[$objet]['cle_unique']) ? $tab_objet[$objet]['cle_unique'] : array('code');
    $tab_objet_existant = sql_allfetsel(id_table_objet($table) . ',' . implode(',', $cle_unique), $table);
    $tab_temp = array();
    foreach ($tab_objet_existant as $ob) {
        $super_cle = array();
        foreach ($cle_unique as $cle) {
            $super_cle[] = $ob[$cle];
        }
        $tab_temp[implode("-+-", $super_cle)] = $ob[id_table_objet($table)];
    }
    $tab_objet_existant = $tab_temp;

    $pointeur_fichier = fopen($fichier_modele, "r");
    if ($pointeur_fichier <> 0) {
        $ligne = fgets($pointeur_fichier, 4096);
        $nb_ligne = 0;
        while (!feof($pointeur_fichier)) {
            $ligne = fgets($pointeur_fichier, 4096);
            $tab = explode("\t", $ligne);
            if (count($tab) > 1) {
                $tab_value = array();
                $i = 0;
                reset($tab_description['field']);

                while (list ($key, $val) = each($tab_description['field'])) {
                    if ($option_decalage > $i) {
                        $i++;
                        continue;
                    }

                    if (isset($tab_objet[$objet]['correspondance'])) {
                        $tab_correspondance = $tab_objet[$objet]['correspondance'];
                        if (isset($tab_correspondance[$key])) {

                            if (!is_array($tab_correspondance[$key])) {
                                $tab_value[$key] = $tab[$tab_correspondance[$key]];
                            } else {
                                if (isset($tab_correspondance[$key]['col'])) {
                                    if (isset($correspondance[$key]['fichier'])) {
                                        $tab_value[$key] = cog_ramener_valeur($tab, $tab_correspondance[$key], $objet,
                                            $contenu_fichier);
                                    } else {
                                        $tab_value[$key] = cog_renvoyer_valeur($tab, $tab_correspondance[$key]);
                                    }
                                } else {
                                    $tab_value[$key] = "";
                                    reset($tab_correspondance[$key]);
                                    while (list ($indice1, $valeur1) = each($tab_correspondance[$key])) {
                                        $tab_value[$key] .= sql_quote(cog_renvoyer_valeur($tab, $valeur1));
                                    }
                                }
                            }
                        }
                    } else {
                        $tab_value[$key] = utf8_encode(trim($tab[$i - $option_decalage]));
                    }
                    $i++;
                }

                $filtre_relation = false;
                if (isset($tab_objet[$objet]['relation'])) {
                    foreach ($tab_objet[$objet]['relation'] as $key => $relation) {
                        $tab_depcom = cog_ramener_valeur($tab, $relation, $objet, $contenu_fichier, false);
                        for ($ii = 0; $ii < count($tab_depcom); $ii++) {
                            $tab_depcom[$ii] = array(
                                'departement' => substr($tab_depcom[$ii], 0, 2),
                                'code' => substr($tab_depcom[$ii], 2)
                            );

                            if (!cog_applique_filtre($tab_depcom[$ii], $tab_filtres)) {
                                $filtre_relation = true;
                            }

                        }
                    }
                }

                if (!cog_applique_filtre($tab_value, $tab_filtres) || $filtre_relation) {
                    continue;
                }


                $super_cle = array();
                $super_cles = '';
                foreach ($cle_unique as $cle) {
                    $super_cle[] = $tab_value[$cle];
                    $super_cles .= $tab_value[$cle];
                }
                if (!empty($super_cles)) {
                    $existe_deja = false;
                    if (isset($tab_objet_existant[implode("-+-", $super_cle)])) {
                        $existe_deja = true;
                        $id_objet = $tab_objet_existant[implode("-+-", $super_cle)];
                        $where = id_table_objet($table) . '=' . intval($id_objet);
                    }

                    if ($option_replace && $existe_deja) {
                        sql_updateq($table, $tab_value, $where);
                    } elseif (!$existe_deja) {
                        $id_objet = sql_insertq($table, $tab_value);
                    }

                    // Ajout des éventuels liaison
                    if (isset($tab_objet[$objet]['relation'])) {
                        /*foreach($tab_depcom as $key=>$depcom){
                            if($id_cog_commune=$tab_commune[$depcom['departement'].$depcom['code']])
                                sql_insertq("spip_cog_communes_liens",array('id_cog_commune'=>$id_cog_commune,'id_objet'=>$id,'objet'=> objet_type($objet)));
                            else
                                $erreurs[]="Erreur grave Commune introuvable : ".$depcom['departement'].$depcom['code'];
                        }*/
                        $req = array();
                        foreach ($tab_depcom as $key => $depcom) {
                            if (isset($tab_commune[$depcom['departement'] . $depcom['code']])) {
                                $req_relation [] = '(' . implode(',', array(
                                        $tab_commune[$depcom['departement'] . $depcom['code']],
                                        $id_objet,
                                        "'" . objet_type($objet) . "'"
                                    )) . ')';
                            }

                        }
                    }

                    if (($nb_ligne % 100) == 0) {
                        if (!empty($req_relation)) {
                            $req_relation = "REPLACE INTO spip_cog_communes_liens  (id_cog_commune,id_objet,objet) VALUES " . implode(',',
                                    $req_relation);
                            if (!sql_query($req_relation)) {
                                $erreurs[] = "Erreur dans la création des relation avec les communes.";
                            }
                        }

                        $req_relation = array();
                    }

                    $nb_ligne++;
                }
            }
        }
        if (!empty($req_relation)) {
            $req_relation = "REPLACE INTO spip_cog_communes_liens  (id_cog_commune,id_objet,objet) VALUES " . implode(',',
                    $req_relation);
            if (!sql_query($req_relation)) {
                $erreurs[] = "Erreur dans la création des relation avec les communes.";
            }
        }

    }
    $message .= $nb_ligne . ' enregistrements ajoutés.';
    fclose($pointeur_fichier);
    return array($message, $erreurs);

}


function importer_fichier_distant($source)
{
    include_spip('inc/distant');
    $fichier = copie_locale($source);


    include_spip('inc/pclzip');
    $archive = new PclZip($fichier);
    $archive->extract(
        PCLZIP_OPT_PATH, _tmp_dir,
        PCLZIP_CB_PRE_EXTRACT, 'callback_deballe_fichier'
    );
    //$contenu = verifier_compactes($archive);

    effacer_repertoire_temporaire(_tmp_dir);
    return true;
}

unset($filterSubset);


function conversion_fichier_excel($fichier_xls_in, $fichier_xls_out, $sheetname, $cols, $ligne_depart, $ligne_arrive)
{
    $inputFileType = 'Excel5';
//	$inputFileType = 'Excel2007';
//	$inputFileType = 'Excel2003XML';
//	$inputFileType = 'OOCalc';
//	$inputFileType = 'Gnumeric';
    $inputFileName = $fichier_xls_in;
    $chunkSize = 2000;
    $filterSubset = new MyReadFilter($cols);
    $sheetData = array();
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objReader->setReadDataOnly(true);
    $objReader->setLoadSheetsOnly($sheetname);
    $objReader->setReadFilter($filterSubset);
    for ($startRow = $ligne_depart; $startRow <= $ligne_arrive; $startRow += $chunkSize) {
        $filterSubset->setRows($startRow, $chunkSize);
        $objPHPExcel = $objReader->load($inputFileName);
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, false, true, false);
        $objPHPExcel->disconnectWorksheets();
        unset($objPHPExcel);
        $chaine = "";
        foreach ($sheetData as &$data) {
            if (!empty($data[0])) {
                $chaine .= implode("\t", $data) . PHP_EOL;
            }
        }
        file_put_contents($fichier_xls_out, $chaine, FILE_APPEND);
        unset($sheetData);
        unset($chaine);
    }
    unset($filterSubset);
    unset($objReader);
    return true;
}


include_spip('lib/PHPExcel/Classes/PHPExcel/IOFactory');

class MyReadFilter implements PHPExcel_Reader_IReadFilter
{
    private $_startRow = 0;
    private $_endRow = 0;
    private $_columns = array();

    public function __construct($columns)
    {
        $this->_columns = $columns;
    }

    public function setRows($startRow, $chunkSize)
    {
        $this->_startRow = $startRow;
        $this->_endRow = $startRow + $chunkSize;
    }

    public function readCell($column, $row, $worksheetName = '')
    {
        if ($row >= $this->_startRow && $row <= $this->_endRow) {
            if (in_array($column, $this->_columns)) {
                return true;
            }
        }
        return false;
    }
}


?>
