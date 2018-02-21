<?php
/*
 * Plugin Notifications
 * (c) 2009-2012 SPIP
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// https://code.spip.net/@action_instituer_forum_dist
function action_instituer_forum_paremail_dist() {

	$force = true;
	// si on a active la protection antibot la moderation se fera en differe
	include_spip('inc/config');
	if (lire_config('notifications/moderation_email_protection_antibot','') == 'on') {
		$force = false;
	}
	// verification manuelle de la signature : cas particulier de cette action signee par email
	$arg = _request('arg');
	$hash = _request('hash');

	include_spip("inc/securiser_action");
	$action = 'instituer_forum_paremail';
	$pass = secret_du_site();

	$verif = _action_auteur("$action-$arg", '', $pass, 'alea_ephemere');

	$ae = explode("-",$arg);
	$id_forum = array_shift($ae);
	$statut = array_shift($ae);
	$statut_init = array_shift($ae);
	// l'email est ce qui reste
	$email = implode("-",$ae);
	$message = null;
	$erreur_auteur = _T('notifications:info_moderation_interdite');

	include_spip("inc/filtres");
	$lien_moderation = lien_ou_expose(url_absolue(generer_url_entite($id_forum,'forum',"","forum$id_forum",false)),_T('notifications:info_moderation_lien_titre'));
	$erreur = _T('notifications:info_moderation_url_perimee')."<br />$lien_moderation";

	if ($hash==_action_auteur("$action-$arg", '', $pass, 'alea_ephemere')
	  OR $hash==_action_auteur("$action-$arg", '', $pass, 'alea_ephemere_ancien')){
		$erreur = "";
	}

	// que le hash soit invalide ou pas, on regarde si jamais on est loge avec cet email
	// auquel cas on peut utiliser les liens, meme perimes (confort)
	// et la moderation est immediate, sans attente de validation antibot
	if (isset($GLOBALS['visiteur_session'])
		AND isset($GLOBALS['visiteur_session']['id_auteur'])
		AND $GLOBALS['visiteur_session']['id_auteur']
		AND isset($GLOBALS['visiteur_session']['email'])
		AND $GLOBALS['visiteur_session']['email']==$email){
		$message = sql_fetsel("id_objet,objet,statut","spip_forum","id_forum=".intval($id_forum));
		if (autoriser("modererforum",$message['objet'],$message['id_objet'])){
			$erreur_auteur = "";
			$erreur = "";
			$force = true;
		}
	}
	if ($erreur) {
		spip_log("Signature incorrecte pour $arg","moderationparemail"._LOG_INFO_IMPORTANTE);
	}

	// si hash est ok, verifier si l'email correspond a un auteur qui a le droit de faire cette action
	if (!$erreur){

		if (!$message) {
			$message = sql_fetsel("id_objet,objet,statut","spip_forum","id_forum=".intval($id_forum));
		}

		// on recherche le message en verifiant qu'il a bien le statut
		if ($message){
			if ($message['statut']!=$statut_init){
				$erreur = _T("notifications:info_moderation_deja_faite",array('id_forum'=>$id_forum,'statut'=>$message['statut']))
					."<br />$lien_moderation";
			}
			else {
				// trouver le(s) auteur(s) et verifier leur autorisation si besoin
				if ($erreur_auteur){
					$res = sql_select("*","spip_auteurs","email=".sql_quote($email,'','text'));
					while ($auteur = sql_fetch($res)){
						if (autoriser("modererforum",$message['objet'],$message['id_objet'],$auteur)){
							$erreur_auteur = "";
							// on ajoute l'exception car on est pas identifie avec cet id_auteur
							autoriser_exception("modererforum",$message['objet'],$message['id_objet']);
							break;
						}
					}
				}
				if ($erreur_auteur){
					$erreur = $erreur_auteur 
					  . "<br /><small>"
					  . _L("(aucun auteur avec l'email $email n'a de droit suffisant)")
					  . "</small>";
					spip_log("Aucun auteur pour $email autorise a moderer $id_forum","moderationparemail"._LOG_INFO_IMPORTANTE);
				}
			}
		}
		else {
			spip_log("Message forum $id_forum introuvable","moderationparemail"._LOG_INFO_IMPORTANTE);
			$erreur = "Message forum $id_forum introuvable"; // improbable ?
		}
	}

	if (!$erreur){
		$erreur = notifications_moderation_execute($id_forum, $statut, $email, $force);
	}

	// Dans tous les cas on finit sur un minipres qui dit si ok ou echec
	$titre = (!$erreur ? _T("notifications:info_moderation_confirmee_$statut",array('id_forum'=>$id_forum)) : $erreur);
	include_spip('inc/minipres');
	echo minipres($titre,"","",true);

}

/**
 * Enregistre la demande de moderation dans le message, et verifie si il y en a pas deja une autre en attente
 * si c'est le cas renvoie une erreur pour affichage
 * @param int $id_forum
 * @param string $statut
 * @return string
 *   erreur eventuelle
 */
function notifications_moderation_verifie_et_enregistre($id_forum, $statut, $email) {

	$erreur = "";
	$message = sql_fetsel('*', 'spip_forum', 'id_forum='.intval($id_forum));

	// securite : il faut avoir l'autorisation de moderer le message pour faire quelque chose ici
	if (!autoriser("modererforum", $message['objet'], $message['id_objet'])) {
		return _T('notifications:info_moderation_interdite');
	}

	// si c'est la premiere fois et que le champs n'existe pas, on le cree a la volee
	// on ne fait volontairement pas d'upgrade via administrations pour ne pas compliquer la base si cette fonction ne sert jamais
	if (!isset($message['moderation_a_valider'])) {
		sql_alter("TABLE spip_forum ADD moderation_a_valider text DEFAULT '' NOT NULL");
		$message['moderation_a_valider'] = '';
	}

	$moderations = json_decode($message['moderation_a_valider'], true);
	if (!is_array($moderations)) {
		$moderations = array();
	}
	$append = true;
	if (count($moderations)) {
		foreach ($moderations as $moderation) {
			if ($moderation['statut'] == $statut) {
				// on ne fait rien, c'est la premiere demande qui est conservee
				// $moderation['time'] = $_SERVER['REQUEST_TIME'];
				// $moderation['email'] = $email;
				$append = false;
			}
			else {
				include_spip("inc/filtres");
				$lien_moderation = lien_ou_expose(url_absolue(generer_url_entite($id_forum,'forum',"","forum$id_forum",false)),_T('notifications:info_moderation_lien_titre'));
				// il y a au moins une autre moderation a valider sur un statut different => erreur
				$erreur = _T("notifications:info_moderation_deja_faite",array('id_forum'=>$id_forum,'statut'=>$moderation['statut']))
									."<br />$lien_moderation";
			}
		}
	}
	if ($append) {
		spip_log("Moderation differee a valider message $id_forum $statut par $email","moderationparemail"._LOG_INFO_IMPORTANTE);
		$moderations[] = array('statut' => $statut, 'time' => $_SERVER['REQUEST_TIME'], 'email' => $email);
		sql_updateq('spip_forum', array('moderation_a_valider'=>json_encode($moderations)), 'id_forum='.intval($id_forum));
	}

	return $erreur;
}

/**
 * @param $id_forum
 * @param $statut
 * @param $email
 * @param bool $force
 * @return string
 */
function notifications_moderation_execute($id_forum, $statut, $email, $force = false) {
	// si le flag force est true on peut instituer immediatement
	if ($force) {
		if ($message = sql_fetsel('*', 'spip_forum', 'id_forum='.intval($id_forum))) {
			spip_log("Moderation immediate message $id_forum $statut par $email","moderationparemail"._LOG_INFO_IMPORTANTE);
			$arg = "$id_forum-$statut";
			$instituer_forum = charger_fonction("instituer_forum","action");
			$instituer_forum($arg);
		}
		return '';
	}
	// sinon on enregistre la demande dans le message et on lance une tache async chargee de l'executer dans quelques secondes
	// qui verifiera qu'on a pas plusieurs demandes contradictoires en cours (cas du bot qui clic sur les 3 liens de moderations du meme message)
	else {
		$erreur = notifications_moderation_verifie_et_enregistre($id_forum, $statut, $email);
		if (!$erreur) {
			// dans 10secondes on valide cette moderation si on en a pas recue d'autre entre temps
			job_queue_add('notifications_moderation_valide',"Valider la moderation forum $id_forum statut=$statut", array($id_forum), 'action/instituer_forum_paremail', false, $_SERVER['REQUEST_TIME'] + 10);
		}
		return $erreur;
	}
}

function notifications_moderation_valide($id_forum) {
	if ($message = sql_fetsel('*', 'spip_forum', 'id_forum='.intval($id_forum))
	  and $message['moderation_a_valider']
	  and $moderations = json_decode($message['moderation_a_valider'], true)){

		spip_log("Valider la moderation $id_forum : ".$message['moderation_a_valider'],"moderationparemail"._LOG_INFO_IMPORTANTE);

		// si une seule demande de moderation, ce n'est pas un bot qui a clique plusieurs fois, on peut executer direct
		if (count($moderations) == 1) {
			$moderation = reset($moderations);
			// on ajoute l'exception car on est pas identifie avec cet id_auteur
			autoriser_exception("modererforum",$message['objet'],$message['id_objet']);
			notifications_moderation_execute($id_forum, $moderation['statut'], $moderation['email'], true);
			// et on vide la liste
			$moderations = '';
		}
		// sinon, il y a eu plusieurs demandes, on les refuse
		else {
			spip_log("Refus moderation $id_forum : ".$message['moderation_a_valider'],"moderationparemail"._LOG_INFO_IMPORTANTE);
			$last = 0;
			foreach($moderations as $moderation) {
				$last = max($last, $moderation['time']);
			}
			// on vide la liste si la plus recente date de au moins 1min, par securite
			if ($last < $_SERVER['REQUEST_TIME'] - 60) {
				$moderations = '';
			}
			// sinon on se relance dans 1min pour faire cette vidange
			else {
				job_queue_add('notifications_moderation_valide',"Purger les moderations forum $id_forum", array($id_forum), 'action/instituer_forum_paremail', false, $_SERVER['REQUEST_TIME'] + 60);
			}
		}

		if (!$moderations) {
			spip_log("RAZ moderation $id_forum","moderationparemail"._LOG_INFO_IMPORTANTE);
			sql_updateq('spip_forum', array('moderation_a_valider' => ''), 'id_forum='.intval($id_forum));
		}
	}
}