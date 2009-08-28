<?php
/**
 * Plugin No-SPAM
 * (c) 2008 Cedric Morin Yterium.net
 * Licence GPL
 *
 */

// pour verifier le nobot et le jeton sur un formulaire, l'ajouter a cette globale
$GLOBALS['formulaires_no_spam'][] = 'forum';

/**
 * Ajouter un jeton temporaire lie a l'heure et a l'IP pour limiter la reutilisation possible du formulaire
 *
 * @param array $flux
 * @return array
 */
function nospam_formulaire_charger($flux){
	$form = $flux['args']['form'];
	if (in_array($form,$GLOBALS['formulaires_no_spam'])){
		include_spip("inc/nospam");
		$jeton = creer_jeton($form);
		$flux['data']['_hidden'] .= "<input type='hidden' name='_jeton' value='$jeton' />";
	}
	return $flux;
}

/**
 * Verifier le jeton temporaire lie a l'heure et a l'IP pour limiter la reutilisation possible du formulaire
 *
 * @param array $flux
 * @return array
 */
function nospam_formulaire_verifier($flux){
	$form = $flux['args']['form'];
	if (in_array($form,$GLOBALS['formulaires_no_spam'])){
		include_spip("inc/nospam");
		$jeton = _request('_jeton');
		
		// le jeton prend en compte l'heure et l'ip de l'internaute
		if (_request('nobot') // trop facile !
		OR (!verifier_jeton($jeton, $form))){
			$flux['data']['message_erreur'] .= _T('nospam:erreur_jeton');
			if ($form=='forum')
				unset($flux['data']['previsu']);
		}
	}
	if ($form=='forum'){
		if (!isset($flux['data']['texte'])
			AND $GLOBALS['meta']['forums_texte'] == 'oui'){
			// regarder si il y a du contenu en dehors des liens !
			$texte = PtoBR(propre(_request('texte')));
			$texte = preg_replace(',<a.*</a>,Uims','',$texte);
			$texte = trim(preg_replace(',[\W]+,uims',' ',$texte));
			if (strlen($texte) < 10){
				$flux['data']['texte'] = _T('forum_attention_dix_caracteres');
				unset($flux['data']['previsu']);
			}
		}
	}
	return $flux;
}

/**
 * Au moment de decider du statut d'un forum,
 * quelques verifications et une moderation si necessaire !
 *
 * @param array $flux
 * @return array
 */
function nospam_pre_edition($flux){
	if ($flux['args']['table']=='spip_forum'
	  AND $flux['args']['action']=='instituer'){
	  
	  // ne pas publier automatiquement certains messages suspects ...
	  if ($flux['data']['statut'] == 'publie'){
	  	// si c'est un message bourre de liens, on le modere
	  	$texte = propre($flux['data']['texte']);
	  	$liens = extraire_balises($texte,'a');
	  	if (count($liens)>20)
	  		$flux['data']['statut']='spam';
	  	elseif (count($liens)>=4)
	  		$flux['data']['statut']='prop';

			// verifier que cette ip et/ou cet email n'a pas deja envoye du spam
			// auquel cas on modere des qu'il y a un lien
			if ($flux['data']['statut'] == 'publie'){
				$email = $flux['data']['email_auteur'] ? " OR email_auteur=".sql_quote($flux['data']['email_auteur']):"";
				if (sql_countsel('spip_forum','(ip='.sql_quote($GLOBALS['ip'])."$email) AND statut='spam'")>0){
					if (count($liens)>10)
						$flux['data']['statut']='spam';
					elseif (count($liens)>=1)
						$flux['data']['statut']='prop';
				}
			}

			// verifier qu'un message identique n'a pas ete publie il y a peu
			if ($flux['data']['statut'] == 'publie'){
				if (sql_countsel('spip_forum','texte='.sql_quote($flux['data']['texte'])." AND statut IN ('publie','off','spam')")>0)
					$flux['data']['statut']='spam';
			}
			// verifier que cette ip n'en est pas a son N-ieme post en peu de temps
			// plus de 5 messages en 5 minutes c'est suspect ...
			if ($flux['data']['statut'] == 'publie'){
				if (sql_countsel('spip_forum','ip='.sql_quote($GLOBALS['ip']).' AND maj>DATE_SUB(NOW(),INTERVAL 5 minute)')>5)
					$flux['data']['statut']='spam';
			}
	  }
	}
	return $flux;
}


?>
