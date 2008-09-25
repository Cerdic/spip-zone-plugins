<?php
function forum_insert_base($c, $id_forum, $id_article, $id_breve, $id_syndic, $id_rubrique, $statut, $retour)
{
	$afficher_texte = (_request('afficher_texte') <> 'non');
	$ajouter_mot = _request('ajouter_mot');

	// Antispam : si 'nobot' a ete renseigne, ca ne peut etre qu'un bot
	if (strlen(_request('nobot'))) {
		tracer_erreur_forum('champ interdit (nobot) rempli');
		return false;
	}

	//  Si forum avec previsu sans bon hash de securite, echec silencieux
	if ($afficher_texte AND forum_insert_noprevisu()) {
		return false;
	}

	if (array_reduce($_POST, 'reduce_strlen', (20 * 1024)) < 0) {
		ask_php_auth(_T('forum_message_trop_long'),
			_T('forum_cliquer_retour',
				array('retour_forum' => $retour)));
	}

	// Entrer le message dans la base
	$id_message = sql_insertq('spip_forum', array('date_heure'=> 'NOW()'));

	if ($id_forum>0) {
		$id_thread = sql_getfetsel("id_thread", "spip_forum", "id_forum = $id_forum");
	}
	else
		$id_thread = $id_message; # id_thread oblige INSERT puis UPDATE.

	// id_rubrique est parfois passee pour les articles, on n'en veut pas
	if ($id_rubrique > 0 AND ($id_article OR $id_breve OR $id_syndic))
		$id_rubrique = 0;

	// Entrer les cles de jointures et assimilees
global $visiteur_session;
	if ($visiteur_session) {
	$moderation_plug_admin=$GLOBALS['meta']["moderation_plug_admin"];
	$moderation_plug_redac=$GLOBALS['meta']["moderation_plug_redac"];
	$moderation_plug_visit=$GLOBALS['meta']["moderation_plug_visit"];
	$autstat = $visiteur_session['statut'];
	if ($autstat == '0minirezo' AND $moderation_plug_admin == 'oui') {
		sql_updateq('spip_forum', array('id_parent' => $id_forum, 'id_rubrique' => $id_rubrique, 'id_article' => $id_article, 'id_breve' => $id_breve, 'id_syndic' => $id_syndic, 'id_thread' => $id_thread, 'statut' => 'publie'), "id_forum = $id_message");
		}
	else if ($autstat == '1comite' AND $moderation_plug_redac == 'oui') {
		sql_updateq('spip_forum', array('id_parent' => $id_forum, 'id_rubrique' => $id_rubrique, 'id_article' => $id_article, 'id_breve' => $id_breve, 'id_syndic' => $id_syndic, 'id_thread' => $id_thread, 'statut' => 'publie'), "id_forum = $id_message");
	}
	else if ($autstat == '6forum' AND $moderation_plug_visit == 'oui')  {
		sql_updateq('spip_forum', array('id_parent' => $id_forum, 'id_rubrique' => $id_rubrique, 'id_article' => $id_article, 'id_breve' => $id_breve, 'id_syndic' => $id_syndic, 'id_thread' => $id_thread, 'statut' => 'publie'), "id_forum = $id_message");
		}
	else if (!$autstat) {	
		sql_updateq('spip_forum', array('id_parent' => $id_forum, 'id_rubrique' => $id_rubrique, 'id_article' => $id_article, 'id_breve' => $id_breve, 'id_syndic' => $id_syndic, 'id_thread' => $id_thread, 'statut' => $statut), "id_forum = $id_message");
		}
	else {
		sql_updateq('spip_forum', array('id_parent' => $id_forum, 'id_rubrique' => $id_rubrique, 'id_article' => $id_article, 'id_breve' => $id_breve, 'id_syndic' => $id_syndic, 'id_thread' => $id_thread, 'statut' => $statut), "id_forum = $id_message");
	}
	}
	// Entrer les mots-cles associes
	if ($ajouter_mot) mots_du_forum($ajouter_mot, $id_message);

	//
	// Entree du contenu et invalidation des caches
	//
	include_spip('inc/modifier');
	revision_forum($id_message, $c);

	// Ajouter un document
	if (isset($_FILES['ajouter_document'])
	AND $_FILES['ajouter_document']['tmp_name']) {
		$ajouter_documents = charger_fonction('ajouter_documents', 'inc');
		$ajouter_documents(
			$_FILES['ajouter_document']['tmp_name'],
			$_FILES['ajouter_document']['name'], 'forum', $id_message,
			'document', 0, $documents_actifs);
		// supprimer le temporaire et ses meta donnees
		spip_unlink($_FILES['ajouter_document']['tmp_name']);
		spip_unlink(preg_replace(',\.bin$,',
			'.txt', $_FILES['ajouter_document']['tmp_name']));
	}

	// Notification
	if ($notifications = charger_fonction('notifications', 'inc'))
		$notifications('forumposte', $id_message);

	return $id_message;
}

// calcul de l'adresse de retour en cas d'echec du POST
// mais la veritable adresse de retour sera calculee apres insertion
?>