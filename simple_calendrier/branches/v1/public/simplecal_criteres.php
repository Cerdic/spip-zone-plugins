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
    
    $date_now = date('Y-m-d'); // 'Y-m-d H:i:s'
    
    /*
    if ($not){
        $c = "'($table.date_debut < \'$date_now\' AND $table.date_fin < \'$date_now\')'";
    } else {
        $c = "'($table.date_debut >= \'$date_now\' OR $table.date_fin >= \'$date_now\')'";
    }
    */
    
    $c = array("'OR'",
            array("'>='", "'$table.date_debut'", "'\'$date_now\''"),
            array("'>='", "'$table.date_fin'", "'\'$date_now\''"));
    
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
    
    $date_premier = date('Y-m-01');
    $date_dernier = date('Y-m-31'); // meme pas faux (pour la comparaison) ...
    
    
    /* c'est pareil ! */
    /*
    $c = "";
    $c .= "'(";
    $c .= "($table.date_debut >= \'$date_premier\' AND $table.date_debut <= \'$date_dernier\')"; 
    $c .= " OR ";
    $c .= "($table.date_fin >= \'$date_premier\' AND $table.date_fin <= \'$date_dernier\')";
    $c .= ")'";
    */   
    
    $c = array("'OR'",
        array("'AND'",
            array("'>='", "'$table.date_debut'", "'\'$date_premier\''"),
            array("'<='", "'$table.date_debut'", "'\'$date_dernier\''")
        ),
        array("'AND'",
            array("'>='", "'$table.date_fin'", "'\'$date_premier\''"),
            array("'<='", "'$table.date_fin'", "'\'$date_dernier\''")
        )
    );
    
    // Inversion de la condition ?
    $c = ($not ? array("'NOT'", $c) : $c);
        
    $boucle->where[] = $c;
}

?>