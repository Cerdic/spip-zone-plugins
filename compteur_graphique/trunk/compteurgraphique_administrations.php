<?php

function compteurgraphique_install($action)
{
    $CompteurGraphiqueTable = 'spip_compteurgraphique';
    switch ($action) {

    case 'test':
        if (!opendir(_DIR_IMG.'CompteurGraphique')) {
            mkdir(_DIR_IMG.'CompteurGraphique');
        }
        $CG_verif_ancien = sql_select('id_compteur', $CompteurGraphiqueTable, 'statut = 10');
        $CG_ver_tab_ancien = sql_fetch($CG_verif_ancien);
        $CG_id_compteur_ancien = $CG_ver_tab_ancien['id_compteur'];
        if (isset($CG_id_compteur_ancien)) {
            sql_query('RENAME TABLE ext_compteurgraphique TO spip_compteurgraphique');
        }
        $CG_verif = sql_select('id_compteur', $CompteurGraphiqueTable, 'statut = 10');
        $CG_ver_tab = sql_fetch($CG_verif);
        $CG_id_compteur = $CG_ver_tab['id_compteur'];
        if (!isset($CG_id_compteur)) {
            return false;
        } else {
            return true;
        }
        break;

    case 'install':
        $CG_verif = sql_select('id_compteur', $CompteurGraphiqueTable, 'statut = 10');
        $CG_ver_tab = sql_fetch($CG_verif);
        $CG_id_compteur = $CG_ver_tab['id_compteur'];
        if (!isset($CG_id_compteur)) {
            include_spip('base/compteurgraphique');
            include_spip('base/create');
            creer_base();
            sql_insertq($CompteurGraphiqueTable, array('decompte' => 0, 'statut' => 10));
        }
        break;

    case 'uninstall':
        sql_query('DROP TABLE spip_compteurgraphique');
        break;
    }
}
