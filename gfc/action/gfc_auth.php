<?

function action_gfc_auth_dist() {
	//ini_set("error_reporting", E_ALL);
	//ini_set("display_errors", 1);
	
	// Set the default timezone since many servers won't have this configured
	//date_default_timezone_set('America/Los_Angeles');
	
	//get osapi info
	$display_name = $member_id = false;
	require_once $_SERVER['DOCUMENT_ROOT']."/plugins/gfc/osapi/osapi.php";
	include_spip('inc/texte');
	include_spip('base/abstract_sql');
	$provider = new osapiFriendConnectProvider();
	$auth = new osapiFCAuth($GLOBALS['gfc']['cookie_value']);
	$osapi = new osapi($provider, $auth);
	$strictMode = true;
	if ($osapi) {
		$request = $osapi->people->get(array('userId'=>'@me', 'groupId'=>'@self'));
		$batch = $osapi->newBatch();
		$batch->add($request, 'me');
		$result = $batch->execute();
		$me = $result['me'];
		if ($me instanceof osapiError) {
			$code = $me->getErrorCode();
      		$message = $me->getErrorMessage();	
			//die("$code - $message");
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
		if (login_spip($member_id)){}
		//elseif member already connected in SPIP...
		elseif($GLOBALS['auteur_session']['id_auteur']!=''){
			//if he already has a gfc_id, we do nothing !!THIS IS BAD, WE NEED TO WORK ON THIS CASE!!
			$res = spip_query("select gfc_uid from spip_auteurs where id_auteur=".sql_quote($GLOBALS['auteur_session']['id_auteur'])." and gfc_uid!='' limit 1");
			if(sql_count($res)==1){}
			// else we consider this is an attempt to bind a spip account to a google friend account, we automatically bind the 2 account
			else spip_query("update spip_auteurs set gfc_uid=".sql_quote($member_id)." where id_auteur=".sql_quote($GLOBALS['auteur_session']['id_auteur']));
		}
		//if not connected to SPIP and gfc_id not in our system, we create a new SPIP account
		else{
			$declaration = array();
			$declaration['statut'] = 'nouveau';
			$declaration['bio'] = 'forum';
			$declaration['nom'] = safehtml($display_name);
			$declaration['login'] = $declaration['url_propre'] = ereg_replace("[^a-zA-Z0-9_]", "_", $display_name);;
			$declaration['email'] = $GLOBALS['gfc']['default_email'];
			$declaration['gfc_uid'] = $member_id;
			$n = sql_insert('spip_auteurs', ('(en_ligne,' .join(',',array_keys($declaration)).')'), ("(NOW()," .join(", ",array_map('sql_quote', $declaration)) .")"));
			$declaration['id_auteur'] = $n;
			
			//then we log user in
			login_spip($member_id);
		}
	}
	if($_SESSION["gfc"]["login_redirect"] != '') $url_retour = $_SESSION["gfc"]["login_redirect"];
	else $url_retour = "/";
	header("Location: $url_retour");
	die();
}

function login_spip($gfc_id, $spip_id=''){
	if($gfc_id != '') $res = spip_query("select * from spip_auteurs where gfc_uid=".sql_quote($gfc_id)." limit 1");
	elseif($spip_id != '') $res = spip_query("select * from spip_auteurs where id_auteur=".sql_quote($spip_id)." limit 1");
	if ($row = sql_fetch($res)){
		$auth_source = 'gfc';
		$row['auth'] = $auth_source;
		// create session
		$session = charger_fonction('session','inc');
		$spip_session = $session($row);
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