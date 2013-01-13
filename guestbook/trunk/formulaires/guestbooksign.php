<?php
/**
 * Plugin Guestbook
 * (c) 2008-2013 Yohann Prigent (potter64), Stéphane Santon
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_guestbooksign_charger_dist($id_guestmessage='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('guestmessage','new', '',0,'','',array(),'');
/*	if (_request('note'))
		$valeurs['note'] = _request('note');
*/
	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_guestbooksign_verifier_dist($id_guestmessage='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$erreurs = formulaires_editer_objet_verifier('guestmessage',$id_guestmessage);

 	include_spip('inc/texte');
	// si nospam est present on traite les spams
	if (include_spip('inc/nospam')) {
		$caracteres = compter_caracteres_utiles(_request('guestmessage'));
		// moins de 10 caracteres sans les liens = spam !
		if ($caracteres < 10){
			$erreurs['guestmessage'] = _T('guestmessage:formulaire_attention_dix_caracteres');
			$erreurs['guestmessage-erreur'] = 'erreur';
		}
		// on analyse le pseudo, le prenom, le nom
		$verifions = array('pseudo', 'prenom', 'nom', 'ville');
		foreach ($verifions as $verification) {
			if (_request($verification) != '') {
				$infos_verif = analyser_spams(_request($verification));
				// si un lien dans le champ = spam !
				if ($infos_verif['nombre_liens'] > 0) {
					$erreurs[$verification] = _T('nospam:erreur_spam');
					$erreurs[$verification.'-erreur'] = 'erreur';
				}
			}
		}
		// on analyse le texte
		$infos_texte = analyser_spams(_request('guestmessage'));
		// si le texte contient plus de trois lien = spam !
		if ($infos_texte['nombre_liens'] >= 3) {
			$erreurs['guestmessage'] = _T('nospam:erreur_spam');
			$erreurs['guestmessage-erreur'] = 'erreur';
		}
	}

	return $erreurs;
}

/**
 * Traiter les champs postes
 */
function formulaires_guestbooksign_traiter_dist($id_guestmessage='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$message = array();
	include_spip('base/abstract_sql');
	$ip = $GLOBALS['ip'];
	if (sql_insertq("spip_guestmessages", array(
		'guestmessage' => _request('guestmessage'),
		'email' => _request('email'),
		'nom' => _request('nom'),
		'prenom' => _request('prenom'),
		'pseudo' => _request('pseudo'),
		'ville' => _request('ville'),
		'statut' => 'prop',
		'ip' => $ip,
		'note' => _request('note'),
		'date' => date('Y-m-d H:i:s'),
	)))
		$message['message_ok'] = _T('guestmessage:texte_message_poste_ok');
	else $message['message_ok'] = _T('guestmessage:texte_erreur_traiter_post');
	return $message;
}


?>