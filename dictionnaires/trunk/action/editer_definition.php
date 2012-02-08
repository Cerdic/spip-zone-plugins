<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Action de création / Modification d'un definition
 * @param unknown_type $arg
 * @return unknown_type
 */
function action_editer_definition_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// si id_definition n'est pas un nombre, c'est une creation
	if (!$id_definition = intval($arg)) {
		$id_definition = insert_definition();
	}

	// Enregistre l'envoi dans la BD
	if ($id_definition > 0) $err = definition_set($id_definition);

	if (_request('redirect')) {
		$redirect = parametre_url(urldecode(_request('redirect')),
			'id_definition', $id_definition, '&') . $err;

		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
	else
		return array($id_definition,$err);
}

/**
 * Crée un nouveau definition et retourne son ID
 *
 * @param array $champs Un tableau avec les champs par défaut lors de l'insertion
 * @return int id_definition
 */
function insert_definition($champs=array()) {
	// Envoyer aux plugins avant insertion
	$champs = pipeline('pre_insertion',
		array(
			'args' => array(
				'table' => 'spip_definitions',
			),
			'data' => $champs
		)
	);
	// Insérer l'objet
	$id_definition = sql_insertq('spip_definitions', $champs);
	// Envoyer aux plugins après insertion
	pipeline('post_insertion',
		array(
			'args' => array(
				'table' => 'spip_definitions',
				'id_objet' => $id_definition
			),
			'data' => $champs
		)
	);

	return $id_definition;
}

/**
 * Appelle la fonction de modification d'un definition
 *
 * @param int $id_definition
 * @param unknown_type $set
 * @return $err
 */
function definition_set($id_definition, $set=null, $purger_cache=true) {
	$err = '';

	include_spip('inc/saisies');
	$saisies = saisies_chercher_formulaire('editer_definition', array($id_definition));
	$champs = saisies_lister_champs($saisies, false);
	
	$c = array();
	foreach ($champs as $champ)
		$c[$champ] = _request($champ,$set);
	
	// Pour le parent on fera plus tard
	if (isset($c['id_dictionnaire'])){
		unset($c['id_dictionnaire']);
	}
	
	include_spip('inc/modifier');
	revision_definition($id_definition, $c);
	
	// Modification de statut, changement de rubrique ?
	$c = array();
	foreach (array(
		'date', 'statut', 'id_dictionnaire'
	) as $champ)
		$c[$champ] = _request($champ, $set);
	$err .= instituer_definition($id_definition, $c, $purger_cache);

	return $err;
}

/**
 * Enregistre une révision de definition
 *
 * @param int $id_definition
 * @param array $c
 * @return
 */
function revision_definition($id_definition, $c=false) {
	$invalideur = "id='id_definition/$id_definition'";

	modifier_contenu('definition', $id_definition,
		array(
			'nonvide' => array('titre' => _T('info_sans_titre')),
			'invalideur' => $invalideur
		),
		$c);

	return ''; // pas d'erreur
}

/**
 * Modifie des éléments à part
 *
 * @param int $id_definition
 * @param array $c
 * @return
 */
function instituer_definition($id_definition, $c, $purger_cache=true){
	include_spip('inc/autoriser');
	include_spip('inc/rubriques');
	include_spip('inc/modifier');
	
	$row = sql_fetsel('statut, date, id_dictionnaire', 'spip_definitions', "id_definition=$id_definition");
	$id_dictionnaire = $row['id_dictionnaire'];
	$statut_ancien = $statut = $row['statut'];
	$date_ancienne = $date = $row['date'];
	$champs = array();
	
	$d = isset($c['date']) ? $c['date'] : null;
	$s = isset($c['statut']) ? $c['statut'] : $statut;
	
	// On ne modifie le statut que si c'est autorisé
	if ($s != $statut or ($d AND $d != $date)) {
		if (autoriser('publierdans', 'dictionnaire', $id_dictionnaire))
			$statut = $champs['statut'] = $s;
		else if (autoriser('modifier', 'definition', $id_definition) and $s != 'publie')
			$statut = $champs['statut'] = $s;
		else
			spip_log("editer_definition $id_definition refus " . join(' ', $c));

		// En cas de publication, fixer la date a "maintenant"
		// sauf si $c commande autre chose
		// ou si le produit est deja date dans le futur
		// En cas de proposition d'une définition (mais pas depublication), idem
		if ($champs['statut'] == 'publie'
			or ($champs['statut'] == 'prop' and ($d or !in_array($statut_ancien, array('publie', 'prop'))))
		){
			if ($d or strtotime($d=$date)>time())
				$champs['date'] = $date = $d;
			else
				$champs['date'] = $date = date('Y-m-d H:i:s');
		}
	}
	
	// Verifier que la rubrique demandee existe et est differente
	// de la rubrique actuelle
	if ($id_dictionnaire_new = $c['id_dictionnaire']
		and $id_dictionnaire_new != $id_dictionnaire
		and (sql_fetsel('1', 'spip_dictionnaires', "id_dictionnaire=$id_dictionnaire_new"))
	){
		$champs['id_dictionnaire'] = $id_dictionnaire_new;

		// Si la définition était publiée
		// et que le demandeur n'est pas admin du dictionnaire où c'était
		// repasser le produit en statut 'proposé'.
		if ($statut == 'publie'
			and !autoriser('publierdans', 'dictionnaire', $id_dictionnaire)
		)
			$champs['statut'] = 'prop';
	}
	
	// Envoyer aux plugins
	$champs = pipeline(
		'pre_edition',
		array(
			'args' => array(
				'table' => 'spip_definitions',
				'id_objet' => $id_definition,
				'action' => 'instituer',
				'statut_ancien' => $statut_ancien,
			),
			'data' => $champs
		)
	);
	// Si à ce stade il n'y a pas de champs à modifier
	// on arrête là mais on refait quand même le cache des définitions si besoin
	if (!count($champs)){
		// On refait le cache des définitions si le nouveau ou l'ancien statut était publié
		if ($purger_cache and $statut_ancien == 'publie'){
			include_spip('inc/dictionnaires');
			dictionnaires_lister_definitions(true);
		}
		return;
	}
	
	// Envoyer les modifications et calculer les héritages
#	editer_definition_heritage($id_definition, $id_dictionnaire, $statut_ancien, $champs, $calcul_rub);
	sql_updateq('spip_definitions', $champs, "id_definition=$id_definition");
	
	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_definition/$id_definition'");
	
	if ($date) {
		$t = strtotime($date);
		$p = @$GLOBALS['meta']['date_prochain_postdate'];
		if ($t > time() AND (!$p OR ($t < $p))) {
			ecrire_meta('date_prochain_postdate', $t);
		}
	}
	
	// Pipeline
	pipeline(
		'post_edition',
		array(
			'args' => array(
				'table' => 'spip_definitions',
				'id_objet' => $id_definition,
				'action' => 'instituer',
				'statut_ancien' => $statut_ancien,
			),
			'data' => $champs
		)
	);
	
	// On refait le cache des définitions si le nouveau ou l'ancien statut était publié
	if ($purger_cache and ($champs['statut'] == 'publie' or $statut_ancien == 'publie')){
		include_spip('inc/dictionnaires');
		dictionnaires_lister_definitions(true);
	}
	
	// Notifications
	if ($notifications = charger_fonction('notifications', 'inc', true)) {
		$notifications('definition_instituer', $id_definition,
			array('statut' => $statut, 'statut_ancien' => $statut_ancien, 'date'=>$date)
		);
	}
	
	return '';
}

?>
