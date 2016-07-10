<?php
if (!defined('_ECRIRE_INC_VERSION')) return;
/**
* Importation d'un événement dans la base
**/
function importation_evenement($objet_evenement,$id_almanach){
	#on recupère les données de décalage
		$decalage = _request('decalage');
	#on recupere les infos de l'evenement dans des variables
	    $attendee = $objet_evenement->getProperty( "attendee" ); #nom de l'attendee
	    $lieu = $objet_evenement->getProperty("location");#récupération du lieu
	    $summary_array = $objet_evenement->getProperty("summary", 1, TRUE); #summary est un array on recupere la valeur dans l'insertion attention, summary c'est pour le titre !
		$titre_evt=str_replace('SUMMARY:', '', $summary_array["value"]);
		$url = $objet_evenement->getProperty( "URL");#on récupère l'url de l'événement pour la mettre dans les notes histoire de pouvoir relier à l'événement original
	    $descriptif_array = $objet_evenement->getProperty("DESCRIPTION", 1,TRUE);
	    $organizer = $objet_evenement->getProperty("ORGANIZER");#organisateur de l'evenement
	#données de localisation de l'évenement
	    $localisation = $objet_evenement->getProperty( "GEO" );#c'est un array array( "latitude"  => <latitude>, "longitude" => <longitude>))
	    $latitude = $localisation['latitude'];
	    $longitude = $localisation['longitude'];
	//un petit coup avec l'uid
	    $uid_distante = $objet_evenement->getProperty("UID");#uid de l'evenement
	#les 3 lignes suivantes servent à récupérer la date de début et à la mettre dans le bon format
	    $dtstart_array = $objet_evenement->getProperty("dtstart", 1, TRUE); 
	    	$dtstart = $dtstart_array["value"];
   			$startDate = "{$dtstart["year"]}-{$dtstart["month"]}-{$dtstart["day"]}";
   			$startTime = '';#on initialise l'heure' de début
   			$heure_debut = $dtstart["hour"]+$decalage;
    		if (!in_array("DATE", $dtstart_array["params"])) {
       			 $startTime = " $heure_debut:{$dtstart["min"]}:{$dtstart["sec"]}";
    			}
    		#on fait une variable qui contient le résultat des deux précédentes actions
    		$date_debut = $startDate.$startTime;
	#les 3 lignes suivantes servent à récupérer la date de fin et à la mettre dans le bon format
  		$dtend_array = $objet_evenement->getProperty("dtend", 1, TRUE);
   			$dtend = $dtend_array["value"];
    		$endDate = "{$dtend["year"]}-{$dtend["month"]}-{$dtend["day"]}";
    		$endTime = '';#on initialise l'heure' de fin
   			$heure_fin = $dtend["hour"]+$decalage;
    		if (!in_array("DATE", $dtend_array["params"])) {
       			$endTime = " $heure_fin:{$dtend["min"]}:{$dtend["sec"]}";
    			}
    		#on fait une variable qui contient le résultat des deux précédentes actions
    		$date_fin = $endDate.$endTime;
	#on insere les infos des événements dans la base 
	# ca ce sera pour quand j'arriverai à faire fonctionner le selecteur d'articles $id_article = preg_replace('(article\|)','',_request('id_article')); #le selecteur d'article fournit un tableau, on se débarasse du mot article dedans et on appellera ensuite la première valeur (il pourrait y avoir des saisies multiples même si ici on ne les autorise pas)
	$id_mot = _request('id_mot');
	$id_article = _request('id_article'); 
	$id_evenement= sql_insertq('spip_evenements',
	  array(
			'id_article' =>$id_article,
		  'date_debut'=>$date_debut,
		  'date_fin'=>$date_fin,
			'titre'=>$titre_evt,
			'descriptif'=>'<math>'.$descriptif_array["value"].'</math>',
			'lieu'=>$lieu,'adresse'=>'',
			'inscription'=>'0',
			'places'=>'0',
			'horaire'=>'oui',
			'statut'=>'publie',
			'attendee'=>str_replace('MAILTO:', '', $attendee),
			'id_evenement_source'=>'0',
			'uid'=>$uid_distante,
			'sequence'=>$sequence_distante,
			'notes'=>$url));

	#on associe l'événement à l'almanach
	#objet_associer(array('almanach'=>$id_almanach),array('evenement'=>$id_evenement),array('vu'=>'oui'));
	sql_insertq("spip_almanachs_liens",array('id_almanach'=>$id_almanach,'id_objet'=>$id_evenement,'objet'=>'evenement','vu'=>'oui'));
	#on associe l'événement à son mot
	sql_insertq("spip_mots_liens",array('id_mot'=>$id_mot,'id_objet'=>$id_evenement,'objet'=>'evenement'));
	#on ajoute la resa si on le doit
	if ((_request("id_ressource"))>0) {
		$id_ressource=_request("id_ressource");
		ajout_resa($titre_evt,$id_ressource,$date_debut,$date_fin);
	}
}


/**
*ajout d'une reservation à l'événement si c'est coché
**/

function ajout_resa($titre_evt,$id_ressource,$date_debut,$date_fin){
	$id_orr_reservation = sql_insertq("spip_orr_reservations",array('orr_reservation_nom'=>$titre_evt,'orr_date_debut'=>$date_debut,'orr_date_fin'=>$date_fin));
	sql_insertq("spip_orr_reservations_liens",array('id_orr_reservation'=>$id_orr_reservation,'id_objet'=>$id_ressource,'objet'=>'orr_ressource','vu'=>'non'));
echo "ajout résa";

}
?>
