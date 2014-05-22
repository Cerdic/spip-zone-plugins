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
 * Declaration des champs sur les objets
 *
 * @param array $tables
 * @return array
 */
function titre_logo_declarer_tables_objets_sql($tables)
{

    // champs titre_logo et descriptif_logo sur tous les objets
    $tables[]['field']['titre_logo'] = "text DEFAULT '' NOT NULL";
    $tables[]['field']['descriptif_logo'] = "text DEFAULT '' NOT NULL";

    return $tables;
}
