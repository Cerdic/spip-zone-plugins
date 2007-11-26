<?php

// Si l'on se connecte sur cette page avec un parametre p=..., il faut valider
// l'auteur, le connecter et activer sa session.
if (_request('p') !== null) {
	$s = spip_query('SELECT * FROM spip_auteurs WHERE cookie_oubli='.sql_quote(_request('p')));
	if (!$t = sql_fetch($s)
	OR !$t['id_auteur']) {
		include_spip('inc/minipres');
		die(minipres());
	}

	// $t est l'auteur souhaite : le valider si besoin
	// et recuperer sous son id_auteur tous ses forums
	if ($t['statut'] == 'nouveau') {
		include_spip('inc/auth');
		$t['statut'] = acces_statut($t['id_auteur'], $t['statut'], $t['bio']);
		sql_update('spip_forum', array('id_auteur', $t['id_auteur']), 'email_auteur='.sql_quote($t['email']));
	}

	// Si ce n'est pas l'auteur connecte, se connecter sous son nom
	if (!isset($GLOBALS['visiteur_session']['id_auteur'])
	OR $GLOBALS['visiteur_session']['id_auteur'] != $t['id_auteur']
	OR $GLOBALS['visiteur_session']['statut'] != $t['statut']) {
		include_spip('inc/session');
		supprimer_sessions($GLOBALS['visiteur_session']['id_auteur']);
		ajouter_session($t);
		include_spip('inc/headers');
		redirige_par_entete(parametre_url(self(), 'p', '', '&'));
	}
}

?>
