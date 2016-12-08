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
	$valeurs['tableau_decalage'] = array();
	$i = -24;
	while ($i < 0){
		if (abs($i) !=1){
			$valeurs['tableau_decalage'][strval($i)] = strval($i)." "._T("date_heures");
		}
		else {
			$valeurs['tableau_decalage'][strval($i)] = strval($i)." "._T("date_une_heure");		
		}
		$i++;
	}
	$valeurs['tableau_decalage'][0] = _T("almanach:aucun_decalage");
	$i++;
	while ($i <= 24){
		if (abs($i) !=1){
			$valeurs['tableau_decalage'][strval($i)] = "+".strval($i)." "._T("date_heures");
		}
		else {
			$valeurs['tableau_decalage'][strval($i)] = "+".strval($i)." "._T("date_une_heure");		
		}
		$i++;		
	}
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
	$decalage = array();
	// Si besoin, on récupère les anciennes versions de certains champs
	if ($id_almanach!='new'){
		$ancien_decalage = array();
		$ancien_decalage['ete'] = sql_getfetsel("decalage_ete","spip_almanachs","id_almanach=$id_almanach");
		$ancien_decalage['hiver'] = sql_getfetsel("decalage_hiver","spip_almanachs","id_almanach=$id_almanach");
		$ancien_id_article = sql_getfetsel("id_article","spip_almanachs","id_almanach=$id_almanach");
	}
	$chargement = formulaires_editer_objet_traiter('almanach',$id_almanach,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	
	#on recupère l'id de l'almanach dont on aura besoin plus tard
	$id_almanach = $chargement['id_almanach'];
	$url = _request("url");
	$id_article = _request("id_article");
	$id_mot = _request("id_mot");
	$decalage['ete'] = _request("decalage_ete");
	$decalage['hiver'] = _request("decalage_hiver");
	
	
	#on associe le mot à l'almanach
	if ($id_mot){
		sql_insertq(
			"spip_mots_liens",
			array(
				'id_mot'=>$id_mot,
				'id_objet'=>$id_almanach,
				'objet'=>'almanach'
			)
		);
	}
	
	// Corriger les évènements existants si certaines propriétés de l'almanache sont modifiés
	if ($id_almanach!='new'){
		corriger_decalage($id_almanach,$decalage,$ancien_decalage);
		corriger_article_referent($id_almanach,$id_article,$ancien_id_article);
	}
	# on importe les autres évènement
	importer_almanach($id_almanach,$url,$id_article,$id_mot,$decalage);
	
	return $chargement;
}

function corriger_decalage($id_almanach,$nouveau_decalage,$ancien_decalage){
	include_spip('action/editer_evenement');
	$decalage_ete = intval($nouveau_decalage['ete']) - intval($ancien_decalage['ete']);
	$decalage_hiver = intval($nouveau_decalage['hiver']) - intval($ancien_decalage['hiver']);
	$liens = sql_allfetsel('E.uid, E.id_evenement, E.date_debut, E.date_fin',
		"spip_evenements AS E
		INNER JOIN spip_almanachs_liens AS L
		ON E.id_evenement = L.id_objet AND L.id_almanach=$id_almanach","E.horaire!=".sql_quote("non"));	
	
	if(is_array($liens) and count($liens)>0){
		foreach ($liens as $l){
			$champs_sql = array();
			$id_evenement = intval($l["id_evenement"]);
			$heure_ete_debut = intval(affdate($l['date_debut'],'I'));//Est-ce que la date de début se trouve en période d'heure d'été?
			$heure_ete_fin = intval(affdate($l['date_fin'],'I'));// Est-ce que la date de fin se trouve en période d'heure d'été?
			
			if ($heure_ete_debut){
				$champs_sql['date_debut'] = "DATE_ADD(date_debut, INTERVAL  $decalage_ete HOUR)";
			}
			else {
				$champs_sql['date_debut'] = "DATE_ADD(date_debut, INTERVAL  $decalage_hiver HOUR)";
			}

			if ($heure_ete_fin){
				$champs_sql['date_fin'] = "DATE_ADD(date_fin, INTERVAL  $decalage_ete HOUR)";
			}
			else {
				$champs_sql['date_fin'] = "DATE_ADD(date_fin, INTERVAL  $decalage_hiver HOUR)";
			}
		
			autoriser_exception('evenement','modifier',$id_evenement);
			objet_modifier('evenement',$id_evenement,$champs_sql);
			autoriser_exception('evenement','modifier',$id_evenement,false);
		}
  }
}

function corriger_article_referent($id_almanach,$id_article,$ancien_id_article){
	if ($id_article != $ancien_id_article){
		
		$liens = sql_allfetsel('E.uid, E.id_evenement',
			"spip_evenements AS E
			INNER JOIN spip_almanachs_liens AS L
			ON E.id_evenement = L.id_objet AND L.id_almanach=$id_almanach");
		
		$c = array(
			"id_parent" => $id_article,
		);
		
		foreach ($liens as $l){
			$id_evenement = intval($l["id_evenement"]);
			autoriser_exception('evenement','modifier',$id_article);
			objet_modifier('evenement',$id_evenement,$c);
			autoriser_exception('evenement','modifier',$id_article,false);
		}
	}
}