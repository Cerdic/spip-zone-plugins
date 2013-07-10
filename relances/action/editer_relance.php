<?php

/**
 * Plugin Coordonnées 
 * Licence GPL (c) 2010 Matthieu Marcillaud 
**/
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_relance_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// pas d'relance ? on en cree une nouvelle, mais seulement si 'oui' en argument.
	if (!$id_relance = intval($arg)) {
		if (!in_array($arg, array('oui', 'new'))) {
			include_spip('inc/headers');
			redirige_url_ecrire();
		}
		$id_relance = insert_relance();
	}

	if ($id_relance) $err = revisions_relances($id_relance);
	return array($id_relance,$err);
}


function insert_relance() {
	
	$champs = array();
	
	// Envoyer aux plugins
	$champs = pipeline('pre_insertion', array(
		'args' => array(
			'table' => 'spip_relances',
		),
		'data' => $champs
	));
	
	$id_relance = sql_insertq("spip_relances", $champs);

	return $id_relance;
}


// Enregistrer certaines modifications d'une relance
function revisions_relances($id_relance, $c=false) {

	// recuperer les champs dans POST s'ils ne sont pas transmis
	if ($c === false) {
		$c = array();
		foreach (array('titre','texte','duree', 'periode','quand') as $champ
		) {
			if (($a = _request($champ)) !== null) {
				$c[$champ] = $a;
			}
		}
	}

	include_spip('inc/modifier');
	modifier_contenu('relance', $id_relance, array(
			'invalideur' => "id='id_relance/$id_relance'"
		),
		$c);
}
?>
