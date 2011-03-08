<?php
// traitement CVT du formulaire lettre
// doc. http://www.spip.net/fr_article3796.html

// TODO possible
// avoir une option de confirmation par email

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/actions');
include_spip('inc/editer');

// Charger
function formulaires_mesabonnes_charger_dist($type_abonnement = "subscribe")
{
	$valeurs = array(
		"mesabos_nom" => '',
		"type_abonnement" => $type_abonnement
	);
	return $valeurs;
}

// Vérifier
function formulaires_mesabonnes_verifier_dist()
{
	$erreurs = array();
	// verifier que si un email a été saisi, il est bien valide :
	include_spip('inc/filtres');
	if (!_request('mesabos_email'))
		$erreurs['mesabos_email'] = _T('form_prop_indiquer_email');
	else if (!email_valide(_request('mesabos_email')))
		$erreurs['mesabos_email'] = _T('form_prop_indiquer_email');

	if (_request('mesabos_nom'))
		set_request("mesabos_nom", _request('mesabos_nom'));

	// opt-in
	if (!_request('mesabos_subscribe'))
		$erreurs['mesabos_subscribe'] = _T('info_obligatoire_02');
	else
		set_request("type_abonnement", _request('mesabos_subscribe'));

	if (count($erreurs))
		$erreurs['message_erreur'] = '';
	return $erreurs;
}


// Traiter
function formulaires_mesabonnes_traiter_dist()
{
	$res = array();
	$message = "action inconnue";

	$nom = strip_tags(strtolower(trim(_request('mesabos_nom'))));
	$email = strip_tags(strtolower(trim(_request('mesabos_email'))));
	$statut = _request('mesabos_subscribe');
	$liste = ""; // pas utilise pour l'instant
	$lang = strip_tags(strtolower(trim(_request('mesabos_lang'))));
	$date_motif = date("Y-m-d H:i:s");


	// abonnement
	if ($statut=="subscribe"){
		$statut = "publie";
		sql_delete('spip_mesabonnes', "email = '$email'"); // pour eviter doublons , FIXME: tester existant et faire un update ....
		$id_abonne = sql_insertq('spip_mesabonnes', array(
		                                                 'nom' => "$nom",
		                                                 'email' => "$email",
		                                                 'lang' => "$lang",
		                                                 'date_modif' => "$date_motif",
		                                                 'liste' => "$liste",
		                                                 'statut' => "$statut"));
		if ($id_abonne){
			$message = _T('mesabonnes:merci', array('email' => "$email"));
			$res = array('message_ok' => $message, 'id_abonne' => $id_abonne);
		}
		else {
			$message = _T('mesabonnes:erreur');
			$res = array('message_erreur' => $message);
		}
	}

	// desabonnement
	if ($statut=="unsubscribe"){
		//on recupere l'id par l email
		$id_abonne = sql_getfetsel('id_abonne', 'spip_mesabonnes', 'email='.sql_quote($email));
		if ($id_abonne){
			//TODO message en cas d echec de lupdate
			sql_updateq('spip_mesabonnes',
			            array('statut' => "poubelle"),
			            'id_abonne='.intval($id_abonne));
			$message = _T('mesabonnes:bye', array('email' => "$email"));
			$res = array('message_ok' => $message, 'id_abonne' => $id_abonne);
		}
		else {
			$message = _T('mesabonnes:erreur_pas_trouve', array('email' => "$email"));
			$res = array('message_erreur' => $message);
		}
	}
	
	return $res;
}

?>