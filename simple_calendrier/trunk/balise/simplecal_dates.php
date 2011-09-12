<?php
/**
 * Plugin Simple Calendrier pour Spip 2.1.2
 * Licence GPL (c) 2010-2011 Julien Lanfrey
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_DATE_JOUR($p) { 
    $date_now = date('Y-m-d H:i:s');
    
    $p->code = "'$date_now'"; 
    return $p; 
}

function balise_DATE_PREMIER_DU_MOIS($p) { 
    $date = date('Y-m-01');
    $p->code = "'$date'"; 
    return $p; 
}

function balise_DATE_DERNIER_DU_MOIS($p) { 
    $date = date('Y-m-31');
    $p->code = "'$date'"; 
    return $p; 
}

function balise_DATE_JOUR_PLUS($p) { 
    $date_now = date('Y-m-d H:i:s');
    
    $jour = jour($date_now);
    $mois = mois($date_now);
    $annee = annee($date_now);
    $heure = 0;
    $minute = 0;
    $seconde = 0;
        
    $nb_jour = intval($p->param[0][1][0]->texte);
    $date_plus = date("Y-m-d", mktime($heure, $minute, $seconde, $mois, $jour+$nb_jour, $annee));

    $p->code = "'$date_plus'"; return $p; 
}

function balise_DATE_JOUR_MOINS($p) { 
    $date_now = date('Y-m-d H:i:s');
    
    $jour = jour($date_now);
    $mois = mois($date_now);
    $annee = annee($date_now);
    $heure = 0;
    $minute = 0;
    $seconde = 0;
        
    $nb_jour = intval($p->param[0][1][0]->texte);
    $date_plus = date("Y-m-d", mktime($heure, $minute, $seconde, $mois, $jour-$nb_jour, $annee));
    
    $p->code = "'$date_plus'"; return $p; 
}

?>
