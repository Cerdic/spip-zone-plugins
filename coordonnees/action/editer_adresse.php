<?php

/**
 * Plugin Coordonnées 
 * Licence GPL (c) 2010 Matthieu Marcillaud 
**/
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_adresse_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// pas d'adresse ? on en cree une nouvelle, mais seulement si 'oui' en argument.
	if (!$id_adresse = intval($arg)) {
		if ($arg != 'oui') {
			include_spip('inc/headers');
			redirige_url_ecrire();
		}
		$id_adresse = insert_adresse();
	}

	if ($id_adresse) $err = revisions_adresses($id_adresse);
	return array($id_adresse,$err);
}


function insert_adresse() {
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

	// ajouter la liaison si presente
	if ($objet = _request('objet')
	and $id_objet = _request('id_objet')) {
		sql_insertq("spip_adresses_liens", array(
			'id_adresse' 	=> $id_adresse,
			'objet' 		=> $objet,
			'id_objet'		=> $id_objet,
		));
	}
	
	return $id_adresse;
}


// Enregistrer certaines modifications d'une adresse
function revisions_adresses($id_adresse, $c=false) {

	// recuperer les champs dans POST s'ils ne sont pas transmis
	if ($c === false) {
		$c = array();
		foreach (array(
				'voie', 'complement', 'boite_postale',
				'code_postal', 'ville', 'pays', 'titre') as $champ
		) {
			if (($a = _request($champ)) !== null) {
				$c[$champ] = $a;
			}
		}
	}

	include_spip('inc/modifier');
	modifier_contenu('adresse', $id_adresse, array(
			'invalideur' => "id='id_adresse/$id_adresse'"
		),
		$c);
}
?>
