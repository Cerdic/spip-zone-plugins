<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *  Plugin SiloSPIP: création de sites en libre service                    *
 *  Fichier include de fonction d'acces au panel (AlternC)                 *
 *                                                                         *
 *  Copyright (c) 2009                                                     *
 *  Daniel Viñar Ulriksen, dani@rezo.net                                   *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

define ('_INSTALL_URL_PANEL', 'https://panel.softwarelibre.gob.bo/admin/');
define ('_INSTALL_USER_ADMIN_PANEL', 'prueba');
define ('_INSTALL_PASS_ADMIN_PANEL', 'prueba123');
define ('_INSTALL_BASE_SITE_PANEL', 'spip');

function silospip_password($longueur = 12)
{   
    $password = "";
    for($i; $i <= $longueur; $i++)
    	$password .=chr(mt_rand(35, 126));
    return $password;
}

// verifier le user du panel AlternC
function silospip_panel_verifier_user($panel_user = '', $panel_pwd = 'test') {

	if ($panel_user == '') $panel_user = $GLOBALS["visiteur_session"]['login'];

	$url_panel =  lire_config('silospip_url_panel/');
	$req_panel = curl_init($url_panel.'login.php');
	curl_setopt($req_panel, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($req_panel, CURLOPT_HEADER, 1);
	curl_setopt($req_panel, CURLOPT_NOBODY, 1);
	curl_setopt($req_panel, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($req_panel, CURLOPT_POST, 1);

	$campos = array('username' => $panel_user, 
			'password' => $panel_pwd 
	);
	curl_setopt($req_panel, CURLOPT_POSTFIELDS, $campos);
	$page = curl_exec($req_panel);
	$header = substr($page,0,curl_getinfo($req_panel,CURLINFO_HEADER_SIZE));
	curl_close($req_panel);
	if (preg_match('/[\r\n]Set-Cookie:\s*session=deleted/',$header))
		return false;
	else
		return true;
}


// creacion del usuario alternc
function silospip_panel_creer_user($admin_panel_user = '', $admin_panel_pwd = '',
						$panel_user = '',
						$panel_pwd = '',
						$panel_user_nom = '',
						$panel_user_prenom = ''	,
						$panel_user_mail = '',
						$panel_user_domaine = '') {

	if ($panel_user == '') $panel_user = $GLOBALS["visiteur_session"]['login'];
	if ($panel_user_mail == '') $panel_user_mail = $GLOBALS["visiteur_session"]['email'];

	$url_panel =  lire_config('silospip_url_panel/');
	$req_panel = curl_init($url_panel.'adm_doadd.php');
	curl_setopt($req_panel, CURLOPT_HEADER, 1);
	curl_setopt($req_panel, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($req_panel, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($req_panel, CURLOPT_POST, 1);

	$campos = array('username' => $admin_panel_user ? $admin_panel_user :_INSTALL_USER_ADMIN_PANEL, 
			'password' => $admin_panel_pwd ? $admin_panel_pwd : _INSTALL_PASS_ADMIN_PANEL, 
			'login' => $panel_user,
			'pass' => $panel_pwd,
			'passconf' => $panel_pwd,
			'canpass' => '1',
			'nom' => $panel_user_nom,
			'prenom' => $panel_user_prenom,
			'nmail' => $panel_user_mail,
			'type' => 'default',
			'create_dom' => '1',
			'create_dom_list' => $panel_user_domaine
	);
	
	curl_setopt($req_panel, CURLOPT_POSTFIELDS, $campos);
	curl_exec($req_panel);
	$affiche = "usuario alternc ".$panel_user." creado correctamente.<br />";
/*
	if ($page = curl_exec($req_panel)) {
		$affiche = "usuario alternc ".$panel_user." creado correctamente.<br />";
		setcookie('mutu_user_panel', "user_cree".md5($site));
	} else {
		$affiche = "falló la creación del usuario alternc ".$panel_user.".<br />";
		setcookie('mutu_user_panel', "user_pas_cree".md5($site));
	}
*/

	// activation base principale	
	curl_setopt($req_panel, CURLOPT_URL, $url_panel.'sql_addmain.php');
	$campos = array('username' => $panel_user, 
		'password' => $panel_pwd,
		'pass' => silospip_password() 
	);
	curl_setopt($req_panel, CURLOPT_POSTFIELDS, $campos);
	curl_exec($req_panel);
	$affiche .= "Base principal del usuario ".$panel_user." activada.<br />";
	curl_close($req_panel);
	return $affiche;
	
}

// creacion del usuario alternc
function silospip_panel_creer_base(
						$panel_user = '',
						$panel_pwd = '',
						$silo_nom = '') {

	if ($panel_user == '') $panel_user = $GLOBALS["visiteur_session"]['login'];
	if ($silo_nom == '') $silo_nom = $panel_user;

	$url_panel =  lire_config('silospip_url_panel/');

	// login user panel
	// il faut le faire a part et conserver les cookies, car la creation de base a un champs 'password'...
	$req_panel = curl_init($url_panel.'login.php');
	$cookies =  _NOM_TEMPORAIRES_INACCESSIBLES . "cookies_silo";
	ecrire_fichier($cookies, 'ok');
	curl_setopt($req_panel, CURLOPT_COOKIEFILE,  $cookies);

	curl_setopt($req_panel, CURLOPT_HEADER, 1);
	curl_setopt($req_panel, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($req_panel, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($req_panel, CURLOPT_POST, 1);

	$campos = array('username' => $panel_user, 
		'password' => $panel_pwd, 
	);
	curl_setopt($req_panel, CURLOPT_POSTFIELDS, $campos);
	curl_exec($req_panel);
/*
	if (curl_exec($req_panel)) {
		$affiche = "usuario alternc ".$panel_user." logueado correctamente.<br />.";
		setcookie('mutu_user_panel', "user_logue".md5($site));
	} else {
		$affiche = "falló la conexion del usuario alternc ".$panel_user.".<br />";
		setcookie('mutu_user_panel', "user_pas_cree".md5($site));
	}
*/
	
	//	creation user sql 
	curl_setopt($req_panel, CURLOPT_URL, $url_panel.'sql_users_doadd.php');
	$campos = array('usern' => $ext_sql = $silo_nom ? $silo_nom : _INSTALL_BASE_SITE_PANEL,
 							'password' => $pwd = silospip_password(),
							'passconf' => $pwd
	);	
	curl_setopt($req_panel, CURLOPT_POSTFIELDS, $campos);
	curl_exec($req_panel);
	$affiche .= "Usuario mysql ".$panel_user."_spip del usuario alternc ".$panel_user." creado<br /><br />";
/*
	if ($page = curl_exec($req_panel))
		$affiche .= "Usuario mysql ".$panel_user."_spip del usuario alternc ".$panel_user." creado<br /><br />";
*/

	// creacion de una base de datos mysql asociada al nuevo usuario alternc
	curl_setopt($req_panel, CURLOPT_URL, $url_panel.'sql_doadd.php');
	$campos = array('dbn' => $ext_sql
	);
	curl_setopt($req_panel, CURLOPT_POSTFIELDS, $campos);
	curl_exec($req_panel);
	$affiche .= "Base de datos mysql ".$panel_user."_spip del usuario alternc ".$panel_user." creado<br /><br />";
/*	if ($page = curl_exec($req_panel))
		$affiche .= "Base de datos mysql ".$login."_spip del usuario alternc ".$login." creado<br /><br />";
		*/

	// derechos del usuario a la base 
	curl_setopt($req_panel, CURLOPT_URL, $url_panel.'sql_users_dorights.php');
	$campos = array('id' => $ext_sql,
		$ext_sql.'_select' => 'on',
		$ext_sql.'_insert' => 'on',
		$ext_sql.'_update' => 'on',
		$ext_sql.'_delete' => 'on',
		$ext_sql.'_create' => 'on',
		$ext_sql.'_drop' => 'on',
		$ext_sql.'_references' => 'on',
		$ext_sql.'_index' => 'on',
		$ext_sql.'_alter' => 'on',
		$ext_sql.'_create_tmp' => 'on',
		$ext_sql.'_lock' => 'on'
	);
	curl_setopt($req_panel, CURLOPT_POSTFIELDS, $campos);
	curl_exec($req_panel);
	$affiche .= "derechos sobre la base de datos mysql ".$panel_user."_spip affectados al usuario mysql<br /><br />";
/*
	if ($page = curl_exec($req_panel))
		$affiche .= "derechos sobre la base de datos mysql ".$panel_user."_spip affectados al usuario mysql<br /><br />";
*/
	curl_close($req_panel);
	return $affiche;
}
	
?>
