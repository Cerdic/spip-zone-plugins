<?php

/**
 * Plugin No-SPAM
 * (c) 2008 Cedric Morin Yterium.net
 * Licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Verification supplementaire antispam sur le formulaire_signature
 *
 * @param array $flux
 * @return array
 */
function nospam_verifier_formulaire_signature_dist($flux){
	$form = $flux['args']['form'];
	$id_article = $flux['args']['args'][0];
	$row = sql_fetsel('*', 'spip_petitions', "id_article=".intval($id_article));
	if ((!isset($flux['data']['message'])) && ($row['message']  == "oui")){
		include_spip("inc/nospam");
		include_spip("inc/texte");
		// regarder si il y a du contenu en dehors des liens !
		$message = _request('message');
		// on analyse le texte
		$infos_texte = analyser_spams($message);
		if ($infos_texte['nombre_liens'] > 0) {
			// si un lien a un titre de moins de 3 caracteres = spam !
			if ($infos_texte['caracteres_texte_lien_min'] < 3) {
				$flux['data']['message_erreur'] = _T('nospam:erreur_spam');
			}
			// si le texte contient plus de trois liens = spam !
			if ($infos_texte['nombre_liens'] >= 2)
				$flux['data']['message_erreur'] = _T('nospam:erreur_spam');
		}
	}
	// S'il y a un lien dans le champ session_nom => spam
	if (!isset($flux['data']['session_nom'])){
		include_spip("inc/nospam");
		$infos_texte = analyser_spams(_request('session_nom'));
		if ($infos_texte['nombre_liens'] > 0) {
			$flux['data']['message_erreur'] = _T('nospam:erreur_spam');
			spip_log("Lien dans le champ session_nom ".$flux['data']['message_erreur'],'nospam');
		}
	}
	return $flux;
}