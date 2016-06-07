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
	//var_dump('test');
	//if (autoriser('creer', 'shortcuturls')) {
		header('Content-Type: application/json');
		if (!$url) {
			$url = _request('url');
		}
		$url = parametre_url(_request('url'), 'var_mode', '');
		$shortcut_url = sql_getfetsel('titre', 'spip_shortcut_urls', 'url=' . sql_quote($url));
		if ($shortcut_url) {
			include_spip('inc/invalideur');
			suivre_invalideur(0);
			die(json_encode(array('url' => url_absolue($shortcut_url), 'new' => false)));
		} else {
			include_spip('inc/actions');
			include_spip('inc/editer');
			include_spip('action/editer_objet');
			include_spip('inc/distant');
			include_spip('inc/filtres');
			$recup = recuperer_page($url, true);
			if (preg_match(',<title[^>]*>(.*),i', $recup, $regs)) {
				$result['nom_site'] = filtrer_entites(supprimer_tags(preg_replace(',</title>.*,i', '', $regs[1])));
			}

			if (defined('_TAILLE_RACCOURCI')) {
				if (_TAILLE_RACCOURCI >= 5) {
					$taille_raccourci = _TAILLE_RACCOURCI;
				} else {
					$taille_raccourci = 8;
				}
			} else {
				$taille_raccourci = 8;
			}

			if (_request('titre')) {
				$set['titre'] = _request('titre');
			} else {
				$set['titre'] = generer_chaine_aleatoire($taille_raccourci);
			}
			$set['description'] = $result['nom_site'];
			// On supprime ?var_mode=recalcul et autres var_mode
			$set['url'] = $url;
			$set['ip_address'] = $GLOBALS['ip'];
			$set['date_modif'] = date('Y-m-d H:i:s');

			$editer_objet = charger_fonction('editer_objet', 'action');
			list($id, $err) = $editer_objet('new', 'shortcut_url', $set);
			include_spip('inc/invalideur');
			suivre_invalideur(0);
			die(json_encode(array('url' => url_absolue(generer_url_entite($id, 'shortcut_url')), 'new' => true)));
		}
		die(json_encode(array('plouf' => 'plouf')));
	//}
}
