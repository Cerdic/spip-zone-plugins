<?php
	/**
	 * GuestBook
	 *
	 * Copyright (c) 2008 - 2009
	 * Yohann Prigent (potter64)
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/
function formulaires_guestbook_charger_dist() {
	$valeurs = array(
		'email' => $email,
		'pseudo' => $pseudo,
		'nom' => $nom,
		'prenom' => $prenom,
		'ville' => $ville,
		'note' => $note,
		'message' => $message
	);
	if (_request('note'))
		$valeurs['note'] = _request('note');
	return $valeurs;
}
function formulaires_guestbook_verifier_dist(){
	$erreurs = array();
	// verifier que les champs obligatoires sont bien la :
	foreach(array('email','pseudo','ville','message','note') as $obligatoire)
	if (!_request($obligatoire)) { 
		$erreurs[$obligatoire.'-erreur'] = 'obligatoire';
	}
	// verifier que si un email a été saisi, il est bien valide :
	include_spip('inc/filtres');
	if (_request('email') AND !email_valide(_request('email'))) {
		$erreurs['email'] = _T('spip:form_email_non_valide');
		$erreurs['email-erreur'] = 'erreur';
	}	
	if (preg_match(',\d,', _request('prenom'))) {
		$erreurs['prenom'] = _T('guestbook:prenom_chiffres');
		$erreurs['prenom-erreur'] = 'erreur';
	}
	if (preg_match(',\d,', _request('nom'))) {
		$erreurs['nom'] = _T('guestbook:nom_chiffres');
		$erreurs['nom-erreur'] = 'erreur';
	}
	if (count($erreurs))
		$erreurs['message_erreur'] = _T('guestbook:champs_obligatoires');
	include_spip('inc/texte');
	// si nospam est present on traite les spams
	if (include_spip('inc/nospam')) {
		$caracteres = compter_caracteres_utiles(_request('message'));
		// moins de 10 caracteres sans les liens = spam !
		if ($caracteres < 10){
			$erreurs['message'] = _T('forum_attention_dix_caracteres');
			$erreurs['message-erreur'] = 'erreur';
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
		$infos_texte = analyser_spams(_request('message'));
		// si le texte contient plus de trois lien = spam !
		if ($infos_texte['nombre_liens'] >= 3) {
			$erreurs['message'] = _T('nospam:erreur_spam');
			$erreurs['message-erreur'] = 'erreur';
		}
	}
	return $erreurs;
}
function formulaires_guestbook_traiter_dist() {
	include_spip('base/abstract_sql');
	$ip = $GLOBALS['ip'];
	$email	= _request('email');
	$pseudo	= _request('pseudo');
	$nom	= _request('nom');
	$prenom	= _request('prenom');
	$ville	= _request('ville');
	$note	= _request('note');
	$message	= _request('message');
	$post_stat = 'prop';
	$date = date('Y-m-d H:i:s');
	sql_insertq("spip_guestbook", array(
		'id_message' => "",
		'message' => $message,
		'email' => $email,
		'nom' => $nom,
		'prenom' => $prenom,
		'pseudo' => $pseudo,
		'ville' => $ville,
		'statut' => $post_stat,
		'ip' => $ip,
		'note' => $note,
		'date' => $date
	));
	$message = _T('guestbook:message_poste');
	return $message;
}
?>