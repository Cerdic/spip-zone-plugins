<?php
/*
 * Plugin Titre de logo
 *
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) {
    return;
}


/**
 * Upgrade des tables
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function titre_logo_upgrade($nom_meta_base_version, $version_cible)
{
    include_spip('base/objets');
    $tables_objets = array_keys(lister_tables_objets_sql());
    $maj = array();
    $maj['create'] = array();
    foreach ($tables_objets as $table) {
        $maj['create'][] = array('sql_alter',"TABLE $table ADD titre_logo text DEFAULT '' NOT NULL");
        $maj['create'][] = array('sql_alter',"TABLE $table ADD descriptif_logo text DEFAULT '' NOT NULL");
    }

    include_spip('base/upgrade');
    maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Une fonction pour verifier que les champs sont bien sur tous les objets,
 * appelee lors de la configuration
 * (cas d'un nouvel objet ajouté apres l'install du plugin)
 *
 * @return void
 */
function titre_logo_check_upgrade()
{
    include_spip('base/objets');
    $tables_objets = array_keys(lister_tables_objets_sql());
    $trouver_table = charger_fonction('trouver_table', 'base');
    foreach ($tables_objets as $table) {
        $desc = $trouver_table($table);
        if (!isset($desc['field']['titre_logo'])) {
            sql_alter("TABLE $table ADD titre_logo text DEFAULT '' NOT NULL");
        }
        if (!isset($desc['field']['descriptif_logo'])) {
            sql_alter("TABLE $table ADD descriptif_logo text DEFAULT '' NOT NULL");
        }
    }
}

/**
 * Desinstallation
 *
 * @param string $nom_meta_base_version
 */
function titre_logo_vider_tables($nom_meta_base_version)
{
    include_spip('inc/meta');
    include_spip('base/abstract_sql');

    include_spip('base/objets');
    $tables_objets = array_keys(lister_tables_objets_sql());
    foreach ($tables_objets as $table) {
        sql_alter("TABLE $table DROP titre_logo");
        sql_alter("TABLE $table DROP descriptif_logo");
    }

    effacer_meta('titre_logo');
    effacer_meta($nom_meta_base_version);
}
