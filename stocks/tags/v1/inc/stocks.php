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

function set_quantite($objet,$id_objet,$quantite) {


    $table_stocks = table_objet_sql('stocks');
    $quantite = intval($quantite);
        
    $insert = sql_insertq(
        $table_stocks,
        array(
            "objet" => $objet,
            "id_objet" => intval($id_objet),
            "quantite" => $quantite
        )
    );


    if (!$insert) {
        $update = sql_update(
            $table_stocks,
            array(
                "quantite" => intval($quantite)
            ),
            array(
                "objet = ".sql_quote($objet),
                "id_objet = ".intval($id_objet)
            )
        );
    }

    if ($insert || $update)
        return $quantite;
    else
        return false;
}

function incrementer_quantite($objet,$id_objet,$quantite) {

    $table_stocks = table_objet_sql('stocks');

    $quantite = intval($quantite);

    if ($quantite == 0)
        return 0;

    if ($quantite > 0)
        $set = array("quantite" => "quantite + ".abs($quantite));
    else 
        $set = array("quantite" => "quantite - ".abs($quantite));

    $update = sql_update(
        $table_stocks,
        $set ,
        array(
            "objet = ".sql_quote($objet),
            "id_objet = ".intval($id_objet)
        )
    );

    return $update;
}

?>
