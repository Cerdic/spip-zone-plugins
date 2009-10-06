<?php

/**
 * Action d'autentification par GFC
 * 
 * @return 
 */

function action_gfc_auth_dist() {
	//ini_set("error_reporting", E_ALL);
	//ini_set("display_errors", 1);
	
	// Set the default timezone since many servers won't have this configured
	//date_default_timezone_set('America/Los_Angeles');
	
	if(function_exists('lire_config')){
		$id = lire_config('gfc/consumer_id') ? lire_config('gfc/consumer_id') : _GFC_CONSUMER_ID;
		$key = lire_config('gfc/consumer_key') ? lire_config('gfc/consumer_key') : _GFC_CONSUMER_KEY;
		$secret = lire_config('gfc/consumer_secret') ? str_replace('*:','',lire_config('gfc/consumer_secret')) : str_replace('*:','',_GFC_CONSUMER_SECRET);
		$default_email = lire_config('gfc/default_email') ? str_replace('*:','',lire_config('gfc/default_email')) : str_replace('*:','',_GFC_DEFAULT_EMAIL);
	}else{
		$id = _GFC_CONSUMER_ID;
		$secret = _GFC_CONSUMER_SECRET;
		$default_email = _GFC_DEFAULT_EMAIL;
	}
	
	$token = $_COOKIE['fcauth'.$id];
	
	//get osapi info
	$display_name = $member_id = false;
	include_spip(_DIR_OSAPI."osapi");
	include_spip("auth/osapiFCAuth");
	include_spip('inc/texte');
	include_spip('base/abstract_sql');
	$provider = new osapiFriendConnectProvider();
	$auth = new osapiFCAuth($token);
	$osapi = new osapi($provider, $auth);
	$strictMode = true;
	if ($osapi) {
		$batch = $osapi->newBatch();
		$request = $osapi->people->get(array('userId'=>'@viewer', 'groupId'=>'@self'));
		$batch->add($request, 'self');
		$result = $batch->execute();
		$me = $result['self'];
		spip_log($result);
		if ($me instanceof osapiError) {
			$code = $me->getErrorCode();
      		$message = $me->getErrorMessage();
			die("$code - $message");
		}
		else{
			$display_name =  $me->getFieldByName("displayName");
			$member_id =  $me->getFieldByName("id");
			if(trim($display_name) == '') $display_name = $member_id;
		}
	}
	//END get osapi info
	
	if($member_id){
		//try to login SPIP if google friend account already binded
		if (login_spip($member_id)){
			
		}
		//elseif member already connected in SPIP...
		else if($GLOBALS['visiteur_session']['id_auteur'] !=''){
			//if he already has a gfc_id, we do nothing !!THIS IS BAD, WE NEED TO WORK ON THIS CASE!!
			$id_auteur = sql_getfetsel("id_auteur","spip_auteurs","id_auteur=".intval($GLOBALS['visiteur_session']['id_auteur'])." AND gfc_uid=''");
			// else we consider this is an attempt to bind a spip account to a google friend account, we automatically bind the 2 account
			if($id_auteur){
				sql_updateq("spip_auteurs",array('gfc_uid'=> $member_id),"id_auteur=".intval($GLOBALS['visiteur_session']['id_auteur']));
			}
		}
		//if not connected to SPIP and gfc_id not in our system, we create a new SPIP account
		else if(!sql_getfetsel("id_auteur","spip_auteurs","gfc_uid='$member_id'")){
			$declaration = array();
			$declaration['statut'] = '6forum';
			$declaration['nom'] = safehtml($display_name);
			$declaration['login'] = preg_replace("[^a-zA-Z0-9_]", "_", $declaration['nom']);
			$declaration['email'] = $default_email;
			$declaration['gfc_uid'] = $member_id;
			$declaration['en_ligne'] = 'NOW()';
			$n = sql_insertq('spip_auteurs',$declaration);
			$declaration['id_auteur'] = $n;
		}
		login_spip($member_id);
	}
	if($_SESSION["gfc"]["login_redirect"] != '') $url_retour = $_SESSION["gfc"]["login_redirect"];
	else $url_retour = "/";
	header("Location: $url_retour");
	die();
}

function login_spip($gfc_id, $spip_id=''){
	if(intval($gfc_id)){
		$res = sql_fetsel("*","spip_auteurs","gfc_uid='$gfc_id'");
	} 
	elseif($spip_id != '') $res = sql_fetsel("*","spip_auteurs","id_auteur='$spip_id'");
	if ($res){
		$auth_source = 'gfc';
		$res['auth'] = $auth_source;
		
		// create session
		$session = charger_fonction('session','inc');
		$spip_session = $session($res);
		// create cookie
		$_COOKIE['spip_session'] = $spip_session;
		preg_match(',^[^/]*//[^/]*(.*)/$,',
			   url_de_base(),
			   $r);
		include_spip('inc/cookie');
		spip_setcookie('spip_session', $spip_session, time() + 3600 * 24 * 14, $r[1]);
		
		// antentification
		$auth = charger_fonction('auth','inc');
		$auth();
		return true;
	}
	return false;
}

?>