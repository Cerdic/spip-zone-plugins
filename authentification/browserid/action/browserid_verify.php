<?php

/*
 *  Plugin BrowserID pour SPIP
 *
 *  (c) Fil 2011 - Licence GNU/GPL
 *
 */

define('_BROWSERID_VERIFY', "https://browserid.org/verify");

function action_browserid_verify() {

	$a = array();

	if ($assertion = _request('assertion')
	AND $audience = _request('audience')
	) {
		include_spip('inc/filtres_mini');
		$server = url_absolue('/');
		if ($server !== "$audience/") {
			$a['status'] = 'failure';
			$a['reason'] = "incorrect audience";
		}
		else {
			include_spip('inc/distant');
			$d = recuperer_page(_BROWSERID_VERIFY, false, false, null,
			$data = array(
				'assertion' => $assertion,
				'audience' => $audience
			)
			# forcer l'absence de boundary : browserid.org/verify ne le tolere pas
			# cf. https://github.com/mozilla/browserid/issues/649
			, $boundary = false
			);

			if ($dd = @json_decode($d)) {
				$a = (array) $dd;
				if ($a['status'] == 'okay'
				AND $m = $a['email']
				AND $a['expires'] > time()
				AND $a['audience']."/" == $server
				) {
					include_spip('inc/session');

					// on verifie si l'auteur existe deja en base, si oui on le loge
					include_spip('base/abstract_sql');
					$auteur = sql_fetsel('*', 'spip_auteurs', 'email='.sql_quote($a['email']));
					if ($auteur) {
						include_spip('inc/auth');
						auth_loger($auteur);
						include_spip('inc/texte');
						$a['session_nom'] = typo($auteur['nom']);
						$a['session_statut'] = $auteur['statut'];
						$a['autoriser_ecrire'] = autoriser('ecrire');

						# envoyer une action javascript
#						if ($auteur['statut'] == '0minirezo') {
#							$a['action'] = 'console.log("tu es admin mon pote");';
#						}
						# ou envoyer un message
						#$a['message'] = 'Welcome '.$a['session_nom'];

					}

					session_set('session_email', $a['email']);
					session_set('email_confirme', $a['email']);

				} else {
					$a['status'] = 'failure';
				}
			}
			else {
				$a['status'] = 'failure';
				$a['reason'] = "could not connect to the verification server";
			}
		}
	} else {
		$a['status'] = 'failure';
		$a['reason'] = "need assertion and audience";
	}


	header('Content-Type: text/json; charset=utf-8');
	echo json_encode((object) $a);
}

