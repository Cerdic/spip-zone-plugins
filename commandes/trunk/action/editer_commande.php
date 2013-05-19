<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function action_editer_commande_dist($arg=null) {

	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	
	// si id_commande n'est pas un nombre, c'est une creation
	if (!$id_commande = intval($arg)) {
		$id_commande = insert_commande(array('id_auteur'=>_request('id_auteur')));

	}
	
	// Enregistre l'envoi dans la BD
	if ($id_commande > 0) $err = commande_set($id_commande);

	return array($id_commande,$err);
}


/**
 * Crée une nouvelle commande et retourne son ID
 *
 * @param array $champs Un tableau avec les champs par défaut lors de l'insertion
 * @return int id_commande
 */
 
function insert_commande($champs=array()) {
	$id_commande = false;

	// On insère seulement s'il y a un auteur correct
	if (isset($champs['id_auteur']) and $champs['id_auteur'] = intval($champs['id_auteur'])){
		// Si id_auteur vaut 0 ou n'est pas defini, ne pas creer de commande et envoyer message impossible
		if (!$id_auteur = intval($champs['id_auteur'])) {
			return false; // ? minipress(); ?
		} 

	
		// La date de tout de suite
		$champs['date'] = date('Y-m-d H:i:s');
			
		// Le statut en cours
		$champs['statut'] = 'encours';
	
		// Envoyer aux plugins
		$champs = pipeline('pre_insertion',
			array(
				'args' => array(
					'table' => 'spip_commandes',
				),
				'data' => $champs
			)
		);
		
		// Insérer l'objet
		$id_commande = sql_insertq("spip_commandes", $champs);
		// Envoyer aux plugins après insertion
		pipeline('post_insertion',
			array(
				'args' => array(
					'table' => 'spip_commandes',
					'id_objet' => $id_commande
				),
				'data' => $champs
			)
		);
	}
	
	return $id_commande;
}

/**
 * Appelle la fonction de modification d'une commande
 *
 * @param int $id_commande
 * @param unknown_type $set
 * @return $err
 */
function commande_set($id_commande, $set=null) {
	$err = '';
	
	include_spip('inc/saisies');
	$saisies = saisies_chercher_formulaire('editer_commande', array($id_commande));
	$champs = saisies_lister_champs($saisies, false);
	
	$c = array();
	foreach ($champs as $champ)
		$c[$champ] = _request($champ,$set);
	
	
	include_spip('inc/modifier');
	revision_commande($id_commande, $c);
	
	// Modification de statut
	$c = array();
	foreach (array(
		'id_auteur', 'date', 'statut', 
	) as $champ)
		$c[$champ] = _request($champ, $set);
	$err .= instituer_commande($id_commande, $c);
	
	return $err;
}

/**
 * Enregistre une révision de commande
 *
 * @param int $id_commande
 * @param array $c
 * @return
 */
function revision_commande($id_commande, $c=false) {
	$invalideur = "id='id_commande/$id_commande'";

	modifier_contenu('commande', $id_commande,
		array(
			'nonvide' => array('statut' => _T('info_sans_statut')),
			'invalideur' => $invalideur
		),
		$c);

	return ''; // pas d'erreur
}

/**
 * Modifie des éléments à part que sont l'auteur, la date, le statut
 *
 * @param int $id_commande
 * @param array $c
 * @return
 */
function instituer_commande($id_commande, $c, $calcul_details=true){

	include_spip('inc/autoriser');
	include_spip('inc/modifier');
	
	$row = sql_fetsel("statut, date, id_auteur", "spip_commandes", "id_commande=$id_commande");
	$id_auteur = $row['id_auteur'];
	$statut_ancien = $statut = $row['statut'];
	$date_ancienne = $date = $row['date'];
	$champs = array();

	$d = isset($c['date']) ? $c['date'] : null;
	$s = isset($c['statut']) ? $c['statut'] : $statut;

	// On ne modifie le statut que si c'est autorisé
	if ($s != $statut or ($d AND $d != $date)) {
		//todo = donner l'autorisation a commandes_paypal_traitement_paypal
		//if (autoriser('modifier', 'commande', $id_commande))
			$statut = $champs['statut'] = $s;
		//else
		//	spip_log("editer_commande $id_commande refus " . join(' ', $c),'commandes');

		// Si on doit changer la date explicitement
		if ($d){
			$champs['date'] = $date = $d;
		}
	}


	$champs['id_auteur'] = $id_auteur;

	// Pipeline pre_edition
	// Les dates de paiement et d'envoi sont mises à jour via cette pipeline
	$champs = pipeline(
		'pre_edition',
		array(
			'args' => array(
				'table' => 'spip_commandes',
				'id_objet' => $id_commande,
				'action' => 'instituer',
				'statut_ancien' => $statut_ancien,
			),
			'data' => $champs
		)
	);

	if (!count($champs)) return;

	// Envoyer les modifications et calculer les héritages
	editer_commande_details($id_commande, $champs, $calcul_details);

	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_commande/$id_commande'");

	if ($date) {
		$t = strtotime($date);
		$p = @$GLOBALS['meta']['date_prochain_postdate'];
		if ($t > time() AND (!$p OR ($t < $p))) {
			ecrire_meta('date_prochain_postdate', $t);
		}
	}

	spip_log("instituer_commande : il y a un flux post_edition sur commande",'commandes');

	// Pipeline post-edition
	// Les notifications sont envoyées via cette pipeline
	pipeline(
		'post_edition',
		array(
			'args' => array(
				'table' => 'spip_commandes',
				'id_objet' => $id_commande,
				'action' => 'instituer',
				'statut_ancien' => $statut_ancien,
			),
			'data' => $champs
		)
	);

	return '';
}


// Modifie la commande en calculant les dependances des details
function editer_commande_details($id_commande, $champs, $cond=true) {
	
	if (!$champs) return;

	sql_updateq('spip_commandes', $champs, "id_commande=$id_commande");
	
	// Changer le statut des elements concernes ? (voir details)

	/*
	if ($cond) {
		include_spip('inc/rubriques');
		$postdate = ($GLOBALS['meta']["post_dates"] == "non" and isset($champs['date']) and (strtotime($champs['date']) < time())) ? $champs['date'] : false;
		calculer_rubriques_if($id_rubrique, $champs, $statut, $postdate);
	}
	*/
}

?>
