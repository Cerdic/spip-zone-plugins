<?php


/**
 * Gestion de l'action editer_definition
 *
 * @package SPIP\Dictionnaires\Actions
 */
 
// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Action d'édition d'une définition de dictionnaire dans la base de données dont
 * l'identifiant est donné en paramètre de cette fonction ou
 * en argument de l'action sécurisée
 *
 * Si aucun identifiant n'est donné, on crée alors une nouvelle définition.
 * 
 * @param null|int $arg
 *     Identifiant de la définition. En absence utilise l'argument
 *     de l'action sécurisée.
 * @return array
 *     Liste (identifiant de la définition, Texte d'erreur éventuel)
**/
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

	return array($id_definition, $err);
}


/**
 * Crée une nouvelle définition de dictionnaire
 *
 * @param array $champs
 *     Un tableau avec les champs par défaut lors de l'insertion
 * @return int
 *     Identifiant de la nouvelle définition
 */
function insert_definition($champs=array()) {
	$lang = "";
	// La langue a la creation : si les liens de traduction sont autorises
	// dans les definitions, on essaie avec la langue de l'auteur
	if (in_array('spip_definitions',explode(',',$GLOBALS['meta']['multi_objets']))) {
		lang_select($GLOBALS['visiteur_session']['lang']);
		if (in_array($GLOBALS['spip_lang'],
		explode(',', $GLOBALS['meta']['langues_multilingue']))) {
			$lang = $GLOBALS['spip_lang'];
		}
	}

	if (!$lang) {
		$lang = $GLOBALS['meta']['langue_site'];
	}
	
	$champs['lang'] = $lang;
	
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
 * Modifier une définition
 * 
 * @param int $id_definition
 *     Identifiant de la définition à modifier
 * @param array|null $set
 *     Couples (colonne => valeur) de données à modifier.
 *     En leur absence, on cherche les données dans les champs éditables
 *     qui ont été postés (via _request())
 * @param bool $purger_cache
 *     true pour purcher le cache des définitions au passage lors de l'institution
 * @return string|null
 *     Chaîne vide si aucune erreur,
 *     Null si aucun champ à modifier,
 *     Chaîne contenant un texte d'erreur sinon.
 */
function definition_set($id_definition, $set=null, $purger_cache=true) {
	$err = '';

	include_spip('base/objets');
	$desc = lister_tables_objets_sql('spip_definitions');

	include_spip('inc/modifier');
	$c = collecter_requests(
		// white list
		$desc['champs_editables'],
		// black list
		array(),
		// donnees eventuellement fournies
		$set
	);

	// Pour le parent on fera plus tard
	unset($c['id_dictionnaire']);

	if ($err = objet_modifier_champs('definition', $id_definition,
		array(
			'data' => $set,
			'nonvide' => array('titre' => _T('info_sans_titre'))
		),
		$c)) {
		return $err;
	}

	$c = collecter_requests(array('date', 'statut', 'id_dictionnaire'),array(),$set);
	$err = instituer_definition($id_definition, $c, $purger_cache);
	return $err;
}


/**
 * Instituer une définition : modifier son statut, date, parent
 *
 * @pipeline_appel pre_insertion
 * @pipeline_appel post_insertion
 * 
 * @param int $id_definition
 *     Identifiant de la définition
 * @param array $c
 *     Couples (colonne => valeur) des données à instituer
 * @param bool $purger_cache
 *     true pour purcher le cache des définitions au passage
 * @return null|string
 *     Null si aucun champ à modifier, chaîne vide sinon.
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
	if ($purger_cache and ((isset($champs['statut']) AND $champs['statut'] == 'publie')
		 OR $statut_ancien == 'publie'))
		{
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
