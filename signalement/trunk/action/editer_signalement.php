<?php
/**
 * Plugin Signalement
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * © 2012 - Distribue sous licence GNU/GPL
 *
 * Fonctions d'édition de Signalement
 *
 **/
 
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Appelle toutes les fonctions de modification d'un objet
 * $err est un message d'erreur eventuelle
 *
 * @param string $objet
 * @param int $id
 * @param array|null $set
 * @return mixed|string	
 */
function signalement_modifier($id, $set=null) {
	include_spip('inc/filtres'); // Pour objet_info
	$objet = 'signalement';

	include_spip('inc/modifier');

	$c = collecter_requests(
		// white list
		objet_info($objet,'champs_editables'),
		// black list
		array('date','statut','objet','id_objet'),
		// donnees eventuellement fournies
		$set
	);

	// Si l'objet est publie, invalider les caches et demander sa reindexation
	if (objet_test_si_publie($objet,$id)){
		$invalideur = "id='$objet/$id'";
		$indexation = true;
	}
	else {
		$invalideur = "";
		$indexation = false;
	}

	if ($err = objet_modifier_champs($objet, $id,
		array(
			'nonvide' => '',
			'invalideur' => $invalideur,
			'indexation' => $indexation,
			 // champ a mettre a date('Y-m-d H:i:s') s'il y a modif
			'date_modif' => ''
		),
		$c))
		return $err;

	// Modification de statut, changement de rubrique ?
	// FIXME: Ici lorsqu'un $set est passé, la fonction collecter_requests() retourne tout
	//         le tableau $set hors black liste, mais du coup on a possiblement des champs en trop. 
	$c = collecter_requests(array($champ_date, 'statut', 'id_objet','objet'),array(),$set);

	$err = signalement_instituer($id, $c);

	return $err;
}

/**
 * Inserer en base un signe
 * 
 * @param null $id_parent
 * @return bool|int
 */
function signalement_inserer($id_parent=null) {
	$champs = array();
	spip_log($id_parent,'elix');
	$champs['statut'] = 'prop';
	$champs['date'] = date('Y-m-d H:i:s');
	$champs['id_auteur'] = (is_null(_request('id_auteur'))?$GLOBALS['visiteur_session']['id_auteur']:_request('id_auteur'));
	
	// Envoyer aux plugins
	$champs = pipeline('pre_insertion',
		array(
			'args' => array(
				'table' => "spip_signalements",
			),
			'data' => $champs
		)
	);
	
	$id = sql_insertq("spip_signalements", $champs);
	
	pipeline('post_insertion',
		array(
			'args' => array(
				'table' => 'spip_signalements',
				'id_objet' => $id,
			),
			'data' => $champs
		)
	);

	return $id;
}

/**
 * $c est un array ('statut', 'id_parent' = changement de rubrique)
 * statut et rubrique sont lies, car un admin restreint peut deplacer
 * un objet publie vers une rubrique qu'il n'administre pas
 *
 * @param string $objet
 * @param int $id
 * @param array $c
 * @param bool $calcul_rub
 * @return mixed|string
 */
function signalement_instituer($id, $c, $calcul_rub=true) {

	include_spip('inc/autoriser');
	include_spip('inc/rubriques');
	include_spip('inc/modifier');

	$sel = array();
	$sel[] = "statut";

	$champ_date = 'date';
	$sel[] = "$champ_date as date" ;

	$row = sql_fetsel($sel, 'spip_signalements', 'id_signalement='.intval($id));
	
	$statut_ancien = $statut = $row['statut'];
	if($statut_ancien == 'prop'){
		$c['statut'] = 'publie';
	}
	$date_ancienne = $date = $row['date'];
	$champs = array();
	if($c['objet'] && $c['id_objet']){
		$champs['objet'] = $c['objet'];
		$champs['id_objet'] = $c['id_objet'];
	}

	$d = ($date AND isset($c[$champ_date]))?$c[$champ_date]:null;
	$s = ($statut AND isset($c['statut']))?$c['statut']:$statut;

	// cf autorisations dans inc/instituer_objet
	if ($s != $statut OR ($d AND $d != $date)) {
		if (autoriser('instituer', 'signalement', $id, null, array('statut'=>$s)))
			$statut = $champs['statut'] = $s;
		else if ($s!='publie' AND autoriser('modifier', 'signalement', $id))
			$statut = $champs['statut'] = $s;
		else
			spip_log("editer_objet $id refus " . join(' ', $c));

		// En cas de publication, fixer la date a "maintenant"
		// sauf si $c commande autre chose
		// ou si l'objet est deja date dans le futur
		// En cas de proposition d'un objet (mais pas depublication), idem
		if ($champ_date) {
			if ($champs['statut'] == 'publie'
			 OR ($champs['statut'] == 'prop' AND !in_array($statut_ancien, array('publie', 'prop')))
			 OR $d
			) {
				if ($d OR strtotime($d=$date)>time())
					$champs[$champ_date] = $date = $d;
				else
					$champs[$champ_date] = $date = date('Y-m-d H:i:s');
			}
		}
	}


	// Envoyer aux plugins
	$champs = pipeline('pre_edition',
		array(
			'args' => array(
				'table' => 'spip_signalements',
				'id_objet' => $id,
				'action'=>'instituer',
				'statut_ancien' => $statut_ancien,
				'date_ancienne' => $date_ancienne,
			),
			'data' => $champs
		)
	);

	if (!count($champs)) return '';
	
	sql_updateq("spip_signalements", $champs, 'id_signalement='.intval($id));
	
	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='signalement/$id'");

	// Pipeline
	pipeline('post_edition',
		array(
			'args' => array(
				'table' => 'spip_signalements',
				'id_objet' => $id,
				'action'=>'instituer',
				'statut_ancien' => $statut_ancien,
				'date_ancienne' => $date_ancienne,
			),
			'data' => $champs
		)
	);

	// Notifications
	if ($notifications = charger_fonction('notifications', 'inc')) {
		$notifications("instituersignalement", $id,
			array('statut' => $statut, 'statut_ancien' => $statut_ancien, 'date'=>$date, 'date_ancienne' => $date_ancienne)
		);
	}

	return ''; // pas d'erreur
}

?>
