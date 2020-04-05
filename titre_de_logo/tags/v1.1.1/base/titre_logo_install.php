<?php

function titre_logo_declarer_tables_principales($tables_principales)
{

    $tables_principales['spip_articles']['field']['titre_logo'] = "text DEFAULT '' NOT NULL";
    $tables_principales['spip_articles']['field']['descriptif_logo'] = "text DEFAULT '' NOT NULL";

    $tables_principales['spip_rubriques']['field']['titre_logo'] = "text DEFAULT '' NOT NULL";
    $tables_principales['spip_rubriques']['field']['descriptif_logo'] = "text DEFAULT '' NOT NULL";

    $tables_principales['spip_auteurs']['field']['titre_logo'] = "text DEFAULT '' NOT NULL";
    $tables_principales['spip_auteurs']['field']['descriptif_logo'] = "text DEFAULT '' NOT NULL";

    $tables_principales['spip_breves']['field']['titre_logo'] = "text DEFAULT '' NOT NULL";
    $tables_principales['spip_breves']['field']['descriptif_logo'] = "text DEFAULT '' NOT NULL";

    $tables_principales['spip_syndic']['field']['titre_logo'] = "text DEFAULT '' NOT NULL";
    $tables_principales['spip_syndic']['field']['descriptif_logo'] = "text DEFAULT '' NOT NULL";

    $tables_principales['spip_mots']['field']['titre_logo'] = "text DEFAULT '' NOT NULL";
    $tables_principales['spip_mots']['field']['descriptif_logo'] = "text DEFAULT '' NOT NULL";

    global $table_des_traitements;
    $table_des_traitements['TITRE_LOGO'][] = 'typo(%s)';
    $table_des_traitements['DESCRIPTIF_LOGO'][] = 'propre(%s)';

    return $tables_principales;
}

?>