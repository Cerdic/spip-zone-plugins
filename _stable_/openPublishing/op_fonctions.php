<?php

/*
 * <BOUCLE(op_rubriques)>
 */

function boucle_OP_RUBRIQUES_dist($id_boucle, &$boucles) {
        $boucle = &$boucles[$id_boucle];
        $id_table = $boucle->id_table;
        $boucle->from[$id_table] =  "spip_op_rubriques";
        return calculer_boucle($id_boucle, $boucles);
}

/*
 * <BOUCLE(op_auteurs)>
 */

function boucle_OP_AUTEURS_dist($id_boucle, &$boucles) {
        $boucle = &$boucles[$id_boucle];
        $id_table = $boucle->id_table;
        $boucle->from[$id_table] =  "spip_op_auteurs";
        return calculer_boucle($id_boucle, $boucles);
}

?> 
