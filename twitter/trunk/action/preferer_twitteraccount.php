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
 * Supprimer un utilisateur Twitter associe a l'application
 *
 * @param null|string $account
 */
function action_preferer_twitteraccount_dist($account = null) {
	if (is_null($account)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$account = $securiser_action();
	}

	include_spip("inc/autoriser");
	if(autoriser("preferer","twitteraccount",$account)){

		$cfg = @unserialize($GLOBALS['meta']['microblog']);
		if (isset($cfg['twitter_accounts'][$account])){
			$cfg['default_account'] = $account;

			ecrire_meta("microblog", serialize($cfg));
		}
	}
}
?>