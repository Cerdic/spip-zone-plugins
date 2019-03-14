<?php
if (!defined('_ECRIRE_INC_VERSION')) return;
/**
* Importation ou synchronisation d'un almanach
**/

include_spip('lib/iCalcreator.class'); /*pour la librairie icalcreator incluse dans le plugin icalendar*/
include_spip('inc/autoriser');
include_spip('action/editer_objet');
include_spip('action/editer_liens');
include_spip('inc/config');
include_spip('import_ics_fonctions');
include_spip('inc/filtres_ecrire');

/**
* Fonction qui trouve tous les évènements associés à un almanach, sauf les archivés (sauf si on demande explicitement)
**/
function trouver_evenements_almanach($id_almanach,$champs='uid,id_evenement',$tous=false){
	if ($tous){
		$liens = sql_allfetsel($champs,
			'spip_evenements
			INNER JOIN spip_almanachs_liens AS L
			ON id_evenement = L.id_objet AND L.id_almanach='.intval($id_almanach));
	}
	else{
		$liens = sql_allfetsel($champs,
			'spip_evenements
			INNER JOIN spip_almanachs_liens AS L
			ON id_evenement = L.id_objet AND L.id_almanach='.intval($id_almanach),"statut!=".sql_quote("archive"));
  }
	return $liens;
}

function importer_almanach($id_almanach,$url,$id_article,$decalage){
	// Début de la récupération des évènements
	//configuration nécessaire à la récupération
	$config = array("unique_id"=>"","url"=>$url);
	$cal = new vcalendar($config);
	if (!$cal->parse()){
		spip_log("Erreur lors de l'analyse de l'url $url (almanach $id_almanach)","import_ics"._LOG_ERREUR);
		sql_update("spip_almanachs",array("derniere_erreur"=>"NOW()"),"id_almanach=".intval($id_almanach));
		return;
	}

	$statut = sql_getfetsel('statut','spip_almanachs',"`id_almanach`=$id_almanach");
	$liens = trouver_evenements_almanach($id_almanach);

	// rechercher les mots clefs lié à un objet_modifier
	$mots = lister_objets_lies('mot','almanach',$id_almanach,'spip_mots_liens');


	// on definit un tableau des uid présentes dans la base
	$uid =array();
	foreach ($liens as $u ) {
		$uid[$u["id_evenement"]] = $u['uid'];
	};
	$les_uid_distant = array();
	while ($comp = $cal->getComponent()){
			#les variables qui vont servir à vérifier l'existence et l'unicité
			$uid_distante = $comp->getProperty("UID");#uid de l'evenement
			$les_uid_distant[] = 	$uid_distante;
			$last_modified_distant = $comp->getProperty("LAST-MODIFIED");
			$sequence_distant = $comp->getProperty("SEQUENCE");

			//vérifier l'existence et l'unicité
			if (in_array($uid_distante, $uid)){//si l'uid_distante est présente dans la bdd, alors on teste si l'evenement a été modifié à distance
				$test_variation = sql_fetsel("last_modified_distant,sequence,id_evenement",
					"spip_evenements",
					"`uid`=".sql_quote($uid_distante)
				);
				$last_modified_local = unserialize($test_variation["last_modified_distant"]);
				$sequence_local = $test_variation["sequence"];
				$id_evenement = $test_variation["id_evenement"];
				if ($last_modified_local!=$last_modified_distant or $sequence_local!=$sequence_distant){
						$champs_sql = evenement_ical_to_sql($comp,$decalage);
						autoriser_exception('evenement','modifier',$id_evenement);
						objet_modifier('evenement',$id_evenement,$champs_sql);
						autoriser_exception('evenement','modifier',$id_evenement,false);
						spip_log ("Mise à jour de l'évènement $id_evenement, almanach $id_almanach","import_ics"._LOG_INFO);
					}
				}
			else {
				importer_evenement($comp,$id_almanach,$id_article,$decalage,$statut,$mots);
			};//l'evenement n'est pas dans la bdd, on va l'y mettre
		}
		if (_IMPORT_ICS_DEPUBLIER_ANCIENS_EVTS == 'on' or  lire_config("import_ics/depublier_anciens_evts") == 'on'){
			depublier_ancients_evts($uid,$les_uid_distant,$id_article);
		}

		// mettre à jour les infos de synchronisation. Le champ derniere_synchro n'est pas un champ éditable, donc on passe par sql_update et pas par editer_objet
		sql_update("spip_almanachs",array("derniere_synchro"=>"NOW()"),"id_almanach=".intval($id_almanach));
}

/**
* Dépublier (archiver) les anciens évènements
**/
function depublier_ancients_evts($les_uid_local,$les_uid_distant,$id_article){
	$diff = array_diff ($les_uid_local,$les_uid_distant);
	$print_local = print_r($les_uid_local,true);
	$print_distant = print_r($les_uid_distant,true);
	spip_log("UID local:$print_local;UID_distant:$print_distant","import_ics"._LOG_INFO);
	foreach ($diff as $id_evenement =>$uid){
		autoriser_exception('instituer','evenement',$id_evenement);
		autoriser_exception('modifier','article',$id_article);
		objet_instituer('evenement',$id_evenement,array("statut"=>'archive'));
		spip_log ("Archivage de l'évènement $id_evenement (uid:$uid)","import_ics"._LOG_INFO);
		autoriser_exception('instituer','evenement',$id_evenement,false);
		autoriser_exception('modifier','article',$id_article,false);
	}
}
/**
* Importation d'un événement dans la base
**/
function importer_evenement($objet_evenement,$id_almanach,$id_article,$decalage,$statut,$mots){
	$champs_sql = array_merge(
		evenement_ical_to_sql($objet_evenement,$decalage),
		array(
				"id_article"=>$id_article,
				"date_creation"=>date('Y-m-d H:i:s')
		)
	);

	# création de l'evt
	autoriser_exception('creer','evenement','');
  $id_evenement= objet_inserer('spip_evenements',$id_article,$champs_sql);
	autoriser_exception('creer','evenement','',false);

	autoriser_exception('instituer','evenement',$id_evenement);
	autoriser_exception('modifier','article',$id_article);
	objet_instituer('evenement',$id_evenement,array("statut"=>$statut));
	// lier les mots
	objet_associer(
		array('mot'=>$mots),
		array('evenement'=>$id_evenement)
	);
	autoriser_exception('instituer','evenement',$id_evenement,false);
	autoriser_exception('modifier','article',$id_article,false);


	#on associe l'événement à l'almanach
	objet_associer(array('almanach'=>$id_almanach),array('evenement'=>$id_evenement),array('vu'=>'oui'));
	spip_log ("Import de l'évènement $id_evenement, almanach $id_almanach","import_ics"._LOG_INFO);
}

/*
** Récupérer les propriétés d'un evenements de sorte qu'on puisse en faire la requete sql
*/
function evenement_ical_to_sql($objet_evenement,$decalage){
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

		# récupérer la date de début et la formater correctement
		  	$dtstart_array = $objet_evenement->getProperty("dtstart", 1, TRUE);
			list ($date_debut,$start_all_day) = date_ical_to_sql($dtstart_array,$decalage);
		#les 3 lignes suivantes servent à récupérer la date de fin et à la mettre dans le bon format
	  		$dtend_array = $objet_evenement->getProperty("dtend", 1, TRUE);
			if (is_array($dtend_array)) {
				list ($date_fin,$end_all_day) = date_ical_to_sql($dtend_array,$decalage);
			} else {
				$duration_array = $objet_evenement->getProperty("duration", 1, TRUE);
				if ($duration_array["value"]<>''){
					$date_deb = date_ical_to_sql($dtstart_array,'', TRUE);
					$date_ete = intval(affdate($date_deb,'I'));//Est-on en heure d'été?
					if (!$all_day AND is_array($decalage) AND isset($decalage['ete']) AND isset($decalage['hiver'])) {
						if ($date_ete) {
							$decalageheure = $decalage['ete'];
						} else {
							$decalageheure = $decalage['hiver'];
						}
					}
					$duree_seconde=($duration_array["value"]["hour"]+$decalageheure)*60*60+$duration_array["value"]["min"]*60+$duration_array["value"]["sec"] ;
					$date_fin = "DATE_ADD('$date_deb', INTERVAL $duree_seconde SECOND)";
				} else {
					$date_fin = $date_debut;
					$end_all_day = $debut_all_day;
				}
			}

			// Est-ce que l'evt dure toute la journée?
			if ($end_all_day and $start_all_day){
				$horaire = "non";
				$date_fin = "DATE_SUB('$date_fin', INTERVAL 1 DAY)";// Si un évènement dure toute la journée du premie août, le flux ICAL indique pour DTEND le 2 août (cf http://www.faqs.org/rfcs/rfc2445.html). Par contre le plugin agenda lui dit simplement "evenement du 1er aout au 1er aout, sans horaire". D'où le fait qu'on décale $date_fin par rapport aux flux originel.
			}
			else{
				$horaire = "oui";
			}
		return array(
		  'date_debut' => $date_debut,
		  'date_fin' => $date_fin,
			'titre' => $titre_evt,
			'descriptif' => $descriptif_array["value"],
			'lieu' => $lieu,
			'adresse' => '',
			'inscription' => '0',
			'places' => '0',
			'horaire' => $horaire,
			'attendee' => str_replace('MAILTO:', '', $attendee),
			'id_evenement_source' => '0',
			'uid' => $uid_distante,
			'sequence' => $sequence_distante,
			'last_modified_distant' => $last_modified_distant,
			'notes' => $url
		);
}
