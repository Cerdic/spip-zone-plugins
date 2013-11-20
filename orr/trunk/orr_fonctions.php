<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * récupère la valeur d'un champ extra d'une resa
 */
function valeur_champs_extra($nom_champ,$id_resa){
    return sql_getfetsel($nom_champ, 'spip_orr_reservations', 'id_orr_reservation='.intval($id_resa) ); 
}

function orr_premierjourcalendrier($date){
	list($annee,$mois,$jour) = explode('-',$date);
    $numero_premier_jour = date("N",mktime(0,0,0,$mois,1,intval($annee)));
    return date("Y-m-d",mktime(0,0,0,$mois,1-$numero_premier_jour+1,intval($annee)));
}

function orr_plusunjour($date){
	list($annee,$mois,$jour) = explode('-',$date);
    return date("Y-m-d",mktime(0,0,0,$mois,$jour+1,intval($annee)));
}

/**
 * Determine le jour des 7 jours d'une semaine en connaissant la date du jeudi
 * @param date au format Y-m-d
 * @param $jourvoulu : jour de la semaine recherche
 * @param $format : jour (01 à 31) ou date au format Y-m-d H:i:s
 * @return si jour les 7 jours des dates (01 à 31) 
 * 		si date les 8 dates au format Y-m-d H:i:s
 * 		si nom le jour de la semaine
 **/
function orr_joursemaine($date, $jourvoulu, $format){
	list($annee, $mois, $jour) = explode('-', $date);
	switch ($format) {
		case "jour":
			$format_mktime = "d";
			break;
		case "nom":
			$format_mktime = "N";
			break;
		default:	// format envoyé = date
			$format_mktime = "Y-m-d H:i:s";
			break;
	}
	switch ($jourvoulu) {
		case "lundi":
			$date = date($format_mktime, mktime(0,0,0,$mois,$jour-3, intval($annee)));
			break;
		case "mardi":
			$date = date($format_mktime, mktime(0,0,0,$mois,$jour-2, intval($annee)));
			break;
		case "mercredi" :
			$date = date($format_mktime, mktime(0,0,0,$mois,$jour-1, intval($annee)));
			break;
		case "jeudi":
			$date = date($format_mktime, mktime(0,0,0,$mois,$jour, intval($annee)));
			break;
		case "vendredi":
			$date = date($format_mktime, mktime(0,0,0,$mois,$jour+1, intval($annee)));
			break;
		case "samedi":
			$date = date($format_mktime, mktime(0,0,0,$mois,$jour+2, intval($annee)));
			break;
		case"dimanche":
			$date = date($format_mktime, mktime(0,0,0,$mois,$jour+3, intval($annee)));
			break;
	}
	if ($format == "nom")
		$date = orr_traduction_jour($date);
		
	return $date;
};
		
/**
 * fonction de traduction entre numéro du jour et nom du jour
 * */
 function orr_traduction_jour($numero){
	 $Tjours = array("","Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche" );
	 return $Tjours[intval($numero)];
}

/**
 * comparaison d'une date de début et fin pour cohérence entre elles
 * et si nécessaire pour cohérence avec celles d'une résa en cours
 */
function orr_compare_date($date_debut, $date_fin, $idressource, $idresa){
	include_spip('base/abstract_sql');
	if ($idresa > 0){
		if ($result = sql_select(
				array(
				"reservation.orr_date_debut",
				"reservation.orr_date_fin"),
				array(
				"spip_orr_reservations AS reservation",
				"spip_orr_reservations_liens AS lien",
				"spip_orr_ressources AS ressource"),
				array(
				"reservation.id_orr_reservation=lien.id_orr_reservation",
				"ressource.id_orr_ressource=lien.id_objet",
				"lien.objet='orr_ressource'",
				"ressource.id_orr_ressource=$idressource",
				"reservation.id_orr_reservation<>$idresa")
			)){
			while ($r = sql_fetch($result)){
				$retour = 0;
				// date_debut et date_fin à l'interieur sachant que date_debut peut etre egale orr_date_fin et que date_fin peut etre égale à orr_date_debut
				if (($r[orr_date_debut] <= $date_debut) and ($r[orr_date_fin]>=$date_fin)){
					$retour = 1;
					break;
				}
				// date_debut < à orr_date_debut et date_fin > orr_date_debut
				if (($r[orr_date_debut] >= $date_debut) and ($r[orr_date_debut]<$date_fin)){
					$retour = 1;
					break;
				}
				// date_fin > date de fin et que ma date debut < date de fin
				if (($r[orr_date_fin] > $date_debut) and ($r[orr_date_fin]<$date_fin)) {
					$retour = 1;
					break;
				}
			}
		}
	}
	else {
		if ($result = sql_select(
				array(
				"reservation.orr_date_debut",
				"reservation.orr_date_fin"),
				array(
				"spip_orr_reservations AS reservation",
				"spip_orr_reservations_liens AS lien",
				"spip_orr_ressources AS ressource"),
				array(
				"reservation.id_orr_reservation=lien.id_orr_reservation",
				"ressource.id_orr_ressource=lien.id_objet",
				"lien.objet='orr_ressource'",
				"ressource.id_orr_ressource=$idressource")
			)){
			while ($r = sql_fetch($result)){
				$retour = 0;
				// date_debut et date_fin à l'interieur sachant que date_debut peut etre egale orr_date_fin et que date_fin peut etre égale à orr_date_debut
				if (($r[orr_date_debut] <= $date_debut) and ($r[orr_date_fin]>=$date_fin)){
					$retour = 1;
					break;
				}
				//~ date_debut < à orr_date_debut et date_fin > orr_date_debut
				if (($r[orr_date_debut] >= $date_debut) and ($r[orr_date_debut]<$date_fin)){
					$retour = 1;
					break;
				}
				// date_fin > date de fin et que ma date debut < date de fin
				if (($r[orr_date_fin] > $date_debut) and ($r[orr_date_fin]<$date_fin)) {
					$retour = 1;
					break;
				}
			}
		}
	}
	return $retour;
}

/*
 * Récupération du nom des champs extra d'une table
 */
function orr_nom_champs_extra($nom_table){
	include_spip("inc/config");
	$Tchamps = array();
	$Ttout = lire_config("champs_extras_".$nom_table);
	foreach ($Ttout as $champ)
		$Tchamps[] = $champ['options']['nom'];
	return $Tchamps;
}

?>
