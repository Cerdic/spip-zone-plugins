<?php

if (!defined('_ECRIRE_INC_VERSION')) return;


function formulaires_ajouter_action_charger_dist()
{
	include_spip('inc/session');
	$id_auteur = session_get('id_auteur');

	return array(
		'id_auteur'       => $id_auteur,
		'date_debut'      => $date_debut,
		'date_fin'        => $date_fin,
		'heure_debut'     => date('H:i', strtotime('now - 10 minute')),
		'heure_fin'       => date('H:i'),
		'type_activite'   => $type_activite,
		'id_organisation' => $id_organisation,
		'id_projet'       => $id_projet,
		'detail_activite' => $detail_activite,
		'editable'        => 'oui'
	);
}

function formulaires_ajouter_action_verifier_dist()
{
	return array(
		'message_erreur' => 'Erreur temporaire !'
	);
}

function formulaires_ajouter_action_traiter_dist()
{
	return array(
		'editable'   => 'oui',
		'message_ok' => 'Traitement effectué'
	);
}


?>
