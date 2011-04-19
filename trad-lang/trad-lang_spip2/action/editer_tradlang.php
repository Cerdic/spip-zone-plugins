<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_tradlang_dist($arg=null) {

	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// si id_tradlang n'est pas un nombre, c'est une creation 
	// mais on verifie qu'on a toutes les donnees qu'il faut.
	if (!$id_tradlang = intval($arg)) {
		return false;
	}

	// Enregistre l'envoi dans la BD
	$err = tradlang_set($id_tradlang);
 
	return array($id_tradlang,'');
}

function tradlang_set($id_tradlang,$set=null){
	$err = '';
	
	include_spip('inc/modifier');
	$c = collecter_requests(
		// white list
		array(
			'str', 'comm'
		),
		// black list
		array('statut'),
		// donnees eventuellement fournies
		$set
	);
	
	revision_tradlang($id_tradlang, $c);

	$c = collecter_requests(array('statut'),array(),$set);
	$err .= instituer_tradlang($id_tradlang, $c);

	return $err;
}

// $c est un array ('statut', 'id_parent' = changement de rubrique)
//
// statut et rubrique sont lies, car un admin restreint peut deplacer
// un article publie vers une rubrique qu'il n'administre pas
// http://doc.spip.org/@instituer_article
function instituer_tradlang($id_tradlang, $c) {

	include_spip('inc/autoriser');
	include_spip('inc/rubriques');
	include_spip('inc/modifier');

	$row = sql_fetsel("statut", "spip_tradlang", "id_tradlang=$id_tradlang");
	$statut_ancien = $statut = $row['statut'];
	$champs = array();

	$s = isset($c['statut'])?$c['statut']:$statut;

	// cf autorisations dans inc/instituer_article
	if ($s != $statut) {
		if (autoriser('modifier', 'tradlang', $tradlang))
			$statut = $champs['statut'] = $s;
		else
			spip_log("editer_tradlang $id_tradlang refus " . join(' ', $c));
	}

	
	// Envoyer aux plugins
	$champs = pipeline('pre_edition',
		array(
			'args' => array(
				'table' => 'spip_tradlang',
				'id_objet' => $id_tradlang,
				'action'=>'instituer',
				'statut_ancien' => $statut_ancien,
			),
			'data' => $champs
		)
	);

	if (!count($champs)) return;

	// Envoyer les modifs.
	sql_updateq('spip_tradlang', $champs, "id_tradlang=$id_tradlang");

	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='tradlang/$id_tradlang'");

	// Pipeline
	pipeline('post_edition',
		array(
			'args' => array(
				'table' => 'spip_tradlang',
				'id_objet' => $id_tradlang,
				'action'=>'instituer',
				'statut_ancien' => $statut_ancien,
			),
			'data' => $champs
		)
	);

	// Notifications
	if ($notifications = charger_fonction('notifications', 'inc')) {
		$notifications('instituertradlang', $id_tradlang,
			array('statut' => $statut, 'statut_ancien' => $statut_ancien)
		);
	}

	return ''; // pas d'erreur
}
?>