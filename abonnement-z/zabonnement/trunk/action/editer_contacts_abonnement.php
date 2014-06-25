<?php
 
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_contacts_abonnement_dist($arg=null) {

	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
		
	// si id_contacts_abonnement n'est pas un nombre, c'est une creation
	if (!$id_contacts_abonnement = intval($arg)) {
		spip_log("creation d'un contacts_abonnement",'abonnement');
		$id_contacts_abonnement = insert_contacts_abonnement(array(
			'id_auteur'=>_request('id_auteur'),
			'objet'=>_request('objet'),
			'id_objet'=>_request('id_objet')
			));

	}
	
	// Enregistre l'envoi dans la BD
	if ($id_contacts_abonnement > 0) $err = contacts_abonnement_set($id_contacts_abonnement);

	return array($id_contacts_abonnement,$err);
}

/**
 * Crée un nouveau contacts_abonnement et retourne son ID
 *
 * @param array $champs Un tableau avec les champs par défaut lors de l'insertion
 * @return int id_organisation
 */
function insert_contacts_abonnement($champs=array()) {
	$id_contacts_abonnement = false;
	
	// On insère seulement s'il y a un auteur correct
	if (isset($champs['id_auteur']) and $champs['id_auteur'] = intval($champs['id_auteur'])){
		// Si id_auteur vaut 0 ou n'est pas defini, ne pas creer de commande et envoyer message impossible
		if (!$id_auteur = intval($champs['id_auteur'])) 
			return false; // ? minipress(); ?
		}

	// Le stade de relance à zero
	$champs['stade_relance'] = '0';
	// Statut en cours
	$champs['statut_abonnement'] = $champs['statut_abonnement']?$champs['statut_abonnement']:'encours';
	
	//if($champs['page_envoi']=='auteur_infos') spip_log("YES $champs['page_envoi']",'contacts_abonnement');
	
	if(($id_objet=$champs['id_objet'])&&($objet=$champs['objet'])&&($id_auteur=$champs['id_auteur'])>0){
		$table='spip_'.$objet.'s';	
		//objet = rubrique, article ou abonnement
			$verif = sql_fetsel('*', $table, 'id_'."$objet = " . $id_objet);
			if (!$verif) 
			{
				if (_DEBUG_ABONNEMENT) spip_log("insert_contacts_abonnement $objet $id_objet inexistant",'abonnement');
				die("Action insert_contacts_abonnement $objet $id_objet inexistant");
			}
			
		//creation
			$calculer_prix = charger_fonction('prix', 'inc/');
			$prix=($statut=='offert')?'':$calculer_prix($objet,$id_objet);//pas de prix puisque offert
			//si la date est spécifiée en amont
			$date = $champs['date']?$champs['date']:date('Y-m-d H:i:s');
			//la duree par defaut est fixee a 3 jours
			$duree=($arg['duree'])?$arg['duree']:'3';
			$periode=($arg['periode'])?$arg['periode']:'jours';
			include_spip('abonnement_fonctions');
			$validite = modifier_date($date,$duree);
		
		//specific a abonnement
			if($objet=='abonnement'){
				$prix=($statut=='offert')?'':$verif['prix'];//pas de prix puisque offert
				$duree = $verif['duree'];
				$periode = $verif['periode'];
					
				// jour
				if ($periode == 'jours') {
					$validite = modifier_date($date,$duree);
				}
				// ou mois
				else {
					$validite = modifier_date($date,'',$duree);
				}
			}
		//ouverture des zones
			if($ids_zone=$verif['ids_zone'])
				ouvrir_zone($id_auteur,$ids_zone);
			
			$champs['date']=$date;
			//si la validite est spécifiée en amont
			$validi = $champs['validite']?$champs['validite']:$validite;
			$champs['validite']=$validi;
			$champs['prix']=$prix;
	}

	
	// Envoyer aux plugins avant insertion
	$champs = pipeline('pre_insertion',
		array(
			'args' => array(
				'table' => 'spip_contacts_abonnements',
			),
			'data' => $champs
		)
	);
	
	// Insérer l'objet
	if (_DEBUG_ABONNEMENT) spip_log("insert_contacts_abonnement ".join(' | ',$champs),"contacts_abonnement");
	$id_contacts_abonnement = sql_insertq('spip_contacts_abonnements', $champs);
	
	// Envoyer aux plugins après insertion
	pipeline('post_insertion',
		array(
			'args' => array(
				'table' => 'spip_contacts_abonnements',
			),
			'data' => $champs
		)
	);
	
	return $id_contacts_abonnement;
}

/**
 * Appelle la fonction de modification d'un contacts_abonnement
 *
 * @param int $id_contacts_abonnement
 * @param unknown_type $set
 * @return $err
 */
function contacts_abonnement_set($id_contacts_abonnement, $set=null) {
	$err = '';

	if (is_null($set)){
		$c = array();
		foreach (array(
			'id_auteur', 'objet', 'id_objet', 'date',
			'validite','statut_abonnement','stade_relance',
		) as $champ)
			$c[$champ] = _request($champ);
	}
	else
		$c = $set;
		

	if (_DEBUG_ABONNEMENT) spip_log("contacts_abonnement_set ".join(' | ',$c),"contacts_abonnement");
	
	include_spip('inc/modifier');
	revision_contacts_abonnement($id_contacts_abonnement, $c);
	
	// Modification de statut
	$c = array();
	foreach (array(
		'id_auteur', 'date', 'validite', 'statut_abonnement', 
	) as $champ)
		$c[$champ] = _request($champ, $set);
	$err .= instituer_contacts_abonnement($id_contacts_abonnement, $c);
	
	return $err;
}

/**
 * Enregistre une révision de contact
 *
 * @param int $id_produit
 * @param array $c
 * @return
 */
function revision_contacts_abonnement($id_contacts_abonnement, $c=false) {
	$invalideur = "id='id_contacts_abonnement/$id_contacts_abonnement'";
	
	modifier_contenu('contacts_abonnement', $id_contacts_abonnement,
		array(
			'invalideur' => $invalideur
		),
		$c);
	return ''; // pas d'erreur
}

/**
 * Modifie des éléments à part que sont l'auteur, la date, le statut
 *
 * @param int $id_contacts_abonnement
 * @param array $c
 * @return
 */
function instituer_contacts_abonnement($id_contacts_abonnement, $c){

	include_spip('inc/autoriser');
	include_spip('inc/modifier');
	
	$row = sql_fetsel("id_auteur, objet, id_objet, statut_abonnement, date, validite", "spip_contacts_abonnements", "id_contacts_abonnement=$id_contacts_abonnement");
	$id_auteur = $row['id_auteur'];
	$statut_ancien = $statut = $row['statut_abonnement'];
	$date_ancienne = $date = $row['date'];
	$champs = array();
	
	$d = isset($c['date']) ? $c['date'] : null;
	$s = isset($c['statut_abonnement']) ? $c['statut_abonnement'] : $statut;
	
	// On ne modifie le statut que si c'est autorisé
	if ($s != $statut or ($d AND $d != $date)) {
		//todo = donner l'autorisation a contacts_abonnements_paypal_traitement_paypal
		//if (autoriser('modifier', 'contacts_abonnement', $id_contacts_abonnement))
			$statut = $champs['statut_abonnement'] = $s;

		// Si on doit changer la date explicitement
		if ($d){
			$champs['date'] = $date = $d;
		}
	}
	
	
	$champs['id_auteur'] = $id_auteur;

	
	// Envoyer aux plugins
	$champs = pipeline(
		'pre_edition',
		array(
			'args' => array(
				'table' => 'spip_contacts_abonnements',
				'id_objet' => $id_contacts_abonnement,
				'action' => 'instituer',
				'statut_ancien' => $statut_ancien,
			),
			'data' => $champs
		)
	);

	if (!count($champs)) return;
	
	// update
	sql_updateq('spip_contacts_abonnements', $champs, "id_contacts_abonnement=$id_contacts_abonnement");

	

	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_contacts_abonnement/$id_contacts_abonnement'");
	
	if ($date) {
		$t = strtotime($date);
		$p = @$GLOBALS['meta']['date_prochain_postdate'];
		if ($t > time() AND (!$p OR ($t < $p))) {
			ecrire_meta('date_prochain_postdate', $t);
		}
	}
	
	if (_DEBUG_ABONNEMENT) spip_log("il y a un flux post_edition sur contacts_abonnement",'abonnement');

	// Pipeline
	pipeline(
		'post_edition',
		array(
			'args' => array(
				'table' => 'spip_contacts_abonnements',
				'id_objet' => $id_contacts_abonnement,
				'action' => 'instituer',
				'statut_ancien' => $statut_ancien,
			),
			'data' => $champs
		)
	);
	
	// Notifications
	/*if ($notifications = charger_fonction('notifications', 'inc', true)) {
		$notifications('contacts_abonnement_instituer', $id_contacts_abonnement,
			array('statut' => $statut, 'statut_ancien' => $statut_ancien, 'date'=>$date)
		);
	}*/
	
	return '';
}


function ouvrir_zone($id_auteur,$ids_zone)
{
	$array_ids = explode(",", $ids_zone);
	foreach($array_ids as $id_zone)
	{
	//if (_DEBUG_ABONNEMENT) spip_log("ouvrir_zone $id_zone pour $id_auteur",'abonnement');
		sql_insertq("spip_zones_auteurs", array(
			"id_zone"=>$id_zone,
			"id_auteur"=>$id_auteur
		));
	}
}

?>
