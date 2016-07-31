<?php
if (!defined('_ECRIRE_INC_VERSION')) return;
/**
* Importation ou synchronisation d'un almanach
**/

include_spip('lib/iCalcreator.class'); /*pour la librairie icalcreator incluse dans le plugin icalendar*/
include_spip('inc/autoriser');
include_spip('action/editer_objet');
include_spip('action/editer_liens');

function importer_almanach($id_almanach,$url,$id_article,$id_mot,$decalage){


	// Début de la récupération des évènements
	#configuration nécessaire à la récupération
	$config = array("unique_id"=>"","url"=>$url);
	$cal = new vcalendar($config);
	$cal->parse();
	//ON fait un appel dans la base de spip pour vpouvoir vérifier si un événement y est déjà (ça ne se fait pas en une ligne...)
	$liens = sql_allfetsel('uid', 'spip_evenements');
	// on definit un tableau des uid présentes dans la base
	$uid =array();
	foreach ($liens as $u ) {
		$uid[] = $u['uid'];
	};
	while ($comp = $cal->getComponent()){
			#les variables qui vont servir à vérifier l'existence et l'unicité 
			$uid_distante = $comp->getProperty("UID");#uid de l'evenement
			$last_modified_distant = $comp->getProperty("LAST-MODIFIED");
			$sequence_distant = $comp->getProperty("SEQUENCE");

			//vérifier l'existence et l'unicité
			if (in_array($uid_distante, $uid)){//si l'uid_distante est présente dans la bdd, alors on teste si l'evenement a été modifié à distance
				$test_variation = sql_fetsel("last_modified_distant,sequence,id_evenement",
					"spip_evenements",
					"`uid`=".sql_quote($uid_distante)
				);
				$last_modified_local = unserialize($test_variation["last_modified_local"]);
				$sequence_local = $test_variation["sequence"];
				$id_evenement = $test_variation["id_evenement"];
				if ($last_modified_local!=$last_modified_distant or $sequence_local!=$sequence_distant){
						$champs_sql = evenement_ical_to_sql($comp);
						autoriser_exception('evenement','modifier',$id_evenement);
						objet_modifier('evenement',$id_evenement,$champs_sql);
						autoriser_exception('evenement','modifier',$id_evenement,false);
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
  $champs_sql = array_merge(
		evenement_ical_to_sql($objet_evenement),
		array("id_article"=>$id_article)
	);
	
	# création de l'evt
	autoriser_exception('creer','evenement','');
  $id_evenement= objet_inserer('spip_evenements',$id_article,$champs_sql);
	autoriser_exception('creer','evenement','',false);
	
	autoriser_exception('instituer','evenement',$id_evenement);
	autoriser_exception('modifier','article',$id_article);
	objet_instituer('evenement',$id_evenement,array("statut"=>'publie'));
	autoriser_exception('instituer','evenement',$id_evenement,false);
	autoriser_exception('modifier','article',$id_article,false);

	
	#on associe l'événement à l'almanach
	objet_associer(array('almanach'=>$id_almanach),array('evenement'=>$id_evenement),array('vu'=>'oui'));	
	#on associe l'événement à son mot
	if ($id_mot){
	  autoriser_exception('associermots','evenement',$id_evenement);
		objet_associer(array("mot"=>$id_mot),array("evenement"=>$id_evenement));
		autoriser_exception('associermots','evenement',$id_evenement,false);
	}
}

/* 
** Récupérer les propriétés d'un evenements de sorte qu'on puisse en faire la requete sql
*/
function evenement_ical_to_sql($objet_evenement){
	
		#on recupere les infos de l'evenement dans des variables
		    $attendee = $objet_evenement->getProperty( "attendee" ); #nom de l'attendee
		    $lieu = $objet_evenement->getProperty("location");#récupération du lieu
		    $summary_array = $objet_evenement->getProperty("summary", 1, TRUE); #summary est un array on recupere la valeur dans l'insertion attention, summary c'est pour le titre !
				$titre_evt=str_replace('SUMMARY:', '', $summary_array["value"]);
				$url = $objet_evenement->getProperty( "URL");#on récupère l'url de l'événement pour la mettre dans les notes histoire de pouvoir relier à l'événement original
		    $descriptif_array = $objet_evenement->getProperty("DESCRIPTION", 1,TRUE);
		    $organizer = $objet_evenement->getProperty("ORGANIZER");#organisateur de l'evenement
				$last_modified_distant = serialize($objet_evenement->getProperty("LAST-MODIFIED"));
				$sequence_distante = $objet_evenement->getProperty("SEQUENCE");
				if (is_null($sequence_distante)){
					$sequence_distante = 0;
				}
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
		
		return array(
		  'date_debut'=>$date_debut,
		  'date_fin'=>$date_fin,
			'titre'=>$titre_evt,
			'descriptif'=>$descriptif_array["value"],
			'lieu'=>$lieu,'adresse'=>'',
			'inscription'=>'0',
			'places'=>'0',
			'horaire'=>$horaire,
			'attendee'=>str_replace('MAILTO:', '', $attendee),
			'id_evenement_source'=>'0',
			'uid'=>$uid_distante,
			'sequence'=>$sequence_distante,
			'last_modified_distant'=>$last_modified_distant,
			'notes'=>$url);
}

?>