<?php
/*
 * Plugin messagerie
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */

/**
 * Chargement des valeurs par defaut de #FORMULAIRE_ECRIRE_MESSAGE{url_redirection_apres_envoi}
 * la fonction recoit en entree les arguments de la balise dans le squelette
 * renvoyer la liste des champs en cle, et les valeurs par defaut a la saisie
 * les valeurs seront automatiquement surchargees par _request() en cas de second tour de saisie
 * renvoyer false pour ne pas autoriser la saisie
 * dans id renvoyer la cle primaire de l'objet traite si necessaire (sera mise a new sinon)
 *
 * le champ destinataire n'est active que si autoriser('destiner','message') l'autorise pour l'auteur connecte
 *  
 * @return unknown
 */
function formulaires_ecrire_message_charger_dist($redirect="",$destinataire=""){
	include_spip('base/abstract_sql');
	include_spip('inc/filtres');
	$valeurs = array('objet'=>'','texte_message'=>'');
	include_spip('inc/autoriser');
	if (autoriser('destiner','message',0)){
		$valeurs['destinataire'] = '';
		$valeurs['destinataires'] = '';
	}
	if ($repondre = _request('repondre')){

		$row = sql_fetsel('id_auteur','spip_auteurs_liens',array('id_objet='.intval($repondre), 'objet="message"'));
		if (isset($row["id_auteur"]) && $row["id_auteur"] == $GLOBALS['visiteur_session']['id_auteur']){
		    $row = sql_fetsel('id_auteur,titre,texte,date_heure','spip_messages','id_message='.intval($repondre));
		    if (isset($valeurs['destinataires']))
			    $valeurs['destinataires'] = array($row['id_auteur']);
		    $valeurs['objet'] = "Re : ".textebrut($row['titre']);
		    $valeurs['texte_message'] = "\n\n\n<quote>\n"
		    . sql_getfetsel('nom','spip_auteurs','id_auteur='.intval($row['id_auteur']))
		    . " - " . affdate($row['date_heure']) . "\n\n "
		    . $row['texte'] . "</quote>\n";

		} else {
		    // tentative de "hack"
		}
	}
	if (is_numeric($destinataire)){
		$dest = sql_getfetsel('id_auteur','spip_auteurs','id_auteur='.intval($destinataire));
		if (isset($valeurs['destinataires']) && $dest)
			$valeurs['destinataires'] = array($dest);
	}

	return $valeurs;
}

/**
 * Verification de la saisie de #FORMULAIRE_ECRIRE_MESSAGE
 *
 * @return array
 */
function formulaires_ecrire_message_verifier_dist($redirect=""){
	include_spip('inc/messagerie');
	return messagerie_verifier(array('objet','texte_message'));
}


/**
 * Traitement de la saisie de #FORMULAIRE_ECRIRE_MESSAGE
 *
 * @return string
 */
function formulaires_ecrire_message_traiter_dist($redirect=""){
	include_spip('inc/texte');
	include_spip('inc/messagerie');

	$objet = typo(_request('objet'));
	$texte = _request('texte_message');
	$out = _T("ecrire_message:message_envoye_erreur");
	$ok = false;

	$exp = $GLOBALS['visiteur_session']['id_auteur'];
	$dests = _request('destinataires');

	list($auteurs_dests,$emails_dests) = messagerie_destiner($dests);

	$id_message = 0;
	$general = false;
	include_spip('inc/autoriser');
	if (is_array($dests) AND in_array(_EMAIL_GENERAL,$dests)
	  AND autoriser('destiner_general','message',0))
		$general = true;

	if ($id_message = messagerie_messager($objet, $texte, $auteurs_dests,$emails_dests,$general)){

		// et invalidons les pages en cache faisant reference au message
		include_spip('inc/invalideur');
		suivre_invalideur("envoyermessage/$id_message");
		$ok = true;
	}

	if ($ok){
		$out = _T("ecrire_message:message_envoye");
		if (!$redirect AND defined('_REDIRECT_POST_ENVOI_MESSAGE'))
			$redirect = _REDIRECT_POST_ENVOI_MESSAGE;
		$redirect = calculer_url($redirect);
		return array('message_ok'=>$out,'id_message'=>$id_message,'redirect'=>$redirect);
	}
	else
		return array('message_erreur'=>$out);
}

?>