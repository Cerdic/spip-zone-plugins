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
 * Ajouter le champ de formulaire 'nobot' au besoin
 *
 * @param array $flux
 * @return array
 */
function nospam_recuperer_fond($flux){
	// determiner le nom du formulaire
	$fond = $flux['args']['fond'];
	if (false !== $pos = strpos($fond, 'formulaires/')) {
		$form = substr($fond, $pos + 12);
		if (in_array($form, $GLOBALS['formulaires_no_spam'])){
			// on ajoute le champ 'nobot' si pas present dans le formulaire
			$texte = &$flux['data']['texte'];
			if ((false === strpos($texte, 'name="nobot"'))
			and (false !== $pos = strpos($texte, '</form>'))) {
				$nobot = recuperer_fond("inclure/nobot", array('nobot'=>''));
				$texte = substr_replace($texte, $nobot, $pos, 0);
			}
		}
	}
	return $flux;
}

/**
 * Ajouter un jeton temporaire lie a l'heure et a l'IP pour limiter la reutilisation possible du formulaire
 *
 * @param array $flux
 * @return array
 */
function nospam_formulaire_charger($flux){
	$form = $flux['args']['form'];
	if (in_array($form, $GLOBALS['formulaires_no_spam'])){
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
	if (in_array($form, $GLOBALS['formulaires_no_spam'])){
		include_spip("inc/nospam");
		$jeton = _request('_jeton');
		// le jeton prend en compte l'heure et l'ip de l'internaute
		if (_request('nobot') // trop facile !
		OR (!verifier_jeton($jeton, $form))){
			#spip_log('pas de jeton pour '.var_export($flux,true),'nospam');
			$flux['data']['message_erreur'] .= _T('nospam:erreur_jeton');
			if ($form=='forum')
				unset($flux['data']['previsu']);
		}
	}
	if ($form=='forum'){
		if (!isset($flux['data']['texte'])
			AND $GLOBALS['meta']['forums_texte'] == 'oui'){
			include_spip("inc/nospam");
			// regarder si il y a du contenu en dehors des liens !
			$caracteres = compter_caracteres_utiles(_request('texte'));
			if ($caracteres < 10){
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
		// sauf si le posteur a de toute facon le pouvoir de moderer et de se publier
		include_spip('inc/autoriser');
	  if ($flux['data']['statut'] == 'publie'
	  AND (!isset($GLOBALS['visiteur_session']['id_auteur']) OR !autoriser('modererforum'))){

			$email = strlen($flux['data']['email_auteur']) ? " OR email_auteur=".sql_quote($flux['data']['email_auteur']):"";
			$spammeur_connu = (sql_countsel('spip_forum','(ip='.sql_quote($GLOBALS['ip'])."$email) AND statut='spam'")>0);

			// si c'est un message bourre de liens, on le modere
			// le seuil varie selon le champ et le fait que le spammeur est deja connu ou non
			$seuils = array(
				// seuils par defaut
				0=>array(
					0=>array(1=>'prop',3=>'spam'), // seuils par defaut
					'url_site' => array(2=>'spam'), // 2 liens dans le champ url, c'est vraiment louche
					'texte'=>array(4=>'prop',20=>'spam') // pour le champ texte
				),
				// seuils severises pour les spammeurs connus
				'spammeur'=>array(
					0=>array(1=>'spam'),
					'url_site' => array(2=>'spam'), // 2 liens dans le champ url, c'est vraiment louche
					'texte'=>array(1=>'prop',5=>'spam')
				)
			);

			$seuils = $spammeur_connu?$seuils['spammeur']:$seuils[0];
			include_spip("inc/nospam"); // pour analyser_spams()
			foreach($flux['data'] as $champ=>$valeur) {
				$infos = analyser_spams($valeur);
				if ($infos['nombre_liens'] > 0) {
					// si un lien a un titre de moins de 3 caracteres, c'est louche...
					if ($infos['caracteres_texte_lien_min'] < 3) {
						$flux['data']['statut'] = 'prop'; // en dur en attendant une idee plus generique
					}
					
					if (isset($seuils[$champ]))
						$seuil = $seuils[$champ];
					else
						$seuil = $seuils[0];

					foreach($seuil as $s=>$stat)
						if ($infos['nombre_liens'] >= $s)
							$flux['data']['statut'] = $stat;
				}
			}

			// verifier qu'un message identique n'a pas ete publie il y a peu
			if ($flux['data']['statut'] != 'spam'){
				if (sql_countsel('spip_forum','texte='.sql_quote($flux['data']['texte'])." AND statut IN ('publie','off','spam')")>0)
					$flux['data']['statut']='spam';
			}
			// verifier que cette ip n'en est pas a son N-ieme post en peu de temps
			// plus de 5 messages en 5 minutes c'est suspect ...
			if ($flux['data']['statut'] != 'spam'){
				if (($nb=sql_countsel('spip_forum','ip='.sql_quote($GLOBALS['ip']).' AND maj>DATE_SUB(NOW(),INTERVAL 5 minute)'))>5)
					$flux['data']['statut']='spam';
				#spip_log("$nb post pour l'ip ".$GLOBALS['ip']." dans les 5 dernieres minutes",'nospam');
			}
	  }
	}
	return $flux;
}


?>
