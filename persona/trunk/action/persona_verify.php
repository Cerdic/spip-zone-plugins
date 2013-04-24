<?php

/*
 *  Plugin persona pour SPIP
 *
 *  (c) Fil 2011 - Licence GNU/GPL
 *
 */

define('_PERSONA_VERIFY', "https://verifier.login.persona.org/verify");


function persona_auth_loger($auteur, &$a) {
	include_spip('inc/auth');
	include_spip('inc/texte');
	auth_loger($auteur);
	$a['session_nom'] = typo($auteur['nom']);
	$a['session_statut'] = $auteur['statut'];
	$a['autoriser_ecrire'] = autoriser('ecrire');
	$a['autoriser_url'] = true;

}

function action_persona_verify() {

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
			$d = recuperer_page(_PERSONA_VERIFY, false, false, null,
			$data = array(
				'assertion' => $assertion,
				'audience' => $audience
			)
			# forcer l'absence de boundary : persona.org/verify ne le tolere pas
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
						persona_auth_loger($auteur, $a);

						# envoyer une action javascript
#						if ($auteur['statut'] == '0minirezo') {
#							$a['action'] = 'console.log("tu es admin mon pote");';
#						}
						# ou envoyer un message
						#$a['message'] = 'Welcome '.$a['session_nom'];

					}

					else
					/* OPTION : creer un compte pour l'auteur */
					{
						$statut_inscription = null;
						if ($GLOBALS['meta']["accepter_inscriptions"] == 'oui')
							$statut_inscription = '1comite';
						else if ($GLOBALS['meta']["forums_publics"] == 'abo')
							$statut_inscription = '6forum';
						else if ($GLOBALS['meta']["accepter_visiteurs"] == 'oui')
							$statut_inscription = '6forum';

						if ($statut_inscription) {
							include_spip('formulaires/inscription');
							#$login = test_login(
							#		preg_replace(',@.*,', '', $a['email']), $a['email']
							#	); # unicite a la rache.

							sql_insertq('spip_auteurs', array(
								'email' => $a['email'],
								'statut' => $statut_inscription,
								'nom' => preg_replace('/@.*/', '', $a['email']),
								'login' => $a['email']
							));
							$auteur = sql_fetsel('*', 'spip_auteurs', 'email='.sql_quote($a['email']));
							persona_auth_loger($auteur, $a);
						}
					}

					session_set('session_email', $a['email']);
					session_set('email_confirme', $a['email']);

				} else {
					$a['status'] = 'failure';
				}
			}
			else {
				$a['status'] = 'failure';
				$a['reason'] = "could not connect to the verification server; please retry";
			}
		}
	} else {
		$a['status'] = 'failure';
		$a['reason'] = "need assertion and audience";
	}


	header('Content-Type: text/json; charset=utf-8');
	echo json_encode((object) $a);
}

