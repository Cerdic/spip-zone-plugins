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
	$le_id_article=_request("le_id_article");//id_article est protégé pour ne prendre que des int avec l'ecran securite, mais comme on utilise le selecteur, on a un tableau
	$id_article=str_replace("article|","",$le_id_article[0]);
	set_request("id_article",$id_article);
	
	if ((lire_config("import_ics/mot_facultatif")==null) and !(_IMPORT_ICS_MOT_FACULTATIF=='off')){
		$erreurs = formulaires_editer_objet_verifier('almanach',$id_almanach, array('titre', 'url', 'id_article', 'id_mot'));
	}
	else{
		$erreurs = formulaires_editer_objet_verifier('almanach',$id_almanach, array('titre', 'url', 'id_article'));
	}

  if ($erreurs["id_article"]){
		$erreurs["le_id_article"]=$erreurs["id_article"];
		unset($erreurs["id_article"]);
	}
	return $erreurs;
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
	$url = _request("url");
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
	importation_almanach($id_almanach,$url);
	return $chargement;
}


?>
