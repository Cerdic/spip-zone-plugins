<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2014                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
 \***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Appelle toutes les fonctions de modification d'un objet
 * $err est un message d'erreur eventuelle
 *
 * @param int $id
 * @param array|null $set
 * @return mixed|string
 */
function reservation_credit_mouvement_modifier($id, $set=null) {
	$table_sql = 'spip_reservation_credit_mouvements';
	$trouver_table = charger_fonction('trouver_table','base');
	$desc = $trouver_table($table_sql);
	if (!$desc OR !isset($desc['field'])) {
		spip_log("Objet 'spip_reservation_credit_mouvement' inconnu dans objet_modifier",_LOG_ERREUR);
		return _L("Erreur objet 'spip_reservation_credit_mouvement' inconnu");
	}
	include_spip('inc/modifier');

	$champ_date = '';
	if (isset($desc['date']) AND $desc['date'])
		$champ_date = $desc['date'];
	elseif (isset($desc['field']['date']))
		$champ_date = 'date';

	$white = array_keys($desc['field']);
	// on ne traite pas la cle primaire par defaut, notamment car
	// sur une creation, id_x vaut 'oui', et serait enregistre en id_x=0 dans la base
	$white = array_diff($white, array($desc['key']['PRIMARY KEY']));

	if (isset($desc['champs_editables']) AND is_array($desc['champs_editables'])) {
		$white = $desc['champs_editables'];
	}
	// Si il n'y pas encore de compte crédit pour l'email en question, on le crée
	$id_reservation_credit = _request('id_reservation_credit') ? _request('id_reservation_credit') : (isset($set['id_reservation_credit']) ? $set['id_reservation_credit'] : '');
	$email = _request('email') ? _request('email') : (isset($set['email']) ? $set['email'] : '');

	if (!$id_reservation_credit) {
		if (!$id_reservation_credit = sql_getfetsel('id_reservation_credit','spip_reservation_credits','email = '.sql_quote($email))){
		$action = charger_fonction('editer_objet', 'action');
		$reservation_credit = $action('new', 'reservation_credit');
		$id_reservation_credit = $reservation_credit[0];
		}
	}


	$c = collecter_requests(
		// white list
		$white,
		// black list
		array($champ_date,'statut','id_parent','id_secteur'),
		// donnees eventuellement fournies
			$set
		);

	  $c['id_reservation_credit'] = $id_reservation_credit;

	// Si l'objet est publie, invalider les caches et demander sa reindexation
	if (objet_test_si_publie('reservation_credit_mouvement',$id)){
		$invalideur = "id='reservation_credit_mouvement/$id'";
		$indexation = true;
	}
	else {
		$invalideur = "";
		$indexation = false;
	}

	if ($err = objet_modifier_champs('reservation_credit_mouvement', $id,
		array(
			'nonvide' => '',
			'invalideur' => $invalideur,
			'indexation' => $indexation,
			 // champ a mettre a date('Y-m-d H:i:s') s'il y a modif
			'date_modif' => (isset($desc['field']['date_modif'])?'date_modif':'')
		),
		$c))
	return $err;

	// Modification de statut, changement de rubrique ?
	// FIXME: Ici lorsqu'un $set est passé, la fonction collecter_requests() retourne tout
	//         le tableau $set hors black liste, mais du coup on a possiblement des champs en trop.
	$c = collecter_requests(array($champ_date, 'statut', 'id_parent'),array(),$set);
	$err = objet_instituer('reservation_credit_mouvement', $id, $c);

	// Actualiser le montant de crédit
	$sql = sql_select('montant,type,devise','spip_reservation_credit_mouvements',
			'id_reservation_credit=' . $id_reservation_credit);

	$montant = array();
	while ($data = sql_fetch($sql)) {
		$id = isset($data['devise']) ? $data['devise'] : 'sans_devise';
			if($data['type'] == 'credit')
		$montant[$id] = $montant[$id] + $data['montant'];
			elseif($data['type'] == 'debit')
		$montant[$id] = $montant[$id] - $data['montant'];
	}

	sql_updateq('spip_reservation_credits',array('credit' => serialize($montant)),'id_reservation_credit=' . $id_reservation_credit);

	return $err;
}
