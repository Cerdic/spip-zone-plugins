<?php

/**
 * Plugin No-SPAM
 * (c) 2008 Cedric Morin Yterium.net
 * Licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Verification supplementaire antispam sur le formulaire_ecrire_auteur
 *
 * @param array $flux
 * @return array
 */
function nospam_verifier_formulaire_ecrire_auteur_dist($flux){
	$form = $flux['args']['form'];
	if (!isset($flux['data']['texte_message_auteur'])){
		include_spip("inc/nospam");
		include_spip("inc/texte");
		// regarder si il y a du contenu en dehors des liens !
		$texte_message_auteur = _request('texte_message_auteur');
		$caracteres = compter_caracteres_utiles($texte_message_auteur);
		if ($caracteres < 10){
			$flux['data']['texte_message_auteur'] = _T('forum_attention_dix_caracteres');
			unset($flux['data']['previsu']);
		}
		// on analyse le sujet
		$infos_sujet = analyser_spams(_request('sujet_message_auteur'));
		// si un lien dans le sujet = spam !
		if ($infos_sujet['nombre_liens'] > 0){
			$flux['data']['sujet_message_auteur'] = _T('nospam:erreur_spam');
			unset($flux['data']['previsu']);
		}

		// on analyse le texte
		$infos_texte = analyser_spams($texte_message_auteur);
		if ($infos_texte['nombre_liens'] > 0) {
			// si un lien a un titre de moins de 3 caracteres = spam !
			if ($infos_texte['caracteres_texte_lien_min'] < 3) {
				$flux['data']['texte_message_auteur'] = _T('nospam:erreur_spam');
			}
			// si le texte contient plus de trois liens = spam !
			if ($infos_texte['nombre_liens'] >= 3)
				$flux['data']['texte_message_auteur'] = _T('nospam:erreur_spam');
		}
	}

	return $flux;
}