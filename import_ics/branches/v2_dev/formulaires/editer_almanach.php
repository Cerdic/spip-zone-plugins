<?php
/**
 * Gestion du formulaire de d'édition de almanach
 *
 * @plugin     Import_ics
 * @copyright  2013
 * @author     Amaury Adon
 * @licence    GNU/GPL
 * @package    SPIP\Import_ics\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('action/editer_liens');
include_spip('inc/editer');
include_spip('inc/import_ics');
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
	$valeurs['resa_auto']='non';
	return $valeurs;

}

/**
 * Vérifications du formulaire d'édition de almanach
 *
 */
function formulaires_editer_almanach_verifier_dist($id_almanach='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	//version de base de la fabrique
	//return formulaires_editer_objet_verifier('almanach',$id_almanach, array('titre', 'url', 'id_article'));
	$le_id_article=_request("le_id_article");//id_article est protégé pour ne prendre que des int avec l'ecran securite, mais comme on utilise le selecteur, on a un tableau
	$id_article=str_replace("article|","",$le_id_article[0]);
	set_request("id_article",$id_article);
	
	if (lire_config("import_ics/mot_facultatif")==null){
		$erreurs = formulaires_editer_objet_verifier('almanach',$id_almanach, array('titre', 'url', 'id_article', 'id_mot'));
	}
	else{
		$erreurs = formulaires_editer_objet_verifier('almanach',$id_almanach, array('titre', 'url', 'id_article'));
	}
	//verification supplementaires
	return $erreurs;
}



/**
 * Traitement du formulaire d'édition de almanach
 *
 * Traiter les champs postés
 *
 */
function formulaires_editer_almanach_traiter_dist($id_almanach='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	//set_request("id_article",str_replace("article|","",_request("id_article")));
	$chargement = formulaires_editer_objet_traiter('almanach',$id_almanach,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	#on recupère l'id de l'almanach dont on aura besoin plus tard
	$id_almanach = $chargement['id_almanach'];
	
	#on associe le mot à l'almanach
	if ($id_mot = _request('id_mot')){
		sql_insertq(
			"spip_mots_liens",
			array(
				'id_mot'=>$id_mot,
				'id_objet'=>$id_almanach,
				'objet'=>'almanach'
			)
		);
	}
	
	// Début de la récupération des évènements
	#configuration nécessaire à la récupération
	$config = array("unique_id"=>"","url"=>_request("url"));
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
				importation_evenement($comp,$id_almanach);
			};//l'evenement n'est pas dans la bdd, on va l'y mettre
 	}
	return $chargement;
}


?>