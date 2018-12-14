<?php
/**
 * Plugin Newsletters
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_newsletter_send_charger_dist($id_newsletter,$mode_test=false){

	$valeurs = array(
		'email_test' => $GLOBALS['visiteur_session']['email'],
		'liste' => '',
		'planifie' => '',
		'resend' => 'non',
		'date_start_jour' => '',
		'date_start_heure' => '',
		'_mode_test' => $mode_test?$mode_test:'',
		'_id_newsletter' => $id_newsletter,
		'editable' => ' ',
	);
	include_spip('inc/autoriser');
	if (!autoriser('envoyer', 'newsletter', $id_newsletter, null, array('test'=>$mode_test))) {
		$valeurs['editable'] = '';
	}

	if (!$mode_test){
		$lists = charger_fonction('lists','newsletter');
		$valeurs['_listes_dispo'] = $lists();
	}

	return $valeurs;
}

function mailshot_list_subscribers($list){
	$subscribers = charger_fonction('subscribers','newsletter');
	return $subscribers(array($list),array('count'=>true));
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_newsletter_send_verifier_dist($id_newsletter,$mode_test=false){
	$erreurs = array();

	if (_request('envoi_test')){
		if (!_request('email_test'))
			$erreurs['email_test'] = _T('info_obligatoire');
	}
	elseif (_request('envoi')){
		if (_request('planifie')){
			if (!_request('date_start_jour') or !_request('date_start_heure')){
				$erreurs['date_start'] = _T('info_obligatoire');
			}
			else {
				include_spip('formulaires/dater');
				if ($v = _request("date_start_jour") and !dater_recuperer_date_saisie($v)) {
					$erreurs['date_start'] = _T('format_date_incorrecte');
				} elseif ($v = _request("date_start_heure") and !dater_recuperer_heure_saisie($v)) {
					$erreurs['date_start'] = _T('format_heure_incorrecte');
				}
			}
		}
		// evite le derapage
		if (!_request('liste'))
			$erreurs['liste'] = _T('info_obligatoire');
	}

	return $erreurs;
}

/**
 * Traiter les champs postes
 */
function formulaires_newsletter_send_traiter_dist($id_newsletter,$mode_test=false){

	$res = array('message_erreur'=>"lapin compris");

	if (_request('envoi_test')){
		$email = _request('email_test');
		// recuperer l'abonne si il existe avec cet email
		$subscriber = charger_fonction('subscriber','newsletter');
		$dest = $subscriber($email);

		// si abonne inconnu, on simule (pour les tests)
		if (!$dest){
			$dest = array(
				'email' => $email,
				'nom' => $GLOBALS['visiteur_session']['nom'],
				'lang' => $GLOBALS['visiteur_session']['lang'],
				'status' => 'on',
				'url_unsubscribe' => url_absolue(_DIR_RACINE . "unsubscribe"),
			);
		}
		elseif($dest['email'] !== $email) {
			$dest['email'] = $email;
		}

		// ok, maintenant on prepare un envoi
		$send = charger_fonction("send","newsletter");
		$res = $send($dest, $id_newsletter, array('test'=>$mode_test?true:false));

		if (!$res)
			$res = array('message_ok'=>_T($mode_test?'newsletter:info_test_envoye':'newsletter:info_envoi_unique_reussi',array('email'=>$email)));
		else
			$res = array('message_erreur'=>$res);
	}
	elseif (_request('envoi')){

		$listes = array();
		if ($liste = _request('liste')){
			$listes = array($liste);
		}

		$options = array();
		if (_request('planifie')){
			$d = dater_recuperer_date_saisie(_request('date_start_jour'));
			$h = dater_recuperer_heure_saisie(_request('date_start_heure'));
			$options['date_start'] = sql_format_date($d[0], $d[1], $d[2], $h[0], $h[1]);
		}
		if (_request('resend')!=='oui'){
			$options['graceful'] = true;
		}

		$bulkstart = charger_fonction("bulkstart","newsletter");
		if ($id_mailshot = $bulkstart($id_newsletter, $listes, $options)){
			$total = sql_getfetsel('total','spip_mailshots','id_mailshot='.intval($id_mailshot));
			$res = array('message_ok'=>singulier_ou_pluriel($total,'mailshot:info_envoi_programme_1_destinataire','mailshot:info_envoi_programme_nb_destinataires'));
			set_request('liste','');
		}
		else
			$res = array('message_erreur'=>'erreur');
	}

	return $res;
}

