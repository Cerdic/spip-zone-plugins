<?php

if (!defined('_ECRIRE_INC_VERSION'))
    return;

function autartrole_declarer_tables_auxiliaires($tables_auxiliaires)
{
    // Extension de la table auteurs_articles
    $liaisons = &$tables_auxiliaires['spip_auteurs_articles'];
    $liaisons['field']['role'] = "VARCHAR(200) DEFAULT '' NOT NULL"; // 20120202: role de l'auteur dans l'article (but initial du plugin = expliciter les auteurs)
    $liaisons['field']['rang'] = "TINYINT DEFAULT 0 NOT NULL"; // 20120210: rang de l'auteur dans l'article (besoin complementaire assez courant = trier les auteurs)
    $liaisons['field']['maj'] = "TIMESTAMP NOT NULL"; // 20120210: horodatage des modifications (i.e. trace de la derniere mise a jour)

    return $tables_auxiliaires;
}

?>