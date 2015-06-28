<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Action de création / Modification d'un produit
 * @param string|null $arg
 * @return array
 */
function action_editer_produit_dist($arg=null) {
	include_spip('inc/autoriser');
	$err="";
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// si id_produit n'est pas un nombre, c'est une creation
	if (!$id_produit = intval($arg)) {
		$id_produit = produit_inserer(_request('id_parent'));
	}

	// Enregistre l'envoi dans la BD
	if ($id_produit > 0) $err = produit_modifier($id_produit);

	if ($err)
		spip_log("echec editeur produit: $err",_LOG_ERREUR);

	return array($id_produit,$err);
}

/**
 * Crée un nouveau produit et retourne son ID
 *
 * @param int $id_rubrique
 * @param array $set
 *   Un tableau avec les champs par défaut lors de l'insertion
 * @return int
 */
function produit_inserer($id_rubrique,$set=null) {
	$id_produit = false;
	
	// On insère seulement s'il y a une rubrique correcte
	if ($id_rubrique = intval($id_rubrique)){
		$champs = array();
		// Si id_rubrique vaut 0 ou n'est pas definie, creer le produit dans la premiere rubrique racine
		if (!$id_rubrique = intval($id_rubrique)) {
			$row = sql_fetsel('id_rubrique, id_secteur, lang', 'spip_rubriques', 'id_parent=0','', '0+titre,titre', "1");
			$id_rubrique = $row['id_rubrique'];
		} else $row = sql_fetsel('lang, id_secteur', 'spip_rubriques', "id_rubrique=".intval($id_rubrique));

		$lang_rub = $row['lang'];
		$champs['id_rubrique'] = $id_rubrique;
		// On propage le secteur
		$champs['id_secteur'] = $row['id_secteur'];
	
		// Dans un premier temps La langue a la creation : c'est la langue de la rubrique ou du site
		$champs['lang'] = $lang_rub ? $lang_rub : $GLOBALS['meta']['langue_site'];
		
		// La date de tout de suite
		$champs['date'] = date('Y-m-d H:i:s');
		
		// Le statut en cours de redac
		$champs['statut'] = 'prop';
		
		if ($set)
			$champs = array_merge($champs, $set);

		// Envoyer aux plugins avant insertion
		$champs = pipeline('pre_insertion',
			array(
				'args' => array(
					'table' => 'spip_produits',
				),
				'data' => $champs
			)
		);
		// Insérer l'objet
		$id_produit = sql_insertq('spip_produits', $champs);
		// Envoyer aux plugins après insertion
		pipeline('post_insertion',
			array(
				'args' => array(
					'table' => 'spip_produits',
				),
				'data' => $champs
			)
		);
	}

	return $id_produit;
}

/**
 * Appelle la fonction de modification d'un produit
 *
 * @param int $id_produit
 * @param array $set
 * @return string
 */
function produit_modifier($id_produit, $set=null) {
	$err = '';

	include_spip('inc/modifier');
	include_spip('inc/filtres');
	$c = collecter_requests(
		// white list
		objet_info('produit','champs_editables'),
		// black list
		array('date','statut','id_parent'),
		// donnees eventuellement fournies
		$set
	);

	// Si le produit est publie, invalider les caches et demander sa reindexation
	$t = sql_getfetsel("statut", "spip_produits", "id_produit=".intval($id_produit));
	$invalideur = $indexation = false;
	if ($t == 'publie') {
		$invalideur = "id='produit/$id_produit'";
		$indexation = true;
	}

	if ($err = objet_modifier_champs('produit', $id_produit,
		array(
			'nonvide' => array('titre' => _T('info_sans_titre')),
			'invalideur' => $invalideur,
			'indexation' => $indexation,
		),
		$c))
		return $err;

	// Modification de statut, changement de rubrique ?
	$c = collecter_requests(array('date', 'statut', 'id_parent'),array(),$set);
	$err = produit_instituer($id_produit, $c);

	return $err;
}


/**
 * Modifie des éléments à part que sont le parent, la date, le statut
 *
 * @param int $id_produit
 * @param array $c
 * @param bool $calcul_rub
 * @return string
 */
function produit_instituer($id_produit, $c, $calcul_rub=true){
	include_spip('inc/autoriser');
	include_spip('inc/rubriques');
	include_spip('inc/modifier');
	
	$row = sql_fetsel("statut, date, id_rubrique", "spip_produits", "id_produit=$id_produit");
	$id_rubrique = $row['id_rubrique'];
	$statut_ancien = $statut = $row['statut'];
	$date_ancienne = $date = $row['date'];
	$champs = array();
	
	$d = isset($c['date']) ? $c['date'] : null;
	$s = isset($c['statut']) ? $c['statut'] : $statut;
	
	// On ne modifie le statut que si c'est autorisé
	if ($s != $statut or ($d AND $d != $date)) {
		if (autoriser('publierdans', 'rubrique', $id_rubrique))
			$statut = $champs['statut'] = $s;
		else if (autoriser('modifier', 'produit', $id_produit) and $s != 'publie')
			$statut = $champs['statut'] = $s;
		else
			spip_log("editer_produit $id_produit refus " . join(' ', $c));

		// En cas de publication, fixer la date a "maintenant"
		// sauf si $c commande autre chose
		// ou si le produit est deja date dans le futur
		// En cas de proposition d'un produit (mais pas depublication), idem
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
	if ($id_parent = $c['id_parent']
		and $id_parent != $id_rubrique
		and (sql_fetsel('1', 'spip_rubriques', "id_rubrique=$id_parent"))
	){
		$champs['id_rubrique'] = $id_parent;

		// Si le produit était publié
		// et que le demandeur n'est pas admin de la rubrique
		// repasser le produit en statut 'proposé'.
		if ($statut == 'publie'
			and !autoriser('publierdans', 'rubrique', $id_rubrique)
		)
			$champs['statut'] = 'prop';
	}
	
	// Envoyer aux plugins
	$champs = pipeline(
		'pre_edition',
		array(
			'args' => array(
				'table' => 'spip_produits',
				'id_objet' => $id_produit,
				'action' => 'instituer',
				'statut_ancien' => $statut_ancien,
			),
			'data' => $champs
		)
	);

	if (!count($champs)) return;
	
	// Envoyer les modifications et calculer les héritages
	editer_produit_heritage($id_produit, $id_rubrique, $statut_ancien, $champs, $calcul_rub);
	
	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_produit/$id_produit'");
	
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
				'table' => 'spip_produits',
				'id_objet' => $id_produit,
				'action' => 'instituer',
				'statut_ancien' => $statut_ancien,
			),
			'data' => $champs
		)
	);
	
	// Notifications
	if ($notifications = charger_fonction('notifications', 'inc', true)) {
		$notifications('produit_instituer', $id_produit,
			array('statut' => $statut, 'statut_ancien' => $statut_ancien, 'date'=>$date)
		);
	}
	
	return '';
}

// Modifie le produit en calculant les héritages
function editer_produit_heritage($id_produit, $id_rubrique, $statut, $champs, $cond=true) {
	// Si on deplace le produit
	// changer aussi son secteur et sa langue (si héritée)
	if (isset($champs['id_rubrique'])) {
		$row_rub = sql_fetsel('id_secteur, lang', 'spip_rubriques', 'id_rubrique='.sql_quote($champs['id_rubrique']));

		$langue = $row_rub['lang'];
		$champs['id_secteur'] = $row_rub['id_secteur'];
		// Pour l'instant la langue est toujours héritée de la rubrique donc pas de test
		$champs['lang'] = $langue;
#		if (sql_fetsel('1', 'spip_produit', "id_produit=$id_produit and langue_choisie<>'oui' and lang<>" . sql_quote($langue))) {
#			$champs['lang'] = $langue;
#		}
	}

	if (!$champs) return;

	sql_updateq('spip_produits', $champs, "id_produit=$id_produit");

	// Changer le statut des rubriques concernees

	if ($cond) {
		include_spip('inc/rubriques');
		$postdate = ($GLOBALS['meta']["post_dates"] == "non" and isset($champs['date']) and (strtotime($champs['date']) < time())) ? $champs['date'] : false;
		calculer_rubriques_if($id_rubrique, $champs, $statut, $postdate);
	}
}

?>
