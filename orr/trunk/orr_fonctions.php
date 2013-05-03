<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function orr_premierjourcalendrier($date){
	list($annee,$mois,$jour)=explode('-',$date);
    $numero_premier_jour=date("N",mktime(0,0,0,$mois,1,intval($annee)));
    return $premier_jour_calendrier=date("Y-m-d",mktime(0,0,0,$mois,1-$numero_premier_jour+1,intval($annee)));
}

function orr_plusunjour($date){
	list($annee,$mois,$jour)=explode('-',$date);
    return $date_plusun=date("Y-m-d",mktime(0,0,0,$mois,$jour+1,intval($annee)));
}

/**
 * Determination le jour des 7 jours d'une semaine connaissant la date du jeudi
 * @param date au format Y-m-d
 * @param $jourvoulu : jour de la semaine recherche
 * @param $format : jour (01 à 31) ou date au format Y-m-d H:i:s
 * @return si "jour" les 7 jours des dates (01 à 31) si "date" les 8 dates au format Y-m-d H:i:s
 **/
function orr_joursemaine($date, $jourvoulu, $format){
	list($annee,$mois,$jour)=explode('-',$date);
/**
 * cas 1 si le format est "jour"
 **/
	if ($format == "jour"){
		switch ($jourvoulu) {
			case "lundi":
				$date=date("d",mktime(0,0,0,$mois,$jour-3,intval($annee)));
				break;
			case "mardi":
				$date=date("d",mktime(0,0,0,$mois,$jour-2,intval($annee)));
				break;
			case "mercredi" :
				$date=date("d",mktime(0,0,0,$mois,$jour-1,intval($annee)));
				break;
			case "jeudi":
				$date=date("d",mktime(0,0,0,$mois,$jour,intval($annee)));
				break;
			case "vendredi":
				$date=date("d",mktime(0,0,0,$mois,$jour+1,intval($annee)));
				break;
			case "samedi":
				$date=date("d",mktime(0,0,0,$mois,$jour+2,intval($annee)));
				break;
			default:
				$date=date("d",mktime(0,0,0,$mois,$jour+3,intval($annee)));
				break;
		}
	}
/**
 * cas 2 si le format est "date"
 **/
	if ($format == "date"){
		switch ($jourvoulu) {
			case "lundi":
				$date=date("Y-m-d H:i:s", mktime (0,0,0, $mois, $jour-3, intval($annee)));
				break;
			case "mardi":
				$date=date("Y-m-d H:i:s", mktime (0,0,0, $mois, $jour-2, intval($annee)));
				break;
			case "mercredi" :
				$date=date("Y-m-d H:i:s", mktime (0,0,0, $mois, $jour-1, intval($annee)));
				break;
			case "jeudi":
				$date=date("Y-m-d H:i:s", mktime (0,0,0, $mois, $jour, intval($annee)));
				break;
			case "vendredi":
				$date=date("Y-m-d H:i:s", mktime (0,0,0, $mois, $jour+1, intval($annee)));
				break;
			case "samedi":
				$date=date("Y-m-d H:i:s", mktime (0,0,0, $mois, $jour+2, intval($annee)));
				break;
			case "dimanche":
				$date=date("Y-m-d H:i:s", mktime (0,0,0, $mois, $jour+3, intval($annee)));
				break;
			default:
				$date=date("Y-m-d H:i:s", mktime (0,0,0, $mois, $jour+4, intval($annee)));
				break;
		}
	}
/**
 * cas 3 si le format est "nom"
 **/
	if ($format == "nom"){
		switch ($jourvoulu) {
			case "lundi":
				$date=date("N",mktime(0,0,0,$mois,$jour-3,intval($annee)));
				$date=orr_traduction_jour($date);
				break;
			case "mardi":
				$date=date("N",mktime(0,0,0,$mois,$jour-2,intval($annee)));
				$date=orr_traduction_jour($date);
				break;
			case "mercredi" :
				$date=date("N",mktime(0,0,0,$mois,$jour-1,intval($annee)));
				$date=orr_traduction_jour($date);
				break;
			case "jeudi":
				$date=date("N",mktime(0,0,0,$mois,$jour,intval($annee)));
				$date=orr_traduction_jour($date);
				break;
			case "vendredi":
				$date=date("N",mktime(0,0,0,$mois,$jour+1,intval($annee)));
				$date=orr_traduction_jour($date);
				break;
			case "samedi":
				$date=date("N",mktime(0,0,0,$mois,$jour+2,intval($annee)));
				$date=orr_traduction_jour($date);
				break;
			default:
				$date=date("N",mktime(0,0,0,$mois,$jour+3,intval($annee)));
				$date=orr_traduction_jour($date);
				break;
		}
	}
	return $date;
};
/**
 * fonction de traduction entre numéro du jour et nom du jour
 * */
 function orr_traduction_jour($numero){
	 switch ($numero){
		case "1":
			 $nom = "Lundi";
			 break;
		case "2":
			$nom = "Mardi";
			break;
		case "3":
			$nom = "Mercredi";
			break;
		case "4":
			$nom = "Jeudi";
			break;
		case "5":
			$nom = "Vendredi";
			break;
		case "6":
			$nom = "Samedi";
			break;
		default:
			$nom = "Dimanche";
			break;
	 }
return $nom;
}
?>
