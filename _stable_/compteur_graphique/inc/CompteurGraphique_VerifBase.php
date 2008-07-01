<?php

    function create_CompteurGraphiqueTable($CompteurGraphiqueTable) {
    $retour = '';
        $createTableQuery ='CREATE TABLE IF NOT EXISTS '.$CompteurGraphiqueTable.'
        (id_compteur INTEGER NOT NULL AUTO_INCREMENT,
        decompte INTEGER DEFAULT NULL,
        id_article INTEGER DEFAULT NULL,
        id_rubrique INTEGER DEFAULT NULL,
        statut INTEGER DEFAULT NULL,
        longueur INTEGER DEFAULT NULL,
        habillage INTEGER DEFAULT NULL,
        PRIMARY KEY (id_compteur)
        )';
        $resCreateTableQuery = sql_query($createTableQuery);
        $resCompteurTechnique = sql_insertq($CompteurGraphiqueTable,array("decompte" => 0,"statut" => 10));
        if ($resCreateTableQuery != 1) {
            $retour .= "La table ".$CompteurGraphiqueTable." n'existe pas et sa cr&eacute;ation est impossible, le plugin Compteur Graphique ne peut pas fonctionner. Merci de cr&eacute;er la table manuellement ou de <a href=\"?exec=admin_plugin\">d&eacute;sactiver le plugin</a>.";
            spip_log('impossible de cr&eacute;er la table '.$CompteurGraphiqueTable, 'mysql');
            return $retour;
        }
    $retour .= debut_cadre_relief('',true, '', "Cr&eacute;ation de la Table");
    $retour .= _T('compteurgraphique:CG_creation_table');
    $retour .= fin_cadre_relief(true);
    spip_log($CompteurGraphiqueTable.' created', 'mysql');
    return $retour;
    }
    function CompteurGraphiqueTable_Verif_Initiale ($CompteurGraphiqueTable) {
    $res_verif = sql_select("*",$CompteurGraphiqueTable,'','','',1);
    if ($res_verif == '') {return create_CompteurGraphiqueTable($CompteurGraphiqueTable);}
    else {return;}
    }
?>