<?php
/**
 * Plugin Simple Calendrier pour Spip 2.1.2
 * Licence GPL (c) 2010-2011 Julien Lanfrey
 *
 */

/**
 * Critere {a_venir} 
 *
 * @param unknown_type $idb
 * @param unknown_type $boucles
 * @param unknown_type $crit
 */

function critere_a_venir_dist($idb, &$boucles, $crit) {
    $boucle = &$boucles[$idb];
    $table = $boucle->id_table;
    $not = $crit->not;
    
    $c = array("'OR'",
            array("'>='", "'$table.date_debut'", "sql_quote(date('Y-m-d'))"),
            array("'>='", "'$table.date_fin'", "sql_quote(date('Y-m-d'))"));
    
    // Inversion de la condition ?
    $c = ($not ? array("'NOT'", $c) : $c);
    
    $boucle->where[] = $c;
}

/**
 * Critere {a_venir} 
 *
 * @param unknown_type $idb
 * @param unknown_type $boucles
 * @param unknown_type $crit
 */
function critere_du_mois_dist($idb, &$boucles, $crit) {
    $boucle = &$boucles[$idb];
    $table = $boucle->id_table;
    $not = $crit->not;
    
    $c = array("'OR'",
        array("'AND'",
            array("'>='", "'$table.date_debut'", "sql_quote(date('Y-m-01'))"),
            array("'<='", "'$table.date_debut'", "sql_quote(date('Y-m-31'))")
        ),
        array("'AND'",
            array("'>='", "'$table.date_fin'", "sql_quote(date('Y-m-01'))"),
            array("'<='", "'$table.date_fin'", "sql_quote(date('Y-m-31'))")
        )
    );
    
    // Inversion de la condition ?
    $c = ($not ? array("'NOT'", $c) : $c);
        
    $boucle->where[] = $c;
}



// {simplecalperiode date_debut, #ENV{periodedebut}, #ENV{periodefin}}
// Format aaaammjj
function critere_simplecalperiode_dist($idb, &$boucles, $crit) {
    $boucle = &$boucles[$idb];
    $table = $boucle->id_table;
    $not = $crit->not;
    
    $parent = $boucles[$idb]->id_parent;
    $params = $crit->param;
    // ---
    
    $log = '';
        
    
    // 'date_debut' - inutile...
    $p0 = $params ? array_shift($params) : "";
    
    // aaaammjj
    $px = $params ? array_shift($params) : "";
    $pdeb = "\n" . 'sprintf("%08d", ($x = '.calculer_liste($px, array(), $boucles, $parent).') ? $x : date("Ymd"))';
    
    // aaaammjj
    $px = $params ? array_shift($params) : "";
    $pfin = "\n" . 'sprintf("%08d", ($x = '.calculer_liste($px, array(), $boucles, $parent).') ? $x : date("Ymd"))';
    
    // ----
    
    $date_debut = $table . ".date_debut";
    $date_fin = $table . ".date_fin";
    
    //    date_debut comprise dans la periode
    // OU date_fin   comprise dans la periode
    $c = array("'OR'",
        array("'AND'",
            array("'>='", "'DATE_FORMAT($date_debut, \'%Y%m%d\')'", ("$pdeb")),
            array("'<='", "'DATE_FORMAT($date_debut, \'%Y%m%d\')'", ("$pfin"))
        ),
        array("'AND'",
            array("'>='", "'DATE_FORMAT($date_fin, \'%Y%m%d\')'", ("$pdeb")),
            array("'<='", "'DATE_FORMAT($date_fin, \'%Y%m%d\')'", ("$pfin"))
        )
    );
    
   
    // Inversion de la condition ?
    $c = ($not ? array("'NOT'", $c) : $c);
        
    $boucle->where[] = $c;
}

?>