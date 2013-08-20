<?php

if (!defined('_ECRIRE_INC_VERSION')) return;


function formulaires_ajouter_action_charger_dist()
{
	include_spip('inc/session');
	$id_auteur = session_get('id_auteur');

	$profil = _request('profil');
	if (!$profil) $profil = 'operateur';

	return array(
		'id_auteur'           => $id_auteur,
		'date_debut'          => date('Y-m-d H:i:s'),
		'date_fin'            => date('Y-m-d H:i:s'),
		'heure_debut'         => date('H:i', strtotime('now - 10 minute')),
		'heure_fin'           => date('H:i'),
		'nb_heures_passees'   => '',
		'nb_heures_facturees' => '',
		'type_activite'       => $type_activite,
		'detail_activite'     => $detail_activite,
		'id_organisation'     => $id_organisation,
		'id_projet'           => $id_projet,
		'profil'              => $profil,
		'editable'            => 'oui'
	);
}

function formulaires_ajouter_action_verifier_dist()
{

	if ($profil = _request('changer_profil')) {
		set_request('profil', $profil);
		return array('message_ok' => _T('dayfill:info_profil_change'));
	}

	if (!_request('enregistrer_activite')) return array('message_ok' => 'Le formulaire a été actualisé offrant des champs supplémentaires à saisir.');

	$erreurs = array();
	// verifier que les champs obligatoires sont bien la :
	foreach (array(
		         'id_auteur',
		         'nb_heures_passees',
		         'nb_heures_facturees',
		         'date_debut',
		         'date_fin',
		         'detail_activite',
		         'id_organisation',
		         'id_projet'
	         ) as $obligatoire) {
		if (!_request($obligatoire)) {
			$erreurs[$obligatoire] = _T('info_obligatoire');
		}
	}

	if (count($erreurs))
		$erreurs['message_erreur'] = _T('dayfill:erreur_saisie_invalide');

	return $erreurs;
}


function formulaires_ajouter_action_traiter_dist()
{
	return array(
		'editable'   => 'oui',
		'message_ok' => 'Traitement effectué'
	);
}


?>
