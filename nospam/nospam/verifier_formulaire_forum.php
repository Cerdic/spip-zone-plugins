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


			if ($h = rechercher_presence_liens_spammes($infos['liens'],_SPAM_URL_MAX_OCCURENCES,'spip_forum',array('texte'))){
				spip_log("Refus message de forum qui contient un lien vers $h","nospam");
				$flux['data']['texte'] = _T('nospam:erreur_url_deja_spammee');
			}
		}

		// on prend en compte la checkbox de confirmation
		// si le flag en session est bien leve
		if (_request('notabuse')){
			session_start();
			if ($_SESSION['notabuse_check']){
				unset($_SESSION['notabuse_check']);
				$_SESSION['notabuse_checked'] = true;
			}
		}
		if (!count($flux['data'])){
			if (nospam_check_ip_status($GLOBALS['ip'])!=='ok'){
				session_start();
				if ($_SESSION['notabuse_checked']){
					// ok on retire de la session le check qui ne sert qu'une fois
					unset($_SESSION['notabuse_checked']);
					// et on laisse passer
				}
				else {
					$flux['data']['texte'] = _T('nospam:info_ip_suspecte')."<br />
					<span class='choix'>
					<input type='checkbox' name='notabuse' value='1' id='notabuse'/> <label for='notabuse'>"
					._T('nospam:label_message_licite')."</label>
					</span>";
					session_start();
					$_SESSION['notabuse_check'] = true;
				}
			}
		}


		if (isset($flux['data']['texte']))
			unset($flux['data']['previsu']);
	}

	return $flux;
}