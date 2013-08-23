<?php
/**
 * Gestion du formulaire de d'édition de almanach
 *
 * @plugin     Import_ics
 * @copyright  2013
 * @author     Amaury
 * @licence    GNU/GPL
 * @package    SPIP\Import_ics\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('action/editer_liens');
include_spip('inc/editer');
include_spip('lib/iCalcreator.class'); /*pour la librairie icalcreator incluse dans le plugin icalendar*/
/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 */
function formulaires_editer_almanach_identifier_dist($id_almanach='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_almanach)));
}

/**
 * Chargement du formulaire d'édition de almanach
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 */
function formulaires_editer_almanach_charger_dist($id_almanach='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('almanach',$id_almanach,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	//$valeurs[_etapes]=2;//on rajoute  un couple clé/valeur pour le nombre d'étapes du formulaire (pas la peine tant que je n'arrive pas à avoir un résutat correct)
	return $valeurs;

}

/**
 * Vérifications du formulaire d'édition de almanach
 *
 */
function formulaires_editer_almanach_verifier_dist($id_almanach='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	//version de base de la fabrique
	//return formulaires_editer_objet_verifier('almanach',$id_almanach, array('titre', 'url', 'id_article'));
	$erreurs = formulaires_editer_objet_verifier('almanach',$id_almanach, array('titre', 'url', 'id_article', 'id_mot'));
	//verification supplementaires
	return $erreurs;
}


/**
* Importation d'un événement dans la base
**/
function importation_evenement($objet_evenement,$id_almanach){
	#on recupere les infos de l'evenement dans des variables
	    $attendee = $objet_evenement->getProperty( "attendee" ); #nom de l'attendee
	    $lieu = $objet_evenement->getProperty("location");#récupération du lieu
	    $summary_array = $objet_evenement->getProperty("summary", 1, TRUE); #summary est un array on recupere la valeur dans l'insertion attention, summary c'est pour le titre !
		$url = $objet_evenement->getProperty( "URL");#on récupère l'url de l'événement pour la mettre dans les notes histoire de pouvoir relier à l'événement original
	    $descriptif_array = $objet_evenement->getProperty("DESCRIPTION");
	    $organizer = $objet_evenement->getProperty("ORGANIZER");#organisateur de l'evenement
	#données de localisation de l'évenement
	    $localisation = $objet_evenement->getProperty( "GEO" );#c'est un array array( "latitude"  => <latitude>, "longitude" => <longitude>))
	    $latitude = $localisation['latitude'];
	    $longitude = $localisation['longitude'];
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
	# ca ce sera pour quand j'arriverai à faire fonctionner le selecteur d'articles $id_article = preg_replace('(article\|)','',_request('id_article')); #le selecteur d'article fournit un tableau, on se débarasse du mot article dedans et on appellera ensuite la première valeur (il pourrait y avoir des saisies multiples même si ici on ne les autorise pas)
	$id_article = _request('id_article'); 
	$id_evenement= sql_insertq('spip_evenements',array('id_article' =>$id_article,'date_debut'=>$date_debut,'date_fin'=>$date_fin,'titre'=>str_replace('SUMMARY:', '', $summary_array["value"]),'descriptif'=>'<math>'.$descriptif_array["value"].'</math>','lieu'=>$lieu,'adresse'=>'','inscription'=>'0','places'=>'0','horaire'=>'oui','statut'=>'publie','attendee'=>str_replace('MAILTO:', '', $attendee),'id_evenement_source'=>'0','uid'=>$uid_distante,'sequence'=>$sequence_distante,'notes'=>$url));
	
	#on associe l'évéenement à l'almanach
	objet_associer(array('almanach'=>$id_almanach),array('evenement'=>$id_evenement),array('vu'=>'oui'));
	#on associe l'événement à son mot
	sql_insertq("spip_mots_liens",array('id_mot'=>$id_mot,'id_objet'=>$id_evenement,'objet'=>'evenement'));
}


/**
 * Traitement du formulaire d'édition de almanach
 *
 * Traiter les champs postés
 *
 */
function formulaires_editer_almanach_traiter_dist($id_almanach='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$chargement = formulaires_editer_objet_traiter('almanach',$id_almanach,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	#on recupère l'id de l'almanach dont on aura besoin plus tard
	$id_almanach = $chargement['id_almanach'];


	#on associe le mot à l'almanach
	$id_mot = _request('id_mot');
	sql_insertq("spip_mots_liens",array('id_mot'=>$id_mot,'id_objet'=>$id_almanach,'objet'=>'almanach'));

	#configuration nécessaire à la récupération
	$config = array("unique_id"=>"","url"=>_request("url"));
	$cal = new vcalendar($config);
	//$cal->setConfig( 'filename', $tmp );
	$cal->parse();


	//ON fait un appel dans la base de spip pour vpouvoir vérifier si un événement y est 
	//déjà (ça ne se fait pas en une ligne...°)
	$liens = sql_allfetsel('id_evenement, uid, sequence', 'spip_evenements');
	// on definit un tableau des uid présentes dans la base
	$uid ="";
	foreach ($liens as $u ) {
		$uid[] = $u['uid'];
	};


 while ($comp = $cal->getComponent())
 {

	#les variables qui vont servir à vérifier l'existence et l'unicité 
		
	   	$sequence_distante = $comp->getProperty( "SEQUENCE" );#sequence d l'evenement http://kigkonsult.se/iCalcreator/docs/using.html#SEQUENCE
	    $uid_distante = $comp->getProperty("UID");#uid de l'evenement
		if (!is_int($sequence_distante)){$sequence_distante="0";}//au cas où le flux ics ne fournirait pas le champ sequence, on initialise la valeur à 0 comme lors d'un import

//On commence à vérifier l'existence et l'unicité  maintenant et on met à jour ou on importe selon le cas
	if (in_array($uid_distante, $uid)){//si l'uid_distante est présente dans la bdd
		$cle = array_search($uid_distante, $uid); // on utilise le fait que les deux tableaux ont le même index pour le récupérer
		$sequence = $liens[$cle]['sequence'];//sequence presente dans la base ayant le meme index

		if ($sequence < $sequence_distante ){//si la sequecne de la bdd est plus petite, il y a eu mise à jour et il faut intervenir
			echo "c'est pas pareil, il faut mettre à jour l'événement ".$liens[$cle]['id_evenement']."<br/>";
		} 
	} else {importation_evenement($comp,$id_almanach);};//l'evenement n'est pas dans la bdd, on va l'y mettre






 }

	return $chargement;
}


?>