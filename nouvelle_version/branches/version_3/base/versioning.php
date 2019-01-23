<?php
    if (!defined("_ECRIRE_INC_VERSION")) return;
    function versioning_declarer_tables_principales($tables_principales){
            // Extension de la table articles
            $tables_principales['spip_articles']['field']['version_of'] = "bigint(21) NOT NULL";     
            return $tables_principales;
    }
    ?>
