<?php
/**
 * Determination le jour des 7 jours d'une semaine connaissant la date d'un jour
 * quelconque de la semaine
 * @param date au format sql
 * @param $jourvoulu : jour de la semaine recherche
 * @return le numero du jour de la semaine de 01 à 31
 **/
function joursemaine($date, $jourvoulu){
/**
 * determination de la date du lundi par rapport à une date quelconque
 **/
list($annee,$mois,$jour)=explode('-',$date);
$jour_semaine = date("N", mktime(0, 0, 0, $mois, $jour, $annee));
$lundi = determlundi($jour_semaine,$mois,$jour,$annee);

/**
 * Determination de tous les jours de la semaine du lundi $lundi
 * return : les 6 jours de la semaine au format : jour de 01 à 31
 **/

list($anneel,$moisl,$jourl,$heurel,$minutel,$secl)=explode('-',$lundi);
    switch ($jourvoulu) {
        case "lundi":
            $date=date("d", mktime (0,0,0, $moisl, $jourl, $anneel));
            break;
        case "mardi":
            $date=date("d", mktime (0,0,0, $moisl, $jourl+1, $anneel));
            break;
        case "mercredi" :
            $date=date("d", mktime (0,0,0, $moisl, $jourl+2, $anneel));
            break;
        case "jeudi":
            $date=date("d", mktime (0,0,0, $moisl, $jourl+3, $anneel));
            break;
        case "vendredi":
            $date=date("d", mktime (0,0,0, $moisl, $jourl+4, $anneel));
            break;
        case "samedi":
            $date=date("d", mktime (0,0,0, $moisl, $jourl+5, $anneel));
            break;
        default:
            $date=date("d", mktime (0,0,0, $moisl, $jourl+6, $anneel));
            break;
    }
return $date;
};
/**
 * Determination le jour des 7 jours d'une semaine connaissant la date d'un jour
 * quelconque de la semaine
 * @param date au format sql
 * @param $jourvoulu : jour de la semaine recherche
 * @return le numero du jour de la semaine de 01 à 31
 **/
function datesemaine($date, $jourvoulu){
/**
 * determination de la date du lundi par rapport à une date quelconque
 **/
list($annee,$mois,$jour)=explode('-',$date);
//$date = $mois.'-'.$jour.'-'.$annee;
$jour_semaine = date("N", mktime(0, 0, 0, $mois, $jour, $annee));
$lundi = determlundi($jour_semaine,$mois,$jour,$annee);

/**
 * Determination de tous les jours de la semaine du lundi $lundi
 * return : les 6 jours de la semaine au format : jour de 01 à 31
 **/

list($anneel,$moisl,$jourl,$heurel,$minutel,$secl)=explode('-',$lundi);
    switch ($jourvoulu) {
        case "lundi":
            $date=date("Y-m-d", mktime (0,0,0, $moisl, $jourl, $anneel));
            break;
        case "mardi":
            $date=date("Y-m-d", mktime (0,0,0, $moisl, $jourl+1, $anneel));
            break;
        case "mercredi" :
            $date=date("Y-m-d", mktime (0,0,0, $moisl, $jourl+2, $anneel));
            break;
        case "jeudi":
            $date=date("Y-m-d", mktime (0,0,0, $moisl, $jourl+3, $anneel));
            break;
        case "vendredi":
            $date=date("Y-m-d", mktime (0,0,0, $moisl, $jourl+4, $anneel));
            break;
        case "samedi":
            $date=date("Y-m-d", mktime (0,0,0, $moisl, $jourl+5, $anneel));
            break;
        default:
            $date=date("Y-m-d", mktime (0,0,0, $moisl, $jourl+6, $anneel));
            break;
    }
return $date;
};
/**
 * Ajoute 1 jour à la date
 * @param  la date au format sql sans les heures : 2012-07-03
 * @return la date + 1 jour au meme format
 **/
function dateplusun($date){
list($annee,$mois,$jour)=explode('-',$date);
return date("Y-m-d", mktime(0, 0, 0, $mois, $jour+1, $annee));
}
/*
 * fonction qui determine le lundi d'une semaine
 * */
function determlundi($jour_semaine,$mois,$jour,$annee){
         switch ($jour_semaine) {
    case '1':
        $lundi=date("Y-m-d H:i:s", mktime (0,0,0, $mois, $jour, $annee));
        break;
    case '2':
        $lundi=date("Y-m-d H:i:s", mktime (0,0,0, $mois, $jour-1, $annee));
        break;
    case '3':
        $lundi=date("Y-m-d H:i:s", mktime (0,0,0, $mois, $jour-2, $annee));
        break;
    case '4':
        $lundi=date("Y-m-d H:i:s", mktime (0,0,0, $mois, $jour-3, $annee));
        break;
    case '5':
        $lundi=date("Y-m-d H:i:s", mktime (0,0,0, $mois, $jour-4, $annee));
        break;
    case '6':
        $lundi=date("Y-m-d H:i:s", mktime (0,0,0, $mois, $jour-5, $annee));
        break;
    default:
        $lundi=date("Y-m-d H:i:s", mktime (0,0,0, $mois, $jour-6, $annee));
        break;
}
return $lundi;
    }
?>
