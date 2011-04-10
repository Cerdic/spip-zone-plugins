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

	// pas d'adresse ? on en cree une nouvelle, mais seulement si 'oui' en argument.
	if (!$id_numero = intval($arg)) {
		if ($arg != 'oui') {
			include_spip('inc/headers');
			redirige_url_ecrire();
		}
		$id_numero = insert_numero();
	}

	if ($id_numero) $err = revisions_numeros($id_numero);
	return array($id_numero, $err);
}


function insert_numero() {
	$champs = array(
		'numero' => _T('coordonnees:item_nouvel_email')
	);
	
	// Envoyer aux plugins
	$champs = pipeline('pre_insertion', array(
		'args' => array(
			'table' => 'spip_numeros',
		),
		'data' => $champs
	));
	
	$id_numero = sql_insertq("spip_numeros", $champs);

	// ajouter la liaison si presente
	if ($objet = _request('objet')
		and $id_objet = _request('id_objet')
	) {
		$type = _request('type') ? _request('type') : '';
		sql_insertq("spip_numeros_liens", array(
			'id_numero' => $id_numero,
			'objet' => $objet,
			'id_objet' => $id_objet,
			'type' => $type
		));
	}
	
	return $id_numero;
}


// Enregistrer certaines modifications d'une adresse
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
}
?>
