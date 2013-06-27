<?php
/*
 * Plugin spip|twitter
 * (c) 2009-2013
 *
 * envoyer et lire des messages de Twitter
 * distribue sous licence GNU/LGPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Ajouter un utilisateur
 * Il faut lancer une demande d'autorisation chez Twitter (1er appel)
 * Au second appel (avec $is_callback=true) on recupere les tokens et on ajoute l'utilisateur
 * a la config du plugin
 */
function action_ajouter_twitteraccount_dist($is_callback = false) {
	if (!$is_callback){
		// au premier appel
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();

		include_spip("inc/autoriser");
		if(autoriser("ajouter","twitteraccount")){

			// lancer la demande d'autorisation en indiquant le nom de l'action qui sera rappelee au retour
			include_spip("action/twitter_oauth_authorize");
			twitter_oauth_authorize("ajouter_twitteraccount",_request('redirect'));
		}
	}
	else {
		// appel au retour de l'authorize
		// recuperer le screenname
		$tokens = array(
			'twitter_token' => $GLOBALS['visiteur_session']['access_token']['oauth_token'],
			'twitter_token_secret' => $GLOBALS['visiteur_session']['access_token']['oauth_token_secret'],
		);
		// ajouter le compte aux preferences
		twitter_ajouter_twitteraccount($tokens);
	}
}

/**
 * Ajouter un compte dans la liste des comptes dispos
 * a partir de ses tokens (meme format que dans twitter_connect()
 *
 * @param array $tokens
 *   twitter_token : token du compte a utiliser
 *   twitter_token_secret : token secret du compte a utiliser
 * @return array
 */
function twitter_ajouter_twitteraccount($tokens){
	$cfg = @unserialize($GLOBALS['meta']['microblog']);

	include_spip("inc/twitter");
	$options = $tokens;
	$options['force'] = true;
	if ($res = twitter_api_call("account/verify_credentials","get",array(),$options)){
		$cfg['twitter_accounts'][$res['screen_name']] = array(
			'token' => $tokens['twitter_token'],
			'token_secret' => $tokens['twitter_token_secret'],
		);
	}
	else {
		$cfg['twitter_accounts'][] = array(
			'token' => $tokens['twitter_token'],
			'token_secret' => $tokens['twitter_token_secret'],
		);
		spip_log("Echec account/verify_credentials lors de l'ajout d'un compte","twitter"._LOG_ERREUR);
	}
	if (!isset($cfg['default_account'])
	  OR !isset($cfg['twitter_accounts'][$cfg['default_account']]))
		$cfg['default_account'] = reset(array_keys($cfg['twitter_accounts']));

	ecrire_meta("microblog", serialize($cfg));

	return $cfg;
}
?>