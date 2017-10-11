<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Creer shortcut_url
 *
 * URLs de la forme :
 * shortcut_url.api/id_shortcut_url
 *
 * @param null $url
 */
function action_api_shortcut_url_creer($url = false) {
	if (autoriser('creer', 'shortcuturl')) {
		if (!$url) {
			$url = urldecode(_request('url'));
		}
		$url = parametre_url($url, 'var_mode', '', '&');
		if (!$url or $url == './') {
			header('Content-Type: application/json');
			die(json_encode(array('error' => '405', 'message' => 'Missing URL parameter')));
		} else {
			if (filter_var($url, FILTER_VALIDATE_URL) === false) {
				header('Content-Type: application/json');
				die(json_encode(array('error' => '405', 'message' => 'Bad URL format')));
			} else {
				$shortcut_url = sql_getfetsel('titre', 'spip_shortcut_urls', 'url=' . sql_quote($url));
				if ($shortcut_url) {
					include_spip('inc/invalideur');
					suivre_invalideur(0);
					header('Content-Type: application/json');
					die(json_encode(array('url' => url_absolue($shortcut_url), 'new' => false)));
				} else {
					$editer_objet = charger_fonction('editer_objet', 'action');
					list($id, $err) = $editer_objet('new', 'shortcut_url', array('url' => $url));
					include_spip('inc/invalideur');
					suivre_invalideur(0);
					header('Content-Type: application/json');
					die(json_encode(
						array('url' => url_absolue(generer_url_entite($id, 'shortcut_url')), 'new' => true)
					));
				}
			}
		}
	} else {
		header('Content-Type: application/json');
		die(json_encode(array('error' => '401', 'message' => 'Authorization failed')));
	}
}
