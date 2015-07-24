<?php

/**
 * Plugin Coordonnées
 * Licence GPL (c) 2015 Matthieu Marcillaud, Cyril Marion
**/
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_rezo_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// pas de réseau social ? on en cree un nouveau, mais seulement si 'oui' en argument.
	if (!$id_rezo = intval($arg)) {
		if (!in_array($arg, array('oui', 'new'))) {
			include_spip('inc/headers');
			redirige_url_ecrire();
		}
		$id_rezo = insert_rezo();
	}

	if ($id_rezo) $err = revisions_rezos($id_rezo);
	return array($id_rezo,$err);
}


function insert_rezo($c = '') {
	$champs = array(
		'titre' => _T('coordonnees:item_nouveau_rezo')
	);

	// Envoyer aux plugins
	$champs = pipeline('pre_insertion', array(
		'args' => array(
			'table' => 'spip_rezos',
		),
		'data' => $champs
	));

	$id_rezo = sql_insertq("spip_rezos", $champs);

	if (!$c)
		$c = array('objet' => _request('objet'),
			'id_objet' => _request('id_objet'),
			'type' => _request('type'));

	// ajouter la liaison si presente
	if (!empty($c['objet']) AND !empty($c['id_objet'])) {
		if (empty($c['type'])) $c['type'] = '';
		$c['id_rezo'] = $id_rezo;
		sql_insertq("spip_rezos_liens", $c);
	}

	return $id_rezo;
}


// Enregistrer certaines modifications d'un réseau social
function revisions_rezos($id_rezo, $c=false) {

	// recuperer les champs dans POST s'ils ne sont pas transmis
	if ($c === false) {
		$c = array();
		foreach (array(
				'titre', 'rezo') as $champ
		) {
			if (($a = _request($champ)) !== null) {
				$c[$champ] = $a;
			}
		}
	}

	include_spip('inc/modifier');
	modifier_contenu('rezo', $id_rezo, array(
			'invalideur' => "id='id_rezo/$id_rezo'"
		),
		$c);
	sql_update("spip_rezos_liens", array(
			'type'=>sql_quote(_request('type'))
		), "id_rezo=".intval($id_rezo)." AND id_objet=".intval(_request('id_objet'))." AND objet=".sql_quote(_request('objet')) );
}

?>
