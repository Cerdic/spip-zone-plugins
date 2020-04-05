<?php

/**
 * Plugin Coordonnées
 * Licence GPL (c) 2010 Matthieu Marcillaud
**/
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_numero_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// pas de numero ? on en cree une nouvelle, mais seulement si 'oui' en argument.
	if (!$id_numero = intval($arg)) {
		if (!in_array($arg, array('oui', 'new'))) {
			include_spip('inc/headers');
			redirige_url_ecrire();
		}
		$id_numero = insert_numero();
	}

	if ($id_numero) $err = revisions_numeros($id_numero);
	return array($id_numero, $err);
}


function insert_numero($c = '') {
	$champs = array(
		'numero' => _T('coordonnees:item_nouveau_numero')
	);

	// Envoyer aux plugins
	$champs = pipeline('pre_insertion', array(
		'args' => array(
			'table' => 'spip_numeros',
		),
		'data' => $champs
	));

	$id_numero = sql_insertq("spip_numeros", $champs);

	if (!$c)
		$c = array('objet' => _request('objet'),
			'id_objet' => _request('id_objet'),
			'type' => _request('type'));

	// ajouter la liaison si presente
	if (!empty($c['objet']) AND !empty($c['id_objet'])) {
		if (empty($c['type'])) $c['type'] = '';
		$c['id_numero'] = $id_numero;
		sql_insertq("spip_numeros_liens", $c);
	}

	return $id_numero;
}


// Enregistrer certaines modifications d'un numero
function revisions_numeros($id_numero, $c=false) {

	// recuperer les champs dans POST s'ils ne sont pas transmis
	if ($c === false) {
		$c = array();
		foreach (array(
				'numero', 'titre') as $champ
		) {
			if (($a = _request($champ)) !== null) {
				$c[$champ] = $a;
			}
		}
	}

	include_spip('inc/modifier');
	modifier_contenu('numero', $id_numero, array(
			'invalideur' => "id='id_numero/$id_numero'"
		),
		$c);
	sql_update("spip_numeros_liens", array(
			'type'=>sql_quote(_request('type'))
		), "id_numero=".intval($id_numero)." AND id_objet=".intval(_request('id_objet'))." AND objet=".sql_quote(_request('objet')) );
}

?>