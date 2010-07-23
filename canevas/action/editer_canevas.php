<?php
/**
 * Plugin Canevas pour Spip 2.0
 * Licence GPL
 * 
 *
 */

function action_editer_canevas_dist(){

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// si id_canevas n'est pas un nombre, c'est une creation 
	if (!$id_canevas = intval($arg)) {
		if (!$id_canevas = canevas_action_insert_canevas())
			return array(false,_L('echec'));
	}
	
	$err = action_canevas_set($id_canevas);
	return array($id_canevas,$err);
}

function action_canevas_set($id_canevas){
	$err = '';

	$c = array();
	foreach (array(
		'objet','id_objet','statut','titre', 'texte',
	) as $champ)
		$c[$champ] = _request($champ);

	include_spip('inc/modifier');

	canevas_action_revision_canevas($id_canevas, $c);

	$err .= canevas_action_instituer_canevas($id_canevas, $c);

	return $err;
}

// creer un nouveau canevas
function canevas_action_insert_canevas(){

	$champs = array(
		'id_auteur' => $GLOBALS['visiteur_session']['id_auteur'],
		'date' => date('Y-m-d H:i:s'));

	// Envoyer aux plugins
	$champs = pipeline('pre_insertion',
		array(
			'args' => array(
			'table' => 'spip_canevas',
			),
			'data' => $champs
		)
	);
	$id_canevas = sql_insertq("spip_canevas", $champs);

	if (!$id_canevas){
		spip_log("canevas action insert canevas : impossible d'ajouter un canevas");
		return false;
	} 
	return $id_canevas;	
}

// Enregistre une revision de canevas
function canevas_action_revision_canevas ($id_canevas, $c=false) {

	modifier_contenu('canevas', $id_canevas,
		array(
			'nonvide' => array('titre' => _T('info_sans_titre'))
		),
		$c);

	return ''; // pas d'erreur
}


// $c est un array
function canevas_action_instituer_canevas($id_canevas, $c) {

	include_spip('inc/autoriser');
	include_spip('inc/modifier');

	$champs = array();

	if (!autoriser('modifier', 'canevas', $id_canevas)){
		spip_log("editer_canevas $id_canevas refus " . join(' ', $c));
		return false;
	}

	// Envoyer aux plugins
	$c = pipeline('pre_edition',
		array(
			'args' => array(
				'table' => 'spip_canevas',
				'id_objet' => $id_canevas
			),
			'data' => $c
		)
	);

	if (!count($c)) return;
	
	// modifier le canevas
	sql_updateq('spip_canevas',$c,"id_canevas=$id_canevas");

	// Pipeline
	pipeline('post_edition',
		array(
			'args' => array(
				'table' => 'spip_canevas',
				'id_objet' => $id_canevas
			),
			'data' => $c
		)
	);

	/* Notifications
	if ($notifications = charger_fonction('notifications', 'inc')) {
		$notifications('instituerevenement', $id_canevas,
			array('id_parent' => $champs['id_article'], 'id_parent_ancien' => $id_parent)
		);
	}
	*/
	return ''; // pas d'erreur
}


function canevas_action_supprime_canevas($id_canevas){
	
	if (intval($id_canevas)){
		sql_delete("spip_canevas", "id_canevas=".intval($id_canevas));
	}
	$id_canevas = 0;
	return $id_canevas;
}

?>