<?php

/**
 * Plugin Coordonnées
 * Licence GPL (c) 2010 Matthieu Marcillaud
**/
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_email_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// pas d'email ? on en cree une nouvelle, mais seulement si 'oui' en argument.
	if (!$id_email = intval($arg)) {
		if (!in_array($arg, array('oui', 'new'))) {
			include_spip('inc/headers');
			redirige_url_ecrire();
		}
		$id_email = insert_email();
	}

	if ($id_email) $err = revisions_emails($id_email);
	return array($id_email, $err);
}


function insert_email() {
	$champs = array(
		'email' => _T('coordonnees:item_nouvel_email')
	);

	// Envoyer aux plugins
	$champs = pipeline('pre_insertion', array(
		'args' => array(
			'table' => 'spip_emails',
		),
		'data' => $champs
	));

	$id_email = sql_insertq("spip_emails", $champs);

	// ajouter la liaison si presente
	if ($objet = _request('objet')
		and $id_objet = _request('id_objet')
	) {
		$type = _request('type') ? _request('type') : '';
		sql_insertq("spip_emails_liens", array(
			'id_email' => $id_email,
			'objet' => $objet,
			'id_objet' => $id_objet,
			'type' => $type
		));
	}

	return $id_email;
}


// Enregistrer certaines modifications d'un email
function revisions_emails($id_email, $c=false) {

	// recuperer les champs dans POST s'ils ne sont pas transmis
	if ($c === false) {
		$c = array();
		foreach (array(
				'email', 'titre') as $champ
		) {
			if (($a = _request($champ)) !== null) {
				$c[$champ] = $a;
			}
		}
	}

	include_spip('inc/modifier');
	modifier_contenu('email', $id_email, array(
			'invalideur' => "id='id_email/$id_email'"
		),
		$c);
	sql_update("spip_emails_liens", array(
			'type'=>sql_quote(_request('type'))
		), "id_email=".intval($id_email)." AND id_objet=".intval(_request('id_objet'))." AND objet=".sql_quote(_request('objet')) );
}

?>