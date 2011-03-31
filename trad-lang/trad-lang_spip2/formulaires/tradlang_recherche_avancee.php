<?php 

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * chargement des valeurs par defaut des champs du #FORMULAIRE_RECHERCHE
 * on peut lui passer l'url de destination en premier argument
 *
 * @param string $lien
 * @return array
 */
function formulaires_tradlang_recherche_avancee_charger_dist($lien = ''){
	if ($GLOBALS['spip_lang'] != $GLOBALS['meta']['langue_site'])
		$lang = $GLOBALS['spip_lang'];
	else
		$lang='';

	return 
		array(
			'action' => ($lien ? $lien : generer_url_public('recherche')), # action specifique, ne passe pas par Verifier, ni Traiter
			'recherche' => _request('recherche'),
			'status' => _request('status'),
			'module' => _request('module'),
			'lang_string' => _request('lang_string'),
			'lang' => $lang
		);
}

?>