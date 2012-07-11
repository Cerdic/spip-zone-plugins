<?php
/*
 *dertermination de la date du lundi par rapport à une date quelconque
 *@param  $date : date issue du datepicker (variable dateText) format : yy-mm-dd
 *@return le nom et le date (numero du jour) des 7 jours de la semaine
 * ainsi que le mois au format mois.nom
 **/

$date = $_REQUEST["date"];
list($annee,$mois,$jour)=explode('-',$date);
$date = $mois.'-'.$jour.'-'.$annee;
$jour_semaine = date("N", mktime(0, 0, 0, $mois, $jour, $annee));
$chiffre_mois = date("m", mktime(0, 0, 0, $mois, $jour, $annee));

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
/**
 * Determination de tous les jours de la semaine du lundi $lundi
 * return : les 6 jours de la semaine (sauf lundi)
 **/

list($annee,$mois,$jour)=explode('-',$lundi);
$lundic    = date("d", mktime (0,0,0, $mois, $jour, $annee));
$mardic    = date("d", mktime (0,0,0, $mois, $jour+1, $annee));
$mercredic = date("d", mktime (0,0,0, $mois, $jour+2, $annee));
$jeudic    = date("d", mktime (0,0,0, $mois, $jour+3, $annee));
$vendredic = date("d", mktime (0,0,0, $mois, $jour+4, $annee));
$samedic   = date("d", mktime (0,0,0, $mois, $jour+5, $annee));
$dimanchec = date("d", mktime (0,0,0, $mois, $jour+6, $annee));

/**
 * determination du mois
 **/
switch ($chiffre_mois) {
    case '1':
        $nom_mois = "Janvier";
        break;
    case '2':
        $nom_mois = "Février";
        break;
    case '3':
        $nom_mois = "Mars";
        break;
    case '4':
        $nom_mois = "Avril";
        break;
    case '5':
        $nom_mois = "Mai";
        break;
    case '6':
        $nom_mois = "Juin";
        break;
    case '7':
        $nom_mois = "Juillet";
        break;
    case '8':
        $nom_mois = "Août";
        break;
    case '9':
        $nom_mois = "Septembre";
        break;
    case '10':
        $nom_mois = "Octobre";
        break;
    case '11':
        $nom_mois = "Novembre";
        break;
    default:
        $nom_mois = "Décembre";
        break;
}

$txt = "<div><p>Lundi_$lundic</p><p>Mardi_$mardic</p><p>Mercredi_$mercredic</p><p>Jeudi_$jeudic</p><p>Vendredi_$vendredic</p><p>Samedi_$samedic</p><p>Dimanche_$dimanchec</p><p>$nom_mois</p></div>";
echo $txt;

?>
