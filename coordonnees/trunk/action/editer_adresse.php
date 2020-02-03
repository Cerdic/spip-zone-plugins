<?php

/**
 * Plugin Coordonnées
 * Licence GPL (c) 2010 Matthieu Marcillaud
**/
if (!defined("_ECRIRE_INC_VERSION")) return;


function action_editer_adresse_dist($arg = null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// pas d'adresse ? on en cree une nouvelle, mais seulement si 'oui' en argument.
	if (!$id_adresse = intval($arg)) {
		if (!in_array($arg, array('oui', 'new'))) {
			include_spip('inc/headers');
			redirige_url_ecrire();
		}
		$id_adresse = insert_adresse();
	}

	$err = '';
	if ($id_adresse) {
		$err = revisions_adresses($id_adresse);
	}
	return array($id_adresse, $err);
}


function insert_adresse($c = '') {
	$champs = array(
		'voie' => _T('coordonnees:item_nouvelle_adresse')
	);

	// Envoyer aux plugins
	$champs = pipeline('pre_insertion', array(
		'args' => array(
			'table' => 'spip_adresses',
		),
		'data' => $champs
	));

	$id_adresse = sql_insertq("spip_adresses", $champs);

	if (!$c) {
		$c = array(
			'objet' => _request('objet'),
			'id_objet' => _request('id_objet'),
			'type' => _request('type')
		);
	}

	// ajouter la liaison si presente
	if (!empty($c['objet']) and !empty($c['id_objet'])) {
		if (empty($c['type'])) {
			$c['type'] = '';
		}
		$c['id_adresse'] = $id_adresse;
		sql_insertq("spip_adresses_liens", $c);
	}

	return $id_adresse;
}


// Enregistrer certaines modifications d'une adresse
function revisions_adresses($id_adresse, $c = false) {

	include_spip('inc/modifier');

	// recuperer les champs dans POST s'ils ne sont pas transmis
	if ($c === false) {
		$c = collecter_requests(
			objet_info('adresses', 'champs_editables'),
			array('maj')
		);
	}

	$retour = objet_modifier_champs('adresse', $id_adresse, array(
			'invalideur' => "id='id_adresse/$id_adresse'"
		),
		$c);

	sql_update("spip_adresses_liens", array(
			'type' => sql_quote(_request('type'))
		), "id_adresse=".intval($id_adresse)." AND id_objet=".intval(_request('id_objet'))." AND objet=".sql_quote(_request('objet')));

	return $retour;
}
