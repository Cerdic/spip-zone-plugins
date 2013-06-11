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
include_spip('inc/editer');
include_spip('lib/iCalcreator.class'); /*pour la librairie icalcreator incluse dans le plugin icalendar*/
/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_almanach
 *     Identifiant du almanach. 'new' pour un nouveau almanach.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un almanach source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du almanach, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_almanach_identifier_dist($id_almanach='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_almanach)));
}

/**
 * Chargement du formulaire d'édition de almanach
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_almanach
 *     Identifiant du almanach. 'new' pour un nouveau almanach.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un almanach source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du almanach, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_almanach_charger_dist($id_almanach='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('almanach',$id_almanach,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	$valeurs[_etapes]=2;//on rajoute  un couple clé/valeur pour le nombre d'étapes du formulaire
	return $valeurs;

}

/**
 * Vérifications du formulaire d'édition de almanach
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_almanach
 *     Identifiant du almanach. 'new' pour un nouveau almanach.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un almanach source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du almanach, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_almanach_verifier_1_dist($id_almanach='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	//version de base de la fabrique
	//return formulaires_editer_objet_verifier('almanach',$id_almanach, array('titre', 'url', 'id_article'));
	$erreurs = formulaires_editer_objet_verifier('almanach',$id_almanach, array('titre', 'url', 'id_article'));
	
	//verification supplementaires

	return $erreurs;
}

/**
 * Traitement du formulaire d'édition de almanach
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_almanach
 *     Identifiant du almanach. 'new' pour un nouveau almanach.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un almanach source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du almanach, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_almanach_traiter_dist($id_almanach='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){

	# on passe par un fichier temp car notre librairie fonctionne comme ca
	//$tmp = _DIR_TMP . 'ics-'.md5('url');
	//ecrire_fichier($tmp, str_replace("\r\n", "\n", $u));
	$config = array("unique_id"=>"","url"=>"http://www.latp.univ-mrs.fr/spip.php?page=seminaire_ical&id_article=351");
	$cal = new vcalendar($config);
	//$cal->setConfig( 'filename', $tmp );
	$cal->parse();

 while ($comp = $cal->getComponent())
 {
	#on recupere les infos de l'evenement dans des variables
	    $attendee = $comp->getProperty( "attendee" ); #nom de l'attendee
	    $lieu = $comp->getProperty("location");#récupération du lieu
	    $summary_array = $comp->getProperty("summary", 1, TRUE); #summary est un array on recupere la valeur dans l'insertion attention, summary c'est pour le titre !
	    $descriptif_array = $comp->getProperty("description",1,TRUE);
	#les 3 lignes suivantes servent à récupérer la date de début et à la mettre dans le bon format
	    $dtstart_array = $comp->getProperty("dtstart", 1, TRUE); 
	    	$dtstart = $dtstart_array["value"];
   			$startDate = "{$dtstart["year"]}-{$dtstart["month"]}-{$dtstart["day"]}";
   			$startTime = '';#on initialise le temps de début
    		if (!in_array("DATE", $dtstart_array["params"])) {
       			 $startTime = " {$dtstart["hour"]}:{$dtstart["min"]}:{$dtstart["sec"]}";
    			}
    		#on fait une variable qui contient le résultat des deux précédentes actions
    		$date_debut = $startDate.$startTime;
	#les 3 lignes suivantes servent à récupérer la date de fin et à la mettre dans le bon format
  		$dtend_array = $comp->getProperty("dtend", 1, TRUE);
   			$dtend = $dtend_array["value"];
    		$endDate = "{$dtend["year"]}-{$dtend["month"]}-{$dtend["day"]}";
    		$endTime = '';#on initialise le temps de fin
    		if (!in_array("DATE", $dtend_array["params"])) {
       			$endTime = " {$dtend["hour"]}:{$dtend["min"]}:{$dtend["sec"]}";
    			}
    		#on fait une variable qui contient le résultat des deux précédentes actions
    		$date_fin = $endDate.$endTime;
	#on insere les infos des événements dans la base spip_evenements
	sql_insertq('spip_evenements',array('id_article' =>$id_article,'date_debut'=>$date_debut,'date_fin'=>$date_fin,'titre'=>str_replace('SUMMARY:', '', $summary_array["value"]),'descriptif'=>'<math>'.$descriptif_array["value"].'</math>','lieu'=>$lieu,'adresse'=>'','inscription'=>'0','places'=>'0','horaire'=>'oui','statut'=>'publie','attendee'=>str_replace('MAILTO:', '', $attendee),'id_evenement_source'=>'0'));
	// 									'date_debut'=>'',
	// 									'date_fin'=>'',
	// 									'titre'=>'',
	// 									'descriptif'=>str_replace('SUMMARY:', '', $summary_array["value"]),
	// 									'lieu'=>'',
	// 									'adresse'=>'',
	// 									'inscription'=>'0',
	// 									'places'=>'0',
	// 									'horaire'=>'oui',
	// 									'statut'=>'publie',
	// 									'maj'=>'',
	// 									'name'=>'',
	// 									'origin'=>'',
	// 									'notes'=>'',
	// 									'attendee'=>$attendee );)

  sql_insertq('spip_almanachs_liens',array('id_almanach'=>'1','id_objet'=>'2','objet'=>'evenement'));
 }
	return formulaires_editer_objet_traiter('almanach',$id_almanach,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
}


?>