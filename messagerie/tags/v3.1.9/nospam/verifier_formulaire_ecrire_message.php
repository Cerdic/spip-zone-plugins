<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Verification supplementaire antispam sur le formulaire_ecrire_message
 *
 * @param array $flux
 * @return array
 */
function nospam_verifier_formulaire_ecrire_message_dist($flux){
	$form = $flux['args']['form'];
	if (!isset($flux['data']['texte_message'])){

		$texte_message = _request('texte_message');
		include_spip("inc/nospam");

		// verifier le status de cette IP
		nospam_check_ip_status($GLOBALS['ip']);

		$email = strlen($flux['data']['email_auteur']) ? " OR email_auteur=" . sql_quote($flux['data']['email_auteur']) : "";
		$spammeur_connu = (isset($GLOBALS['ip_greylist'][$GLOBALS['ip']])
				OR isset($GLOBALS['ip_blacklist'][$GLOBALS['ip']])
		);

		// activer aussi le flag spammeur connu en cas de flood, meme si aucune detection spam jusqu'ici
		if (!$spammeur_connu){            
			$query="SELECT COUNT(id_objet) AS nb_message_envoye_24h FROM spip_auteurs_liens JOIN spip_messages ON spip_auteurs_liens.id_objet = spip_messages.id_message WHERE objet='message' AND type='normal' AND spip_messages.id_auteur=".$GLOBALS['visiteur_session']['id_auteur']." AND timestampdiff(hour, date_heure, NOW()) < 24";
			$r = spip_query($query);
			if ($r) {
				$arr = spip_fetch_array($r);
				if ( $arr["nb_message_envoye_24h"] >= _NB_MESSAGES_MAX_JOUR){
					spip_log("[Flood] ".$arr["nb_message_envoye_24h"]." messages (id_auteur=".$GLOBALS['visiteur_session']['id_auteur'].") dans les 24 dernieres heures",'nospam');
					$spammeur_connu = true;
				}
			}
		}
		
		if ( $spammeur_connu ){
			$flux['data']['objet'] = _T('nospam:erreur_spam');
			unset($flux['data']['previsu']);    				
		}

		// regarder si il y a du contenu cache
		if (!isset($flux['data']['texte_message'])){
			$infos = analyser_spams($texte_message);
			if (isset($infos['contenu_cache']) AND $infos['contenu_cache']){
				$flux['data']['texte_message'] = _T('nospam:erreur_attributs_html_interdits');
			}
		}

		// on analyse le sujet
		$infos_sujet = analyser_spams(_request('objet'));
		// si un lien dans le sujet = spam !
		if ($infos_sujet['nombre_liens'] > 0){
			$flux['data']['objet'] = _T('nospam:erreur_spam');
			unset($flux['data']['previsu']);
		}

		// on analyse le texte
		$infos_texte = analyser_spams($texte_message);
		if ($infos_texte['nombre_liens'] > 0) {
			// si un lien a un titre de moins de 3 caracteres = spam !
			if ($infos_texte['caracteres_texte_lien_min'] < 3) {
				$flux['data']['texte_message'] = _T('nospam:erreur_spam');
			}
			// si le texte contient plus de trois liens = spam !
			if ($infos_texte['nombre_liens'] >= 3)
				$flux['data']['texte_message'] = _T('nospam:erreur_spam');
		}
        
		// si il y a une erreur, pas de previsu, on reste bloque a la premiere etape
		if (isset($flux['data']['texte_message'])){
			unset($flux['data']['previsu']);
		}
	}

	return $flux;
}
