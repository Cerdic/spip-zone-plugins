<?php

include_spip('public/boucles');
include_spip('base/echoppe');



//var_dump($tables_jointures);

function boucle_PRODUITS_dist($id_boucle, &$boucles) {
    $boucle = &$boucles[$id_boucle];
    $id_table = $boucle->id_table;
    $mstatut = $id_table .'.statut';

    // Restreindre aux elements publies
    if (!isset($boucle->modificateur['criteres']['statut'])) {
        if (!$GLOBALS['var_preview']) {
            $boucle->where[]= array("'='", "'$mstatut'", "'\\'publie\\''");
            //if ($GLOBALS['meta']["post_dates"] == 'non')
            //    $boucle->where[]= array("'<'", "'$id_table" . ".date'", "'NOW()'");
        } else {
            $boucle->where[]= array("'IN'", "'$mstatut'", "'(\\'publie\\',\\'prop\\')'");
        }
        
    }
    return calculer_boucle($id_boucle, $boucles);
}


function boucle_PRODUITS_DESCRIPTION_dist($id_boucle, &$boucles) {
    $boucle = &$boucles[$id_boucle];
    $id_table = $boucle->id_table;
    $mstatut = $id_table .'.statut';

    // Restreindre aux elements publies
    if (!isset($boucle->modificateur['criteres']['statut'])) {
        if (!$GLOBALS['var_preview']) {
            $boucle->where[]= array("'='", "'$mstatut'", "'\\'publie\\''");
            //if ($GLOBALS['meta']["post_dates"] == 'non')
            //    $boucle->where[]= array("'<'", "'$id_table" . ".date'", "'NOW()'");
        } else {
            $boucle->where[]= array("'IN'", "'$mstatut'", "'(\\'publie\\',\\'prop\\')'");
        }
        
    }
    return calculer_boucle($id_boucle, $boucles);
}


function boucle_CATEGORIES_dist($id_boucle, &$boucles) {
    $boucle = &$boucles[$id_boucle];
    $id_table = $boucle->id_table;
    $mstatut = $id_table .'.statut';

    // Restreindre aux elements publies
    /*if (!isset($boucle->modificateur['criteres']['statut'])) {
        if (!$GLOBALS['var_preview']) {
            $boucle->where[]= array("'='", "'$mstatut'", "'\\'publie\\''");
            //if ($GLOBALS['meta']["post_dates"] == 'non')
            //    $boucle->where[]= array("'<'", "'$id_table" . ".date'", "'NOW()'");
        } else {
            $boucle->where[]= array("'IN'", "'$mstatut'", "'(\\'publie\\',\\'prop\\')'");
        }
        
    }*/
    return calculer_boucle($id_boucle, $boucles);
}


function boucle_CATEGORIES_DESCRIPTION_dist($id_boucle, &$boucles) {
    $boucle = &$boucles[$id_boucle];
    $id_table = $boucle->id_table;
    $mstatut = $id_table .'.statut';

    // Restreindre aux elements publies
    /*if (!isset($boucle->modificateur['criteres']['statut'])) {
        if (!$GLOBALS['var_preview']) {
            $boucle->where[]= array("'='", "'$mstatut'", "'\\'publie\\''");
            //if ($GLOBALS['meta']["post_dates"] == 'non')
            //    $boucle->where[]= array("'<'", "'$id_table" . ".date'", "'NOW()'");
        } else {
            $boucle->where[]= array("'IN'", "'$mstatut'", "'(\\'publie\\',\\'prop\\')'");
        }
        
    }*/
    return calculer_boucle($id_boucle, $boucles);
}


?>
