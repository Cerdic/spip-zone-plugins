<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2011                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_evenement_dist($arg=null) {

	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// Envoi depuis le formulaire d'edition d'un evenement
	if (!$id_evenement = intval($arg)) {
		$id_evenement = evenement_inserer(_request('id_parent'));
	}

	if (!$id_evenement)
		return array(0,''); // erreur

	$err = evenement_modifier($id_evenement);

	return array($id_evenement,$err);
}

/**
 * Inserer un evenement en base
 *
 * @param int $id_rubrique
 * @return int
 */
function evenement_inserer($id_rubrique) {

	include_spip('inc/rubriques');

	// Si id_rubrique vaut 0 ou n'est pas definie, creer l'evenement
	// dans la premiere rubrique racine
	if (!$id_rubrique = intval($id_rubrique)) {
		$id_rubrique = sql_getfetsel("id_rubrique", "spip_rubriques", "id_parent=0",'', '0+titre,titre', "1");
	}

	// La langue a la creation : c'est la langue de la rubrique
    // TODO JULIEN : si pas de rubrique => langue par defaut du site
	$row = sql_fetsel("lang, id_secteur", "spip_rubriques", "id_rubrique=$id_rubrique");
	$lang = $row['lang'];
	$id_rubrique = $row['id_secteur']; // garantir la racine

	$champs = array(
		'id_rubrique' => $id_rubrique,
		'statut' => 'prop',
		'date' => date('Y-m-d H:i:s'),
		'lang' => $lang,
		'langue_choisie' => 'non');
	
	// Envoyer aux plugins
	$champs = pipeline('pre_insertion',
		array(
			'args' => array(
				'table' => 'spip_evenements',
			),
			'data' => $champs
		)
	);
	$id_evenement = sql_insertq("spip_evenements", $champs);
	pipeline('post_insertion',
		array(
			'args' => array(
				'table' => 'spip_evenements',
				'id_objet' => $id_evenement
			),
			'data' => $champs
		)
	);
	return $id_evenement;
}


/**
 * Modifier un evenement en base
 * $c est un contenu (par defaut on prend le contenu via _request())
 *
 * http://doc.spip.org/@revisions_breves
 *
 * @param int $id_evenement
 * @param array $set
 * @return
 */
function evenement_modifier ($id_evenement, $set=null) {

	include_spip('inc/modifier');
	$c = collecter_requests(
		// white list
		array('titre', 'date_debut', 'date_fin', 'lieu', 'descriptif', 'texte'),
		// black list
		array('id_parent', 'statut'),
		// donnees eventuellement fournies
		$set
	);

	// Si l'evenement est publiee, invalider les caches et demander sa reindexation
	$t = sql_getfetsel("statut", "spip_evenements", "id_evenement=$id_evenement");
	if ($t == 'publie') {
		$invalideur = "id='evenement/$id_evenement'";
		$indexation = true;
	}

	modifier_contenu('evenement', $id_evenement,
		array(
			'nonvide' => array('titre' => _T('info_sans_titre')),
			'invalideur' => $invalideur,
			'indexation' => $indexation
		),
		$c);

	$c = collecter_requests(array('id_parent', 'statut'),array(),$set);
	$err = evenement_instituer($id_evenement, $c);
	return $err;
}

/**
 * Instituer un evenement : modifier son statut ou son parent
 *
 * @param int $id_evenement
 * @param array $c
 * @return string
 */
function evenement_instituer($id_evenement, $c) {
	$champs = array();

	// Changer le statut de l'evenement ?
	$row = sql_fetsel("statut, id_rubrique, lang, langue_choisie", "spip_evenements", "id_evenement=".intval($id_evenement));
	$id_rubrique = $row['id_rubrique'];

	$statut_ancien = $statut = $row['statut'];
	$langue_old = $row['lang'];
	$langue_choisie_old = $row['langue_choisie'];

	if ($c['statut']
	AND $c['statut'] != $statut
	AND autoriser('publierdans', 'rubrique', $id_rubrique)) {
		$statut = $champs['statut'] = $c['statut'];
	}

	// Changer de rubrique ?
	// Verifier que la rubrique demandee est a la racine et differente
	// de la rubrique actuelle
	if ($id_parent = intval($c['id_parent'])
	AND $id_parent != $id_rubrique
	AND (NULL !== ($lang=sql_getfetsel('lang', 'spip_rubriques', "id_parent=0 AND id_rubrique=".intval($id_parent))))) {
		$champs['id_rubrique'] = $id_parent;
		// - changer sa langue (si heritee)
		if ($langue_choisie_old != "oui") {
			if ($lang != $langue_old)
				$champs['lang'] = $lang;
		}
		// si l'evenement est publiee
		// et que le demandeur n'est pas admin de la rubrique
		// repasser l'evenement en statut 'prop'.
		if ($statut == 'publie') {
			if (!autoriser('publierdans','rubrique',$id_parent))
				$champs['statut'] = $statut = 'prop';
		}
	}

	// Envoyer aux plugins
	$champs = pipeline('pre_edition',
		array(
			'args' => array(
				'table' => 'spip_evenements',
				'id_objet' => $id_evenement,
				'action'=>'instituer',
				'statut_ancien' => $statut_ancien,
			),
			'data' => $champs
		)
	);

	if (!$champs) return;

	sql_updateq('spip_evenements', $champs, "id_evenement=".intval($id_evenement));

	//
	// Post-modifications
	//

	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='evenement/$id_evenement'");

	// Au besoin, changer le statut des rubriques concernees 
	include_spip('inc/rubriques');
	calculer_rubriques_if($id_rubrique, $champs, $statut_ancien);

	// Pipeline
	pipeline('post_edition',
		array(
			'args' => array(
				'table' => 'spip_evenements',
				'id_objet' => $id_evenement,
				'action'=>'instituer',
				'statut_ancien' => $statut_ancien,
			),
			'data' => $champs
		)
	);


	// Notifications
	if ($notifications = charger_fonction('notifications', 'inc')) {
		$notifications('instituerevenement', $id_evenement,
			array('statut' => $statut, 'statut_ancien' => $statut_ancien)
		);
	}

	return ''; // pas d'erreur
}

function insert_evenement($id_rubrique) {
	return evenement_inserer($id_rubrique);
}
function revisions_evenements ($id_evenement, $set=false) {
	return evenement_modifier($id_evenement,$set);
}
?>