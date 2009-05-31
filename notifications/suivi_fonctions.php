<?php

if (!$GLOBALS['visiteur_session']['id_auteur']) {
	include_spip('inc/headers');
	redirige_par_entete(parametre_url(generer_url_public('login'), 'url', self(), '&'));
} else {
	// Si l'auteur a un email valide, on lui reaffecte tous les forums
	// signes de son email
	if (strlen($GLOBALS['visiteur_session']['email'])) {
		include_spip('base/abstract_sql');
		sql_update('spip_forum', array('id_auteur' => $GLOBALS['visiteur_session']['id_auteur']), 'id_auteur=0 AND email_auteur='.sql_quote($GLOBALS['visiteur_session']['email']));
	}

}

?>
