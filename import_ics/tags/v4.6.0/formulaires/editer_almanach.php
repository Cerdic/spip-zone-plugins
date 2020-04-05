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
	$erreurs = formulaires_editer_objet_verifier('almanach',$id_almanach, array('titre', 'url', 'id_article'));

	$verifier = charger_fonction('verifier', 'inc/');
	$erreur_url = $verifier(
			trim(_request('url')),
			'url',
			array(
				'mode'=>'php_filter',
				'type_protocole'=>'webcal'
			)
		);
  if ($erreur_url){
		$erreurs['url'] = $erreur_url;
	}
  if (isset($erreurs["id_article"])){
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
	if ($id_almanach!='new' and $id_almanach!='oui'){
		$ancien_decalage = array();
		$anciens_champs = sql_fetsel("decalage_ete,decalage_hiver,id_article,url","spip_almanachs","id_almanach=".sql_quote($id_almanach));
		$ancien_decalage['ete'] = $anciens_champs['decalage_ete'];
		$ancien_decalage['hiver'] = $anciens_champs['decalage_hiver'];
		$ancien_id_article = $anciens_champs['id_article'];
		$ancien_url = $anciens_champs['url'];
	}
	$chargement = formulaires_editer_objet_traiter('almanach',$id_almanach,'',$lier_trad,$retour,$config_fonc,$row,$hidden);

	#on recupère l'id de l'almanach dont on aura besoin plus tard
	$id_almanach = $chargement['id_almanach'];
	$url = trim(_request("url"));
	$id_article = _request("id_article");
	$decalage['ete'] = _request("decalage_ete");
	$decalage['hiver'] = _request("decalage_hiver");



	// Corriger les évènements existants si certaines propriétés de l'almanache sont modifiés
	$evenements = trouver_evenements_almanach($id_almanach,'id_evenement,date_debut,date_fin',true);
	if (is_array($evenements) and count($evenements)>0){
		corriger_decalage($id_almanach,$decalage,$ancien_decalage,$evenements);
		corriger_article_referent($id_almanach,$id_article,$ancien_id_article,$evenements);
	}
	if (isset($ancien_url) and $url!=$ancien_url){// si jamais on a changé l'url => du passé faisons table rase
		include_spip('action/supprimer_evenements_almanach');
		action_supprimer_evenements_almanach_post($id_almanach);
	}
	# on importe les autres évènement
	importer_almanach($id_almanach,$url,$id_article,$decalage);

	return $chargement;
}

function corriger_decalage($id_almanach,$nouveau_decalage,$ancien_decalage,$liens){
	include_spip('action/editer_evenement');
	$decalage_ete = intval($nouveau_decalage['ete']) - intval($ancien_decalage['ete']);
	$decalage_hiver = intval($nouveau_decalage['hiver']) - intval($ancien_decalage['hiver']);

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
		spip_log ("Application du nouveau décalage pour l'évènenement $id_evenement, almanach $id_almanach","import_ics"._LOG_INFO);
  }
}

function corriger_article_referent($id_almanach,$id_article,$ancien_id_article,$liens){
	if ($id_article != $ancien_id_article){

		$c = array(
			"id_parent" => $id_article,
		);

		foreach ($liens as $l){
			$id_evenement = intval($l["id_evenement"]);
			autoriser_exception('evenement','modifier',$id_article);
			objet_modifier('evenement',$id_evenement,$c);
			autoriser_exception('evenement','modifier',$id_article,false);
			spip_log ("Mise à jour de l'article pour l'évènement $id_evenement, almanach $id_almanach","import_ics"._LOG_INFO);
		}
	}
}
