<?php
/**
 * Plugin Gabarits pour Spip 2.0
 * Licence GPL
 * 
 *
 */

function action_editer_gabarit_dist(){

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// si id_gabarit n'est pas un nombre, c'est une creation 
	if (!$id_gabarit = intval($arg)) {
		if (!$id_gabarit = gabarits_action_insert_gabarit())
			return array(false,_L('echec'));
	}
	
	$err = action_gabarits_set($id_gabarit);
	return array($id_gabarit,$err);
}

function action_gabarits_set($id_gabarit){
	$err = '';

	$c = array();
	foreach (array(
		'objet','id_objet','statut','titre', 'texte',
	) as $champ)
		$c[$champ] = _request($champ);

	include_spip('inc/modifier');

	gabarits_action_revision_gabarit($id_gabarit, $c);

	$err .= gabarits_action_instituer_gabarit($id_gabarit, $c);

	return $err;
}

// creer un nouveau gabarit
function gabarits_action_insert_gabarit(){

	$champs = array(
		'id_auteur' => $GLOBALS['visiteur_session']['id_auteur'],
		'date' => date('Y-m-d H:i:s'));

	// Envoyer aux plugins
	$champs = pipeline('pre_insertion',
		array(
			'args' => array(
			'table' => 'spip_gabarits',
			),
			'data' => $champs
		)
	);
	$id_gabarit = sql_insertq("spip_gabarits", $champs);

	if (!$id_gabarit){
		spip_log("gabarits action insert gabarit : impossible d'ajouter un gabarit");
		return false;
	} 
	return $id_gabarit;	
}

// Enregistre une revision de gabarit
function gabarits_action_revision_gabarit ($id_gabarit, $c=false) {

	modifier_contenu('gabarit', $id_gabarit,
		array(
			'nonvide' => array('titre' => _T('info_sans_titre'))
		),
		$c);

	return ''; // pas d'erreur
}


// $c est un array
function gabarits_action_instituer_gabarit($id_gabarit, $c) {

	include_spip('inc/autoriser');
	include_spip('inc/modifier');

	$champs = array();

	if (!autoriser('modifier', 'gabarit', $id_gabarit)){
		spip_log("editer_gabarit $id_gabarit refus " . join(' ', $c));
		return false;
	}

	// Envoyer aux plugins
	$c = pipeline('pre_edition',
		array(
			'args' => array(
				'table' => 'spip_gabarits',
				'id_objet' => $id_gabarit
			),
			'data' => $c
		)
	);

	if (!count($c)) return;
	
	// modifier le gabarit
	sql_updateq('spip_gabarits',$c,"id_gabarit=$id_gabarit");

	// Pipeline
	pipeline('post_edition',
		array(
			'args' => array(
				'table' => 'spip_gabarits',
				'id_objet' => $id_gabarit
			),
			'data' => $c
		)
	);

	/* Notifications
	if ($notifications = charger_fonction('notifications', 'inc')) {
		$notifications('instituergabarit', $id_gabarit,
			array('id_parent' => $champs['id_article'], 'id_parent_ancien' => $id_parent)
		);
	}
	*/
	return ''; // pas d'erreur
}


function gabarits_action_supprime_gabarit($id_gabarit){
	
	if (intval($id_gabarit)){
		sql_delete("spip_gabarits", "id_gabarit=".intval($id_gabarit));
	}
	$id_gabarit = 0;
	return $id_gabarit;
}

?>