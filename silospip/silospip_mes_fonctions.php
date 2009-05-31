<?php

// function oui($c) { return($c ? ' ' : ''); }


// Boucles SiloSPIP
global $tables_principales,$exceptions_des_tables,$table_date;


//
// <BOUCLE(SILOSITES)>
//
function boucle_SILOSITES($id_boucle, &$boucles) {
        global $table_des_tables;
        $boucle = &$boucles[$id_boucle];
        $id_table = $boucle->id_table;
        $type = $boucle->type_requete;
        $id_table = $table_des_tables[$type];
        if (!$id_table)
        //      table hors SPIP
                $boucle->from[$type] =  $type;
        else {
        // les tables declarees par spip ont un prefixe et un surnom
                $boucle->from[$id_table] =  'spip_' . $type ;
        }
        
        return (calculer_boucle($id_boucle, $boucles));
}


?>
