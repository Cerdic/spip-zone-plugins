<?php

/**
 * Plugin No-SPAM
 * (c) 2008 Cedric Morin Yterium.net
 * Licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

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
		include_spip("inc/nospam");
		// regarder si il y a du contenu en dehors des liens !
		$caracteres = compter_caracteres_utiles(_request('texte'));
		$min_length = (defined('_FORUM_LONGUEUR_MINI') ? _FORUM_LONGUEUR_MINI : 10);
		if ($caracteres < $min_length){
			$flux['data']['texte'] = _T('forum_attention_dix_caracteres');
			unset($flux['data']['previsu']);
		}
	}

	return $flux;
}