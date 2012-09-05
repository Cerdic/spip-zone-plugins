<?php

/**
 * Plugin No-SPAM
 * (c) 2008 Cedric Morin Yterium.net
 * Licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_SPAM_URL_MAX_OCCURENCES')) define('_SPAM_URL_MAX_OCCURENCES',3);

/**
 * Verification supplementaire antispam sur le formulaire_forum
 *
 * @param array $flux
 * @return array
 */
function nospam_verifier_formulaire_forum_dist($flux){
	$form = $flux['args']['form'];
	if (!isset($flux['data']['texte'])
		AND $GLOBALS['meta']['forums_texte'] == 'oui'){

		$texte = _request('texte');
		include_spip("inc/nospam");
		// regarder si il y a du contenu en dehors des liens !
		$caracteres = compter_caracteres_utiles($texte);
		$min_length = (defined('_FORUM_LONGUEUR_MINI') ? _FORUM_LONGUEUR_MINI : 10);
		if ($caracteres < $min_length){
			$flux['data']['texte'] = _T('forum_attention_dix_caracteres');
		}

		// regarder si il y a du contenu cache
		if (!isset($flux['data']['texte'])){
			$infos = analyser_spams($texte);
			if (isset($infos['contenu_cache']) AND $infos['contenu_cache']){
				$flux['data']['texte'] = _T('nospam:erreur_attributs_html_interdits');
			}
		}

		// regarder si il y a des liens deja references par des spammeurs
		if (!isset($flux['data']['texte'])
		  AND isset($infos['liens'])
		  AND count($infos['liens'])){

			$hosts = array();
			foreach ($infos['liens'] as $lien){
				$url = extraire_attribut($lien,"href");
				if ($parse = parse_url($url)
				  AND $parse['host'])
					$hosts[] = $parse['host'];
			}

			$hosts = array_unique($hosts);
			$hosts = array_filter($hosts);

			// pour chaque host figurant dans un lien, regarder si on a pas deja eu des spams avec ce meme host
			// auquel cas on refuse poliment le message
			foreach($hosts as $h){
				if (sql_countsel("spip_forum","statut=".sql_quote('spam')." AND texte LIKE ".sql_quote("%$h%"))>=_SPAM_URL_MAX_OCCURENCES){
					spip_log("Refus message de forum qui contient un lien vers $h","nospam");
					$flux['data']['texte'] = _T('nospam:erreur_url_deja_spammee');
					break; // pas la peine de continuer avec les autres liens
				}
			}
		}

		if (isset($flux['data']['texte']))
			unset($flux['data']['previsu']);
	}

	return $flux;
}