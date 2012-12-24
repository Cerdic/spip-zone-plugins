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

function action_editer_spipimmo_negociateur_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// pas de spipimmo_negociateur ? on en cree un nouveau, mais seulement si 'oui' en argument.
	if (!$id_negociateur = intval($arg)) {
		if ($arg != 'oui') {
			include_spip('inc/headers');
			redirige_url_ecrire();
		}
		$id_negociateur = insert_spipimmo_negociateur();
	}
	if ($id_negociateur) $err = revision_spipimmo_negociateur($id_negociateur);
	return array($id_negociateur,$err);
}


function insert_spipimmo_negociateur() {
	$champs = array();

	// Envoyer aux plugins
	$champs = pipeline('pre_insertion', array(
		'args' => array(
			'table' => 'spip_spipimmo_negociateurs',
		),
		'data' => $champs
	));

	$id_negociateur = sql_insertq("spip_spipimmo_negociateurs", $champs);
	return $id_negociateur;
}


// Enregistrer certaines modifications d'un spipimmo_negociateur
function revision_spipimmo_negociateur($id_negociateur, $c=false) {

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
	modifier_contenu('spipimmo_negociateurs', $id_negociateur, array(
		'invalideur' => "id='id_negociateur/$id_negociateur'"
	),
	$c);
}
?>
