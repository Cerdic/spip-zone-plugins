<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// Inhiber la notification par défaut, pour changer le mot de passe nous-mêmes avant de générer le squelette
function envoyer_inscription($desc, $nom, $statut, $options){
	include_spip('action/editer_auteur');
	
	// Récupérer l'email, pour retrouver l'identifiant de l'utilisateur
	$email = $desc['email'];
	
	// Si tout s'est bien passé avant, SPIP a déjà créé l'auteur et lui a déjà donné un login et pass
	if ($user = sql_fetsel('*', 'spip_auteurs', 'email='.sql_quote($email))){
		// On modifie le mot de passe en utilisant les API de SPIP
		auteur_instituer($user['id_auteur'], array('pass' => _request('password')));
	}
	
	// On modifie l'information de mot de passe
	$desc['pass'] = _request('password');
	
	// On continue comme la fonction d'origine
	$contexte = array_merge($desc,$options);
	$contexte['nom'] = $nom;
	$contexte['mode'] = $mode;
	$contexte['url_confirm'] = generer_url_action('confirmer_inscription','',true,true);
	$contexte['url_confirm'] = parametre_url($contexte['url_confirm'],'email',$desc['email']);
	$contexte['url_confirm'] = parametre_url($contexte['url_confirm'],'jeton',$desc['jeton']);

	$message = recuperer_fond('modeles/mail_inscription',$contexte);
	$from = (isset($options['from'])?$options['from']:null);
	$head = null;
	return array("", $message,$from,$head);
}
