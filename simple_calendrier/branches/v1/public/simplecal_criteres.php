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

?>