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

function action_editer_spipimmo_annonce_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// pas de spipimmo_annonce ? on en cree un nouveau, mais seulement si 'oui' en argument.
	if (!$id_annonce = intval($arg)) {
		if ($arg != 'oui') {
			include_spip('inc/headers');
			redirige_url_ecrire();
		}
		$id_annonce = insert_spipimmo_annonce();
	}
	if ($id_annonce) $err = revision_spipimmo_annonce($id_annonce);
	return array($id_annonce,$err);
}


function insert_spipimmo_annonce() {
	$champs = array();

	// Envoyer aux plugins
	$champs = pipeline('pre_insertion', array(
		'args' => array(
			'table' => 'spip_spipimmo_proprietaires',
		),
		'data' => $champs
	));

	$id_annonce = sql_insertq("spip_spipimmo_proprietaires", $champs);
	return $id_annonce;
}


// Enregistrer certaines modifications d'un spipimmo_annonce
function revision_spipimmo_annonce($id_annonce, $c=false) {

	// recuperer les champs dans POST s'ils ne sont pas transmis
    if ($c === false) {
		$c = array();
			foreach (array('vente_location', 'id_proprio', 'id_negociateur', 'honoraires', 'adresse', 'code_postal', 'ville', 'dpe_energie', 'dpe_gaz', 'date_dpe', 'depot_garantie', 'date_annonce', 'date_modification', 'date_dispo', 'annonce') as $champ) {
				if (($a = _request($champ)) !== null) {
					$c[$champ] = $a;
				}
			}
	}

	include_spip('inc/modifier');
	modifier_contenu('spipimmo_proprietaires', $id_annonce, array(
		'invalideur' => "id='id_annonce/$id_annonce'"
	),
	$c);
}
?>
