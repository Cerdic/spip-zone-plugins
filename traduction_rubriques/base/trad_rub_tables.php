<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function trad_rub_declarer_tables_principales($tables_principales){
        // Extension de la table rubriques
        $tables_principales['spip_rubriques']['field']['id_trad'] = "varchar(50)  NOT NULL";      
        return $tables_principales;
}



?>
