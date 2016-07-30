<?php
if (!defined('_ECRIRE_INC_VERSION')) return;
/**
* Importation ou synchronisation d'un almanach
**/

include_spip('lib/iCalcreator.class'); /*pour la librairie icalcreator incluse dans le plugin icalendar*/


function importer_almanach($id_almanach,$url,$id_article,$id_mot,$decalage){


	// Début de la récupération des évènements
	#configuration nécessaire à la récupération
	$config = array("unique_id"=>"","url"=>$url);
	$cal = new vcalendar($config);
	$cal->parse();
	//ON fait un appel dans la base de spip pour vpouvoir vérifier si un événement y est déjà (ça ne se fait pas en une ligne...)
	$liens = sql_allfetsel('id_evenement, uid, sequence', 'spip_evenements');
	// on definit un tableau des uid présentes dans la base
	$uid =array();
	foreach ($liens as $u ) {
		$uid[] = $u['uid'];
	};
	while ($comp = $cal->getComponent()){
			#les variables qui vont servir à vérifier l'existence et l'unicité 
			$sequence_distante = $comp->getProperty( "SEQUENCE" );#sequence d l'evenement http://kigkonsult.se/iCalcreator/docs/using.html#SEQUENCE
			$uid_distante = $comp->getProperty("UID");#uid de l'evenement
			if (!is_int($sequence_distante)){$sequence_distante="0";}//au cas où le flux ics ne fournirait pas le champ sequence, on initialise la valeur à 0 comme lors d'un import
			//est-ce que c'est un googlecal ? Dans ce cas, on a un traitement un peu particulier

			//On commence à vérifier l'existence et l'unicité  maintenant et on met à jour ou on importe selon le cas
			if (in_array($uid_distante, $uid)){//si l'uid_distante est présente dans la bdd
					$cle = array_search($uid_distante, $uid); // on utilise le fait que les deux tableaux ont le même index pour le récupérer
					$sequence = $liens[$cle]['sequence'];//sequence presente dans la base ayant le meme index

					if ($sequence < $sequence_distante ){//si la sequecne de la bdd est plus petite, il y a eu mise à jour et il faut intervenir
					} 
				} 
			else {
				importer_evenement($comp,$id_almanach,$id_article,$id_mot,$decalage);
			};//l'evenement n'est pas dans la bdd, on va l'y mettre	
		}
}

/**
* Importation d'un événement dans la base
**/
function importer_evenement($objet_evenement,$id_almanach,$id_article,$id_mot,$decalage){

	#on recupere les infos de l'evenement dans des variables
	    $attendee = $objet_evenement->getProperty( "attendee" ); #nom de l'attendee
	    $lieu = $objet_evenement->getProperty("location");#récupération du lieu
	    $summary_array = $objet_evenement->getProperty("summary", 1, TRUE); #summary est un array on recupere la valeur dans l'insertion attention, summary c'est pour le titre !
			$titre_evt=str_replace('SUMMARY:', '', $summary_array["value"]);
			$url = $objet_evenement->getProperty( "URL");#on récupère l'url de l'événement pour la mettre dans les notes histoire de pouvoir relier à l'événement original
	    $descriptif_array = $objet_evenement->getProperty("DESCRIPTION", 1,TRUE);
	    $organizer = $objet_evenement->getProperty("ORGANIZER");#organisateur de l'evenement
			$last_modified_distant = serialize($objet_evenement->getProperty("LAST-MODIFIED"));

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
						 $start_all_day = False;
    			}
				else{
					$start_all_day = True;
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
						$end_all_day = False;
    			}
				else{
					$end_all_day = True;			
				}
    		#on fait une variable qui contient le résultat des deux précédentes actions
    		$date_fin = $endDate.$endTime;

		// Est-ce que l'evt dure toute la journée?
		if ($end_all_day and $start_all_day){
			$horaire = "non";
		}
		else{
			$horaire = "oui";
		}
	  $id_evenement= sql_insertq('spip_evenements',
	  array(
			'id_article' =>$id_article,
		  'date_debut'=>$date_debut,
		  'date_fin'=>$date_fin,
			'titre'=>$titre_evt,
			'descriptif'=>$descriptif_array["value"],
			'lieu'=>$lieu,'adresse'=>'',
			'inscription'=>'0',
			'places'=>'0',
			'horaire'=>$horaire,
			'statut'=>'publie',
			'attendee'=>str_replace('MAILTO:', '', $attendee),
			'id_evenement_source'=>'0',
			'uid'=>$uid_distante,
			'sequence'=>$sequence_distante,
			'last_modified_distant'=>$last_modified_distant,
			'notes'=>$url));

	#on associe l'événement à l'almanach
	#objet_associer(array('almanach'=>$id_almanach),array('evenement'=>$id_evenement),array('vu'=>'oui'));
	sql_insertq("spip_almanachs_liens",array('id_almanach'=>$id_almanach,'id_objet'=>$id_evenement,'objet'=>'evenement','vu'=>'oui'));
	#on associe l'événement à son mot
	if ($id_mot){
	  sql_insertq("spip_mots_liens",array('id_mot'=>$id_mot,'id_objet'=>$id_evenement,'objet'=>'evenement'));
  }
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