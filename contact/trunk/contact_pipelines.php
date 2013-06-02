<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Pipeline jqueryui_plugins (SPIP) pour demander au plugin l'insertion des scripts pour .sortable()
 *
 * @param array $plugins
 * @return array
 */
function contact_jqueryui_plugins($plugins){
	if(test_espace_prive()){
		$plugins[] = "jquery.ui.core";
		$plugins[] = "jquery.ui.widget";
		$plugins[] = "jquery.ui.mouse";
		$plugins[] = "jquery.ui.sortable";
	}
	return $plugins;
}

/**
 * Insertion dans le pipeline formulaire_verifier (SPIP)
 * 
 * Dans le formulaire d'inscription, si on a un message d'erreur,
 * on vérifie que ce n'est pas dû au fait que l'auteur a le statut "contact" à cause d'un message 
 * de contact envoyé. 
 * 
 * Si c'est le cas : 
 * - on change son statut de contact à nouveau
 * - on met le statut d'inscription dans prefs
 * - on met comme login son adresse email
 * - on lui crée un pass aléatoirement
 * - on lui indique en erreur qu'il était déjà inscrit et qu'il doit renouveler son pass
 * 
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié s'il y a lieu
 */
function contact_formulaire_verifier($flux){
	if ($flux['args']['form'] == 'inscription' && isset($flux['data']['message_erreur'])){
		if($email = _request('mail_inscription')){
			$auteur = sql_fetsel('*','spip_auteurs','email='.sql_quote($email));
			if($auteur['statut'] == 'contact'){
				$statut_inscription = $flux['args']['args'][0];
				include_spip('action/inscrire_auteur');
				creer_pass_pour_auteur($auteur['id_auteur']);
				sql_updateq('spip_auteurs',array('prefs' => $statut_inscription,'statut' => 'nouveau','login'=> $email),'id_auteur = '.intval($auteur['id_auteur']));
				$flux['data']['message_erreur'] = _T('contact:message_redemander_pass',array('email'=>$email,'url_pass'=>generer_url_public('spip_pass')));
			}		
		}
	}
	return $flux;
}
?>