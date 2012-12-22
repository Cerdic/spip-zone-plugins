<?php
/**
* Plugin SPIP-Immo
*
* @author: CALV V3
* @author: Pierre KUHN V4
*
* Copyright (c) 2007-12
* Logiciel distribue sous licence GPL.
*
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_spipimmo_proprio_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// pas de spipimmo_proprio ? on en cree un nouveau, mais seulement si 'oui' en argument.
	if (!$id_proprio = intval($arg)) {
		if ($arg != 'oui') {
			include_spip('inc/headers');
			redirige_url_ecrire();
		}
		$id_proprio = insert_spipimmo_proprio();
	}
	if ($id_proprio) $err = revision_spipimmo_proprio($id_proprio);
	return array($id_proprio,$err);
}


function insert_spipimmo_proprio() {
	$champs = array();

	// Envoyer aux plugins
	$champs = pipeline('pre_insertion', array(
		'args' => array(
			'table' => 'spip_spipimmo_proprietaires',
		),
		'data' => $champs
	));

	$id_proprio = sql_insertq("spip_spipimmo_proprietaires", $champs);
	return $id_proprio;
}


// Enregistrer certaines modifications d'un spipimmo_proprio
function revision_spipimmo_proprio($id_proprio, $c=false) {

	// recuperer les champs dans POST s'ils ne sont pas transmis
    if ($c === false) {
		$c = array();
			foreach (array('civilite', 'nom', 'prenom', 'adresse_1', 'code_postal', 'ville', 'tel_fixe') as $champ) {
				if (($a = _request($champ)) !== null) {
					$c[$champ] = $a;
				}
			}
	}

	include_spip('inc/modifier');
	modifier_contenu('spipimmo_proprietaires', $id_proprio, array(
		'invalideur' => "id='id_proprio/$id_proprio'"
	),
	$c);
}
?>
