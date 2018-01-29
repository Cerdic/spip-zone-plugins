<?php

function action_incarner_dist() {

	include_spip('inc/headers');
	include_spip('incarner_fonctions');

	if (! autoriser('incarner')) {
		http_status(403);
		incarner_invalider_cle();
		include_spip('inc/minipres');
		echo minipres();
		exit();
	}

	include_spip('inc/auth');
	if ($login = _request('login')) {
		$auteur = auth_identifier_login($login, '');
		auth_loger($auteur);

		/* Si on vient de se loger dans l'espace privé avec un login qui n'y est
		 * pas autorisé, on redirige vers la page d'accueil, pour éviter un
		 * message d'erreur inutile. */
		if (test_espace_prive() and (! autoriser('ecrire'))) {
			redirige_par_entete(url_de_base());
		}
	} elseif (_request('logout')) {
		incarner_invalider_cle();
		redirige_par_entete(
			html_entity_decode(
				generer_url_action('logout', 'logout=public', false, true)
			)
		);
	}
}
