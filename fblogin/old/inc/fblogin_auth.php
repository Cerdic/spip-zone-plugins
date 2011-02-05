<?php
/*
 * Plugin FBLogin / gestion du login FB
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */

/**
 * Verification de l'authentification par Face book
 * renvoie l'url de la page ou rediriger le visiteur en fonction du succes ou non de l'authentification
 *
 * @return string
 */
function inc_fblogin_auth_dist(){
	$redirect = '';
	// charger l'auth fb dans la session si necessaire
	if (defined('_FB_API_KEY') AND $auth_token = _request('auth_token')){
		include_spip('inc/fb_lib');
		try {
			$api_client = new FacebookRestClient(_FB_API_KEY, _FB_SECRET);
			$_SESSION['fb_session'] = $api_client->auth_getSession($auth_token);
		}
		catch (Exception $e){
			unset($_SESSION['fb_session']);
		}
	}

	if (isset($_SESSION['fb_session'])){
		include_spip('base/abstract_sql');
		include_spip('inc/fb_lib');
		$api_client = new FacebookRestClient(_FB_API_KEY, _FB_SECRET, $_SESSION['fb_session']['session_key']);
		$uid = $api_client->users_getLoggedInUser();
		if (isset($GLOBALS['visiteur_session']['id_auteur'])){
			// verifier que l'auteur spip loggue est bien le meme que le facebookeur
			$res = sql_select('id_auteur','spip_auteurs','fb_uid='.sql_quote($uid));
			if (sql_count($res)==0){
				// on note sur l'auteur loggue son uid facebook
				sql_updateq('spip_auteurs',array('fb_uid'=>$uid),'id_auteur='.intval($GLOBALS['visiteur_session']['id_auteur']));
			}
			elseif ((sql_count($res)>1)
			  OR ( ($row = sql_fetch($res)) AND ($row['id_auteur']==$GLOBALS['visiteur_session']['id_auteur']))
			  ){
				// il n'y a pas correspondance : facebooker != auteur loggue ou plusieurs auteurs identifies pour ce facebookeur
				// on le delog
				$profil_deconnecter = charger_fonction('profil_deconnecter','inc');
				$profil_deconnecter();
			}
		}
		if (!isset($GLOBALS['visiteur_session']['id_auteur'])) {
			$res = sql_select('id_auteur','spip_auteurs','fb_uid='.sql_quote($uid));
			if ((sql_count($res)==1) AND ($row = sql_fetch($res))){
				// ok on a trouve l'auteur qui correspond
				// on le loggue
				$profil_connecter = charger_fonction('profil_connecter','inc');
				$profil_connecter($row['id_auteur'],'fb');
	
				// et on le redirige sur ma page
				$redirect = _ID_ARTICLE_MAPAGE;
			}
		}

		// transferer l'id de session de la session php vers la session spip
		if (isset($GLOBALS['visiteur_session']['id_auteur'])){
			$api_client = new FacebookRestClient(_FB_API_KEY, _FB_SECRET, $_SESSION['fb_session']['session_key']);
			$_SESSION['fb_session']['app_added'] = $api_client->users_isAppAdded();
			include_spip('inc/session');
			session_set('fb_session',$_SESSION['fb_session']);
			unset($_SESSION['fb_session']);
		}
	}
	if (!$redirect) {
		if (($next = _request('redirect'))
		  && !preg_match(',^[a-z]+::[/][/],i',$next))
		  $redirect = $next;
		else {
			if (!isset($GLOBALS['visiteur_session']['id_auteur']))
				$redirect = generer_url_public('sommaire','inscription=oui');
			else
				$redirect = generer_url_entite($GLOBALS['visiteur_session']['id_auteur'],'auteur');
		}
	}
	else {
		$redirect = generer_url_entite($redirect,'article');
	}
	return $redirect;
}

?>
