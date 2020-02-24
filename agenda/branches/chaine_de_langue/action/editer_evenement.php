<?php
/**
 * Plugin Agenda 4 pour Spip 3.0
 * Licence GPL 3
 *
 * 2006-2011
 * Auteurs : cf paquet.xml
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Edition d'un evenement
 * @param string $arg
 * @return array
 */
function action_editer_evenement_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// si id_evenement n'est pas un nombre, c'est une creation
	// mais on verifie qu'on a toutes les donnees qu'il faut.
	if (!$id_evenement = intval($arg)) {
		$id_parent = _request('id_parent');
		if (!$id_evenement = evenement_inserer($id_parent)) {
			return array(false,_L('echec'));
		}
	}

	$err = evenement_modifier($id_evenement);
	return array($id_evenement,$err);
}

/**
 * Creer un nouvel evenement
 *
 * @param int $id_article
 * @param array $set
 * @return int
 */
function evenement_inserer($id_article, $set=null) {
	include_spip('inc/autoriser');
	if (!autoriser('creerevenementdans', 'article', $id_article)) {
		spip_log('agenda action formulaire article : auteur '.$GLOBALS['visiteur_session']['id_auteur']." n'a pas le droit de creer un evenement dans article $id_article", 'agenda');
		return false;
	}

	// support pour l'ancien format avec $id_evenement_source en second argument
	if (is_scalar($set) and intval($set)) {
		$set = array(
			'id_evenement_source' => intval($set),
		);
	}

	$champs = array();
	if ($set and is_array($set)) {
		$champs = $set;
	}
	$champs['id_article'] = intval($id_article);
	if (empty($champs['statut'])) {
		$champs['statut'] = 'prop';
	}

	// Envoyer aux plugins
	$champs = pipeline(
		'pre_insertion',
		array(
			'args' => array(
				'table' => 'spip_evenements',
			),
			'data' => $champs
		)
	);

	// nouvel evenement
	$id_evenement = sql_insertq('spip_evenements', $champs);

	pipeline(
		'post_insertion',
		array(
			'args' => array(
				'table' => 'spip_evenements',
				'id_objet' => $id_evenement
			),
			'data' => $champs
		)
	);

	if (!$id_evenement) {
		spip_log("agenda action formulaire evenement : impossible d'ajouter un evenement", 'agenda');
		return false;
	}
	return $id_evenement;
}

/**
 * Modifier un evenement existant
 *
 * @param int $id_evenement
 * @param array $set
 * @param bool $propagate
 * @return bool|string
 */
function evenement_modifier($id_evenement, $set = null, $propagate=true) {

	include_spip('inc/modifier');
	include_spip('inc/filtres');
	$c = collecter_requests(
		// white list
		objet_info('evenement', 'champs_editables'),
		// black list
		array('statut', 'id_article'),
		// donnees eventuellement fournies
		$set
	);

	// Si l'evenement est publie, invalider les caches et demander sa reindexation
	$t = sql_getfetsel('statut', 'spip_evenements', 'id_evenement='.intval($id_evenement));
	$invalideur = $indexation = false;
	if ($t == 'publie') {
		$invalideur = "id='evenement/$id_evenement'";
		$indexation = true;
	}

	if ($err = objet_modifier_champs(
		'evenement',
		$id_evenement,
		array(
			'data' => $set,
			'nonvide' => array('titre' => _T('info_nouvel_evenement').' '._T('info_numero_abbreviation').$id_evenement),
			'invalideur' => $invalideur,
			'indexation' => $indexation,
		),
		$c
	)) {
		return $err;
	}

	if ($propagate) {
		if (!is_null($repetitions = _request('repetitions', $set))) {
			evenement_modifier_repetitions_filles($id_evenement, $repetitions);
		}
		else {
			evenement_modifier_repetitions_soeurs($id_evenement);
		}
	}

	// Modification de statut, changement de parent ?
	$c = collecter_requests(array('statut', 'id_parent'), array(), $set);
	$err = evenement_instituer($id_evenement, $c);

	return $err;
}

/**
 * Recupere les timestamp des repetitions
 * @param string $repetitions
 * @return array
 */
function agenda_recup_repetitions($repetitions) {
	include_spip('inc/filtres');
	$repetitions = preg_split(',[^0-9\-\/],', $repetitions);
	// gestion des repetitions
	$rep = array();
	foreach ($repetitions as $date) {
		if (strlen($date)) {
			$date = recup_date($date);
			if ($date = mktime(0, 0, 0, $date[1], $date[2], $date[0])) {
				$rep[] = $date;
			}
		}
	}
	return $rep;
}

/**
 * Propager les modifs d'un evenement a ses repetitions filles
 * @param int $id_evenement
 * @param string $repetitions
 */
function evenement_modifier_repetitions_filles($id_evenement, $repetitions = '') {
	if ($row_source = sql_fetsel('*', 'spip_evenements', 'id_evenement='.intval($id_evenement))){
		// Si ce n'est pas un événement source, on n'a rien à faire ici
		if ($row_source['id_evenement_source']!=0){
			return;
		}
		$reps = agenda_recup_repetitions($repetitions);

		// On ne garde que les données correctes pour une modification à propager
		$c = collecter_requests(
		// white list
			objet_info('evenement', 'champs_editables'),
			// black list
			array('id_evenement', 'id_evenement_source', 'modif_synchro_source'),
			// donnees fournies
			$row_source
		);

		agenda_action_update_repetitions($id_evenement, $row_source['modif_synchro_source'], $c , $reps);
	}
}

/**
 * Propager les modifs d'un evenement a ses repetitions soeurs et a sa mere
 * @param int $id_evenement
 */
function evenement_modifier_repetitions_soeurs($id_evenement) {
	if ($row = sql_fetsel('*', 'spip_evenements', 'id_evenement='.intval($id_evenement))){
		// Si ce n'est pas une repetition, on n'a rien à faire ici
		if ($row['id_evenement_source']==0){
			return;
		}
		// si on est plus synchro avec les autres, ne rien faire
		if ($row['modif_synchro_source']==0){
			return;
		}

		// On ne garde que les données correctes pour une modification à propager
		$c = collecter_requests(
		// white list
			objet_info('evenement', 'champs_editables'),
			// black list
			array('id_evenement', 'id_evenement_source', 'modif_synchro_source', 'date_debut', 'date_fin', 'horaire', 'timezone_affiche'),
			// donnees fournies
			$row
		);

		agenda_action_update_repetitions($row['id_evenement_source'], true, $c);
		$row_source = sql_fetsel('*', 'spip_evenements', 'id_evenement='.intval($row['id_evenement_source']));
		if ($row_source['modif_synchro_source']) {
			evenement_modifier($row_source['id_evenement'], $c, false);
		}
	}
}


function agenda_action_update_repetitions($id_evenement_source, $modif_flag, $set, $repetitions=null) {

	$date_debut = $date_fin = $duree = null;

	// On calcule la durée en secondes de l'événement source pour la reporter telle quelle aux répétitions
	if (isset($set['date_debut']) and isset($set['date_fin'])) {
		$date_debut = strtotime($set['date_debut']);
		$date_fin = strtotime($set['date_fin']);
		$duree = $date_fin - $date_debut;
	}
	unset($set['date_debut']);
	unset($set['date_fin']);

	$repetitions_updated = array();
	// mettre a jour toutes les repetitions *avec le flag modif_synchro_source=1* deja existantes ou les supprimer si il n'y a plus lieu
	$res = sql_select('id_evenement,date_debut,modif_synchro_source', 'spip_evenements', 'id_evenement_source='.intval($id_evenement_source));
	while ($row = sql_fetch($res)) {
		$date = strtotime(date('Y-m-d', strtotime($row['date_debut'])));
		if (is_null($repetitions) or in_array($date, $repetitions)) {
			// Cette répétition existe déjà
			$repetitions_updated[] = $date;

			// si besoin on la met a jour
			// cad si le flag modif_synchro_source vaut 1 sur l'evenement source ET destination
			if ($modif_flag and $row['modif_synchro_source']){
				if (!is_null($date_debut)) {
					// On calcule les nouvelles dates/heures en reportant la durée de la source
					$update_date_debut = date('Y-m-d', $date) . ' ' . date('H:i:s', $date_debut);
					$update_date_fin = date('Y-m-d H:i:s', strtotime($update_date_debut)+$duree);

					// Seules les dates sont changées dans les champs de la source
					$set['date_debut'] = $update_date_debut;
					$set['date_fin'] = $update_date_fin;
				}
				// mettre a jour l'evenement
				evenement_modifier($row['id_evenement'], $set, false);
			}
		} else {
			// il est supprime *si* modif_synchro_source vaut 1
			if ($row['modif_synchro_source']) {
				sql_delete('spip_evenements', 'id_evenement='.intval($row['id_evenement']));
			}
		}
	}

	if (!is_null($repetitions)) {
		// regarder les repetitions a ajouter qui sont du coup dupliquees depuis la source
		foreach ($repetitions as $date) {
			if (!in_array($date, $repetitions_updated)) {
				// On calcule les dates/heures en reportant la durée de la source
				$update_date_debut = date('Y-m-d', $date).' '.date('H:i:s', $date_debut);
				$update_date_fin = date('Y-m-d H:i:s', strtotime($update_date_debut)+$duree);

				// Seules les dates sont changées dans les champs de la source
				$set['date_debut'] = $update_date_debut;
				$set['date_fin'] = $update_date_fin;
				$set['id_evenement_source'] = $id_evenement_source;

				// On crée la nouvelle répétition
				if ($id_evenement_new = evenement_inserer($set['id_article'], $set)) {
					// Pour les créations il ne faut pas oublier de dupliquer les liens
					// En effet, sinon les documents insérés avant la création (0-id_auteur) ne seront pas dupliqués
					include_spip('action/editer_liens');
					objet_dupliquer_liens('evenement', $id_evenement_source, $id_evenement_new);
				}
				else {
					spip_log("agenda_action_update_repetitions : echec ajout repetition " . json_encode($set), "agenda" . _LOG_ERREUR);
				}
			}
		}
	}
}

/**
 * Instituer un evenement
 *
 * @param int $id_evenement
 * @param array $c
 * @return bool|string
 */
function evenement_instituer($id_evenement, $c) {

	include_spip('inc/autoriser');
	include_spip('inc/modifier');

	$row = sql_fetsel('id_article, statut', 'spip_evenements', 'id_evenement='.intval($id_evenement));
	$id_parent  = $id_parent_ancien = $row['id_article'];
	$statut = $statut_ancien = $row['statut'];
	$propager_statut_toutes_repetitions = false;

	$champs = array();

	if (!autoriser('modifier', 'article', $id_parent)
		or (isset($c['id_parent'])
		and !autoriser('modifier', 'article', $c['id_parent']))) {
		spip_log("editer_evenement $id_evenement refus " . join(' ', $c));
		return false;
	}

	// Verifier que l'article demande existe et est different
	// de l'article actuel
	if (isset($c['id_parent'])
		and $c['id_parent'] != $id_parent
		and (sql_countsel('spip_articles', 'id_article='.intval($c['id_parent'])))) {
		$id_parent = $champs['id_article'] = $c['id_parent'];
	}

	$sa = sql_getfetsel('statut', 'spip_articles', 'id_article='.intval($id_parent));
	if ($id_parent
		and (
			$id_parent !== $id_parent_ancien
			or $statut == '0'
		)) {
		switch ($sa) {
			case 'publie':
				// statut par defaut si besoin
				if ($statut == '0') {
					$champs['statut'] = $statut = 'publie';
				}
				break;
			case 'poubelle':
				// si article a la poubelle, evenement aussi
				$champs['statut'] = $statut = 'poubelle';
				$propager_statut_toutes_repetitions = true;
				break;
			default:
				// pas de publie ni 0 si article pas publie
				if (in_array($statut, array('publie','0'))) {
					$champs['statut'] = $statut = 'prop';
					$propager_statut_toutes_repetitions = true;
				}
				break;
		}
	}

	// si pas d'article lie, et statut par defaut
	// on met en propose
	if ($statut=='0') {
		$champs['statut'] = $statut = 'prop';
		$propager_statut_toutes_repetitions = true;
	}

	if (isset($c['statut'])
		and $s = $c['statut']
		and $s != $statut) {
		// pour instituer un evenement il faut avoir le droit d'instituer l'article associe avec le meme statut
		if (autoriser('instituer', 'article', $id_parent, null, array('statut'=>$s))
			and ($sa=='publie' or $s!=='publie')) {
			$champs['statut'] = $statut = $s;
		} else {
			spip_log("editer_evenement $id_evenement refus " . join(' ', $c));
		}
	}

	// Envoyer aux plugins
	$champs = pipeline(
		'pre_edition',
		array(
			'args' => array(
				'table' => 'spip_evenements',
				'action'=>'instituer',
				'id_objet' => $id_evenement,
				'id_parent_ancien' => $id_parent_ancien,
				'statut_ancien' => $statut_ancien,
			),
			'data' => $champs
		)
	);

	if (!count($champs)) {
		return;
	}

	// Envoyer les modifs sur l'evenement et toutes ses repetitons synchro
	$ids = sql_allfetsel('id_evenement', 'spip_evenements', 'modif_synchro_source=1 and id_evenement_source='.intval($id_evenement));
	$ids = array_column($ids, 'id_evenement');
	$ids[] = intval($id_evenement);
	sql_updateq('spip_evenements', $champs, sql_in('id_evenement', $ids));

	// et les eventuelles propagations aux repetitions non synchro
	if (!empty($champs['id_article'])) {
		sql_updateq('spip_evenements', ['id_article' => $champs['id_article']], 'modif_synchro_source=0 and id_evenement_source='.intval($id_evenement));
	}
	if (!empty($champs['statut']) and $propager_statut_toutes_repetitions) {
		sql_updateq('spip_evenements', ['statut' => $champs['statut']], 'modif_synchro_source=0 and id_evenement_source='.intval($id_evenement));
	}

	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_article/$id_parent_ancien'");
	suivre_invalideur("id='id_article/$id_parent'");

	// Pipeline
	pipeline(
		'post_edition',
		array(
			'args' => array(
				'table' => 'spip_evenements',
				'action'=>'instituer',
				'id_objet' => $id_evenement,
				'id_parent_ancien' => $id_parent_ancien,
				'statut_ancien' => $statut_ancien,
			),
			'data' => $champs
		)
	);

	// Notifications
	if ($notifications = charger_fonction('notifications', 'inc')) {
		$notifications('instituerevenement', $id_evenement,
			array('id_parent' => $id_parent, 'statut' => $statut, 'id_parent_ancien' => $id_parent, 'statut_ancien' => $statut_ancien)
		);
	}

	return ''; // pas d'erreur
}
