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

// http://doc.spip.org/@action_editer_feedback_dist
function action_editer_feedback_dist($arg=null) {

	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// Envoi depuis le formulaire d'edition d'une feedback
	if (!$id_feedback = intval($arg)) {
		$id_feedback = feedback_inserer(_request('id_parent'));
	}

	if (!$id_feedback)
		return array(0,''); // erreur

	$err = feedback_modifier($id_feedback);

	return array($id_feedback,$err);
}

/**
 * Inserer une feedback en base
 * http://doc.spip.org/@insert_feedback
 *
 * @param int $id_rubrique
 * @return int
 */
function feedback_inserer($id_rubrique) {

	include_spip('inc/rubriques');

	// Si id_rubrique vaut 0 ou n'est pas definie, creer le feedback
	// dans la premiere rubrique racine
	if (!$id_rubrique = intval($id_rubrique)) {
		$id_rubrique = sql_getfetsel("id_rubrique", "spip_rubriques", "id_parent=0",'', '0+titre,titre', "1");
	}

	// La langue a la creation : c'est la langue de la rubrique
	$row = sql_fetsel("lang, id_secteur", "spip_rubriques", "id_rubrique=$id_rubrique");
	$lang = $row['lang'];
	$id_rubrique = $row['id_secteur']; // garantir la racine

	$champs = array(
		'id_rubrique' => $id_rubrique,
		'statut' => 'prop',
		'date_heure' => date('Y-m-d H:i:s'),
		'lang' => $lang,
		'langue_choisie' => 'non');
	
	// Envoyer aux plugins
	$champs = pipeline('pre_insertion',
		array(
			'args' => array(
				'table' => 'spip_feedbacks',
			),
			'data' => $champs
		)
	);
	$id_feedback = sql_insertq("spip_feedbacks", $champs);
	pipeline('post_insertion',
		array(
			'args' => array(
				'table' => 'spip_feedbacks',
				'id_objet' => $id_feedback
			),
			'data' => $champs
		)
	);
	return $id_feedback;
}


/**
 * Modifier une feedback en base
 * $c est un contenu (par defaut on prend le contenu via _request())
 *
 * http://doc.spip.org/@revisions_feedback
 *
 * @param int $id_feedback
 * @param array $set
 * @return string|bool
 */
function feedback_modifier($id_feedback, $set=null) {

	include_spip('inc/modifier');
	$c = collecter_requests(
		// white list
		array('titre', 'texte'/*, 'lien_titre', 'lien_url'*/),
		// black list
		array('id_parent', 'statut'),
		// donnees eventuellement fournies
		$set
	);

	// Si le feedback est publiee, invalider les caches et demander sa reindexation
	$t = sql_getfetsel("statut", "spip_feedbacks", "id_feedback=$id_feedback");
	if ($t == 'publie') {
		$invalideur = "id='feedback/$id_feedback'";
		$indexation = true;
	}

	if ($err = objet_modifier_champs('feedback', $id_feedback,
		array(
			'nonvide' => array('titre' => _T('feedback:titre_nouvelle_feedback')." "._T('info_numero_abbreviation').$id_feedback),
			'invalideur' => $invalideur,
			'indexation' => $indexation
		),
		$c))
		return $err;

	$c = collecter_requests(array('id_parent', 'statut'),array(),$set);
	$err = feedback_instituer($id_feedback, $c);
	return $err;
}

/**
 * Instituer une feedback : modifier son statut ou son parent
 *
 * @param int $id_feedback
 * @param array $c
 * @return string
 */
function feedback_instituer($id_feedback, $c) {
	$champs = array();

	// Changer le statut de la feedback ?
	$row = sql_fetsel("statut, id_rubrique,lang, langue_choisie", "spip_feedbacks", "id_feedback=".intval($id_feedback));
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
		// si la feedback est publiee
		// et que le demandeur n'est pas admin de la rubrique
		// repasser la feedback en statut 'prop'.
		if ($statut == 'publie') {
			if (!autoriser('publierdans','rubrique',$id_parent))
				$champs['statut'] = $statut = 'prop';
		}
	}

	// Envoyer aux plugins
	$champs = pipeline('pre_edition',
		array(
			'args' => array(
				'table' => 'spip_feedbacks',
				'id_objet' => $id_feedback,
				'action'=>'instituer',
				'statut_ancien' => $statut_ancien,
			),
			'data' => $champs
		)
	);

	if (!$champs) return;

	sql_updateq('spip_feedbacks', $champs, "id_feedback=".intval($id_feedback));

	//
	// Post-modifications
	//

	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='feedback/$id_feedback'");

	// Au besoin, changer le statut des rubriques concernees 
	include_spip('inc/rubriques');
	calculer_rubriques_if($id_rubrique, $champs, $statut_ancien);

	// Pipeline
	pipeline('post_edition',
		array(
			'args' => array(
				'table' => 'spip_feedbacks',
				'id_objet' => $id_feedback,
				'action'=>'instituer',
				'statut_ancien' => $statut_ancien,
			),
			'data' => $champs
		)
	);


	// Notifications
	if ($notifications = charger_fonction('notifications', 'inc')) {
		$notifications('instituerfeedback', $id_feedback,
			array('statut' => $statut, 'statut_ancien' => $statut_ancien)
		);
	}

	return ''; // pas d'erreur
}

function insert_feedback($id_rubrique) {
	return feedback_inserer($id_rubrique);
}
function revisions_feedback ($id_feedback, $set=false) {
	return feedback_modifier($id_feedback,$set);
}
?>