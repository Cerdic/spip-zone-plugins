<?php
if (!defined("_ECRIRE_INC_VERSION"))
	return;

include_spip('action/editer_objet');

// https://code.spip.net/@action_editer_liaison_objet_dist
function action_editer_liaison_objet_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// Envoi depuis le formulaire d'edition d'une liaison_objet
	if (!$id_liaison_objet = intval($arg)) {
		$id_liaison_objet = liaison_objet_inserer(_request('id_objet'), _request('objet'));
	}

	if (!$id_liaison_objet)
		return array(
			0,
			''
		); // erreur

	$err = liaison_objet_modifier($id_liaison_objet);

	return array(
		$id_liaison_objet,
		$err
	);
}
function liaison_objet_inserer($id_objet, $objet) {
	$objet_dest = _request('objet_dest');
	$id_objet_dest = _request('id_objet_dest');
	$objet_table = $objet;
	if (!$id_objet) {
		$objet_table = $objet_dest;
		$objet = 'liaison_objet';
		$id_objet == $id_objet_dest;
	}
	if (!$lang = _request('lang')) {
		$table_sql = table_objet_sql($objet_table);
		$trouver_table = charger_fonction('trouver_table', 'base');
		$desc = $trouver_table($table_sql);
		if (!$desc or !isset($desc['field'])) {
			spip_log("Objet $objet inconnu dans objet_modifier", _LOG_ERREUR);
			return _L("Erreur objet $objet inconnu");
		}
		if ($desc['lang'])
			$lang = sql_getfetsel('lang', $table_sql, 'id_' . $objet_table . '=' . $id_objet);
		else
			$lang = $GLOBALS['visiteur_session']['lang'];
	}

	// Verifier l'ordre

	$where = array(
		'id_objet_dest=' . $id_objet_dest,
		'objet_dest=' . sql_quote($objet_dest),
		'lang=' . sql_quote($lang)
	);
	$verifier_ordre = charger_fonction('verifier_ordre', 'inc');

	// le nombre de résultats
	$ordre = $verifier_ordre($where);

	$champs = array(
		'objet_dest' => $objet_dest,
		'id_objet_dest' => $id_objet_dest,
		'lang' => $lang,
		'langue_choisie' => 'non',
		'ordre' => $ordre + 1  //
	);

	// Envoyer aux plugins
	$champs = pipeline('pre_insertion', array(
		'args' => array(
			'table' => 'spip_liaison_objets'
		),
		'data' => $champs
	));
	$id_liaison_objet = sql_insertq("spip_liaison_objets", $champs);
	pipeline('post_insertion', array(
		'args' => array(
			'table' => 'spip_liaison_objets',
			'id_objet' => $id_liaison_objet
		),
		'data' => $champs
	));
	return $id_liaison_objet;
}

/**
 * Modifier une liaison_objet en base
 * $c est un contenu (par defaut on prend le contenu via _request())
 *
 * https://code.spip.net/@revisions_liaison_objets
 *
 * @param int $id_liaison_objet
 * @param array $set
 * @return string|bool
 */
function liaison_objet_modifier($id_liaison_objet, $set = null) {
	include_spip('inc/modifier');

	$c = collecter_requests(
			// white list
			array(
				'titre',
				'descriptif',
				'url',
				'type_lien',
				'id_objet_dest',
				'objet_dest',
				'objet',
				'id_objet'
			),
			// black list
			array(
				'statut'
			),
			// donnees eventuellement fournies
			$set);

	// Si la liaison_objet est publiee, invalider les caches et demander sa reindexation
	$t = sql_fetsel("statut,lang,id_objet_dest,objet_dest,ordre", "spip_liaison_objets", "id_liaison_objet=$id_liaison_objet");
	if ($t['statut'] == 'publie') {
		$invalideur = "id='liaison_objet/$id_liaison_objet'";
		$indexation = true;
	}

	if ($err = objet_modifier_champs('liaison_objet', $id_liaison_objet, array(
		'nonvide' => array(
		),
		'invalideur' => $invalideur,
		'indexation' => $indexation
	), $c)) {
		return $err;
	}

	$c = collecter_requests(array(
		'statut'
	), array(), $set);
	$err = liaison_objet_instituer($id_liaison_objet, $c);
	return $err;
}

/**
 * Instituer une liaison_objet : modifier son statut ou son parent
 *
 * @param int $id_liaison_objet
 * @param array $c
 * @return string
 */
function liaison_objet_instituer($id_liaison_objet, $c) {
	$champs = array();

	// Changer le statut de la liaison_objet ?
	$statut = sql_getfetsel("statut", "spip_liaison_objets", "id_liaison_objet=" . intval($id_liaison_objet));

	$statut_ancien = $statut;

	if ($c['statut'] and $c['statut'] != $statut) {
		$statut = $champs['statut'] = $c['statut'];
	}

	// Envoyer aux plugins
	$champs = pipeline('pre_edition', array(
		'args' => array(
			'table' => 'spip_liaison_objets',
			'id_objet' => $id_liaison_objet,
			'action' => 'instituer',
			'statut_ancien' => $statut_ancien
		),
		'data' => $champs
	));

	if (!$champs)
		return;

	sql_updateq('spip_liaison_objets', $champs, "id_liaison_objet=" . intval($id_liaison_objet));

	//
	// Post-modifications
	//

	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='liaison_objet/$id_liaison_objet'");

	// Pipeline
	pipeline('post_edition', array(
		'args' => array(
			'table' => 'spip_liaison_objets',
			'id_objet' => $id_liaison_objet,
			'action' => 'instituer',
			'statut_ancien' => $statut_ancien
		),
		'data' => $champs
	));

	return ''; // pas d'erreur
}
