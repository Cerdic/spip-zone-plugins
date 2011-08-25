<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function get_quantite($objet,$id_objet) {

    $table_stocks = table_objet_sql('stocks');

    return sql_getfetsel(
        'quantite',
        $table_stocks,
        array(
            "objet = ".sql_quote($objet),
            "id_objet = ".intval($id_objet)
        )
    );
}

?>
