<?php

/**
 * Gestion du génie import_ics_synchro
 *
 * @plugin import_ics pour SPIP
 * @license GPL
 * 
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Actualise tous les almanachs
 *
 * @genie import_ics_synchro
 *
 * @param int $last
 *     Timestamp de la dernière exécution de cette tâche
 * @return int
 *     Positif : la tâche a été effectuée
 */
function genie_import_ics_synchro_dist($t){

//on recupère toutes les infos sur les almanachs
if(
	$resultats = sql_allfetsel('*', 'spip_almanachs')
	and is_array($resultats)
)
{
//librairie icalcreator incluse dans le plugin icalendar
include_spip('lib/iCalcreator.class');

//pour chacun des almanachs, on va traiter les différences
foreach ($resultats as $r) {
		//	on va faire une sélection des evenemnts associés à l'almanach en cours 
		//donc jointure sur les table spip_evenemnts et spip_almanachs_liens
		$evenements_lies = sql_allfetsel('E.uid, E.id_evenement, E.sequence',
			'spip_evenements AS E 
			INNER JOIN spip_almanachs_liens AS L
			ON E.id_evenement = L.id_objet AND L.id_almanach='.intval($r['id_almanach']));

		//tableau des uid associés à cet almanach tiré du tableau précédent
			$uid ="";
			foreach ($evenements_lies as $u ) {
				$uid[] = $u['uid'];
			};

		//configuration nécessaire à la récupération et parsing du calendrier distant

			$config = array("unique_id" => "distant",
							"url" => $r['url']);
			$v = new vcalendar($config);
			$v->parse();

			echo "almanach".$r["id_almanach"]."</br>";
			while ($comp = $v->getComponent())
			{
		//les variables qui vont servir à vérifier l'existence et l'unicité 
			   	$sequence_distante = $comp->getProperty( "SEQUENCE" );#sequence d l'evenement http://kigkonsult.se/iCalcreator/docs/using.html#SEQUENCE
				$uid_distante = $comp->getProperty("UID");#uid de l'evenement;
				//au cas où le flux ics ne fournirait pas le champ sequence, on initialise la valeur à 0 comme lors d'un import
				if (!is_int($sequence_distante)){$sequence_distante="0";}
				//On commence à vérifier l'existence et l'unicité  maintenant et on met à jour 
				//ou on importe selon le cas
				if (in_array($uid_distante, $uid)){//si l'uid_distante est présente dans la bdd
					// on utilise le fait que les deux tableaux ont le même index pour le récupérer
					$cle = array_search($uid_distante, $uid); 
					//sequence presente dans la base ayant le meme index
					$sequence = $evenements_lies[$cle]['sequence'];
					if ($sequence < $sequence_distante) {
						importation_evenement($comp,$r);
					}
				} else {importation_evenement($comp,$r);}
			}
		}
}

return 1;

}


/**
* Importation d'un événement dans la base
**/
function importation_evenement($objet_evenement,$tableau_almanach){
	#on recupere les infos de l'evenement dans des variables
	    $attendee = $objet_evenement->getProperty( "attendee" ); #nom de l'attendee
	    $lieu = $objet_evenement->getProperty("location");#récupération du lieu
	    $summary_array = $objet_evenement->getProperty("summary", 1, TRUE); #summary est un array on recupere la valeur dans l'insertion attention, summary c'est pour le titre !
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
   			$startTime = '';#on initialise le temps de début
    		if (!in_array("DATE", $dtstart_array["params"])) {
       			 $startTime = " {$dtstart["hour"]}:{$dtstart["min"]}:{$dtstart["sec"]}";
    			}
    		#on fait une variable qui contient le résultat des deux précédentes actions
    		$date_debut = $startDate.$startTime;
	#les 3 lignes suivantes servent à récupérer la date de fin et à la mettre dans le bon format
  		$dtend_array = $objet_evenement->getProperty("dtend", 1, TRUE);
   			$dtend = $dtend_array["value"];
    		$endDate = "{$dtend["year"]}-{$dtend["month"]}-{$dtend["day"]}";
    		$endTime = '';#on initialise le temps de fin
    		if (!in_array("DATE", $dtend_array["params"])) {
       			$endTime = " {$dtend["hour"]}:{$dtend["min"]}:{$dtend["sec"]}";
    			}
    		#on fait une variable qui contient le résultat des deux précédentes actions
    		$date_fin = $endDate.$endTime;
	#on insere les infos des événements dans la base 
	//les infos de l'almanach
	$id_mot = $tableau_almanach['id_mot'];
	$id_article = $tableau_almanach['id_article'];
	$id_almanach = $tableau_almanach['id_almanach'];

	//insertion de l'evenement en bdd
	$id_evenement= sql_insertq('spip_evenements',array('id_article' =>$id_article,'date_debut'=>$date_debut,'date_fin'=>$date_fin,'titre'=>str_replace('SUMMARY:', '', $summary_array["value"]),'descriptif'=>'<math>'.$descriptif_array["value"].'</math>','lieu'=>$lieu,'adresse'=>'','inscription'=>'0','places'=>'0','horaire'=>'oui','statut'=>'publie','attendee'=>str_replace('MAILTO:', '', $attendee),'id_evenement_source'=>'0','uid'=>$uid_distante,'sequence'=>$sequence_distante,'notes'=>$url));
	//on associe l'événement à l'almanach
	sql_insertq("spip_almanachs_liens",array('id_almanach'=>$id_almanach,'id_objet'=>$id_evenement,'objet'=>'evenement','vu'=>'oui'));
	//on associe l'événement à son mot
	sql_insertq("spip_mots_liens",array('id_mot'=>$id_mot,'id_objet'=>$id_evenement,'objet'=>'evenement'));
}

?>