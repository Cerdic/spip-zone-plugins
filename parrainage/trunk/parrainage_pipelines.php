<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function parrainage_taches_generales_cron($taches){
	$taches['parrainage_contacts'] = 24 * 3600;
	return $taches;
}

/**
 * Insertion dans le pipeline formulaire_charger (SPIP)
 * 
 * On charge le code d'activation si présent dans l'URL
 * 
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié
 */
function parrainage_formulaire_charger($flux){
	if ($flux['args']['form'] == 'inscription'){
		if ($code_invitation = _request('invitation'))
			$flux['data']['code_invitation'] = $code_invitation;
		else
			$flux['data']['code_invitation'] = '';
	}
	
	return $flux;
}

/**
 * Insertion dans le pipeline recuperer_fond (SPIP)
 * 
 * Sur le formulaire d'inscription, on ajoute la saisie du code d'invitation
 * 
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié
 */
function parrainage_recuperer_fond($flux){
	if ($flux['args']['fond'] == 'formulaires/inscription'){
		include_spip('inc/config');
		$obligatoire = lire_config('parrainage/invitation_obligatoire','') ? 'oui' : 'non';
		$saisie = recuperer_fond(
			'saisies/_base',
			array($flux['args']['contexte']['code_invitation'],
				'type_saisie' => 'input',
				'nom' => 'code_invitation',
				'valeur' => $flux['args']['contexte']['code_invitation'],
				'erreurs' => $flux['args']['contexte']['erreurs'],
				'label' => _T('parrainage:inscription_code_invitation_label'),
				'obligatoire' => $obligatoire
			)
		);
		$flux['data']['texte'] = preg_replace("%<li class='.*?saisie_mail_inscription.*?</li>%is", '$0'.$saisie, $flux['data']['texte']);
	}
	
	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_verifier
 * 
 * Vérifications sur le code d'inscription :
 * -* Si le code d'invitation est obligatoire, refuser l'inscription si pas de code d'invitation
 * ou code d'invitation invalide;
 * -* Si code d'invitation fourni, refuser l'inscription si l'email ne correspond pas 
 * 
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié
 */
function parrainage_formulaire_verifier($flux){
	if ($flux['args']['form'] == 'inscription'){
		include_spip('inc/config');
		$code_invitation = _request('code_invitation');
		// Si l'invitation est obligatoire
		if ((lire_config('parrainage/invitation_obligatoire','') == 'on') and !$code_invitation){
			$flux['data']['code_invitation'] = _T('parrainage:erreur_invitation_obligatoire');
		}
		// Si le code d'invitation est dans l'URL mais ne correspond pas à l'email donné
		elseif ($code_invitation && (!$email = sql_getfetsel('email', 'spip_filleuls', 'code_invitation = '.sql_quote($code_invitation))
			or $email != _request('mail_inscription'))
		){
			$flux['data']['code_invitation'] = _T('parrainage:erreur_invitation_invalide');
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_traiter (SPIP)
 * 
 * Au traitement du formulaire d'inscription, on update le filleul lié au code d'inscription
 * 
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié
 */
function parrainage_formulaire_traiter($flux){
	if ($flux['args']['form'] == 'inscription' and $code_invitation = _request('code_invitation')){
		// On doit d'abord chercher l'id_auteur qui vient d'être créé
		$email = _request('mail_inscription');
		$id_auteur = sql_getfetsel('id_auteur', 'spip_auteurs', 'email = '.sql_quote($email));
		
		// Si l'auteur est bien là, on fait le lien entre le nouvel inscrit et le filleul
		if ($id_auteur > 0)
			sql_updateq(
				'spip_filleuls',
				array(
					'statut' => 'filleul',
					'id_auteur' => $id_auteur
				),
				array(
					'code_invitation = '.sql_quote($code_invitation),
					'email = '.sql_quote(_request('mail_inscription'))
				)
			);
	}
	
	return $flux;
}

?>
